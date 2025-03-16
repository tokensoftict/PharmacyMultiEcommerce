<?php

namespace App\Services\Api\MedReminder;

use App\Classes\ApplicationEnvironment;
use App\Models\MedReminder;
use App\Models\MedReminderSchedule;
use App\Models\User;
use App\Services\Utilities\PushNotificationService;
use Illuminate\Pagination\LengthAwarePaginator;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use NumberFormatter;

class MedReminderService
{
    public MedReminder $medReminder;
    public PushNotificationService $pushNotificationService;
    public MedReminderSchedule $medReminderSchedule;
    public function __construct()
    {
        $this->medReminder =new MedReminder();
        $this->medReminderSchedule = new MedReminderSchedule();
        $this->pushNotificationService = new PushNotificationService();
    }

    /**
     * @param array $data
     * @return array
     */
    public final function prepareMedReminderData(array $data) : array
    {
        $data['date_create'] = date('Y-m-d');
        $data['user_id'] = request()->user()->id;
        if($data['use_interval'] ==  "0"){
            $data['every'] = NULL;
            $data['interval'] =NULL;
        } else {
            $data['normal_schedules'] = NULL;
        }
        return $data;
    }

    /**
     * @param array $data
     * @return MedReminder
     */
    public final function create(array $data) : MedReminder
    {
        $medReminder = $this->medReminder->create($this->prepareMedReminderData($data));
        $this->createSchedules($medReminder);
        return $medReminder;
    }

    /**
     * @param MedReminder $medReminder
     * @param array $data
     * @return MedReminder
     */
    public final function update(MedReminder $medReminder, array $data) : MedReminder
    {
        $medReminder->update($this->prepareMedReminderData($data));
        $medReminder->med_reminder_schedules()->delete();
        $this->createSchedules($medReminder);
        return $medReminder->fresh();
    }

    /**
     * @param MedReminder $medReminder
     * @return bool
     */
    public final function delete(MedReminder $medReminder) : bool
    {
        return $medReminder->delete();
    }

    /**
     * @param MedReminder $medReminder
     * @return MedReminder
     */
    public final function createSchedules(MedReminder $medReminder) : MedReminder
    {
        if($medReminder->use_interval == "1") {
            $schedules = $this->intervalSchedulesGenerator($medReminder);
        } else {
            $schedules = $this->normalScheduleGenerator($medReminder);
        }

        $newSchedules = [];

        foreach($schedules as $title => $schedule) {
            $newSchedules[] = new MedReminderSchedule([
                'title' => $title,
                'scheduled_at' => $schedule
            ]);
        }

        $medReminder->med_reminder_schedules()->saveMany($newSchedules);
        return $medReminder;
    }


    /**
     * @param MedReminder $medReminder
     * @return array
     */
    private function intervalSchedulesGenerator(MedReminder $medReminder) : array
    {
        $startDate = $medReminder->start_date_time;
        $totalDosage = $medReminder->total_dosage_in_package - $medReminder->total_dosage_taken;
        $dosage = $medReminder->dosage;
        $interval = $medReminder->interval.' '.$medReminder->every;

        $schedules = [];
        $numberOfTimes = ceil($totalDosage/$dosage);
        for ($i = 1; $i <= $numberOfTimes; $i++) {
            $ordinal = (new NumberFormatter('en_US', NumberFormatter::ORDINAL))->format($i);
            $schedules[$ordinal." Dosage"] =  date('Y-m-d H:i:s', strtotime('+'.$interval, strtotime($startDate)));
            $startDate = date('Y-m-d H:i:s', strtotime('+'.$interval, strtotime($startDate)));
        }

        return $schedules;
    }


    /**
     * @param MedReminder $medReminder
     * @return array
     */
    private function normalScheduleGenerator(MedReminder $medReminder) : array
    {
        $startDate = date('Y-m-d', strtotime($medReminder->start_date_time));
        $totalDosage = $medReminder->total_dosage_in_package;
        $dosage = $medReminder->dosage;

        $schedules = [];
        $numberOfTimes = ceil($totalDosage/$dosage);
        $dayTimes = ['Morning', 'Afternoon', 'Evening', 'Mid-Night'];

        if(is_string($medReminder->normal_schedules)) {
            $medReminder->normal_schedules = json_decode($medReminder->normal_schedules, true);
        }
        $numberOfTimes = (int)$numberOfTimes;

        $timesToTakePerDay = 0;

        foreach ($dayTimes as $day) {
            if (isset($medReminder->normal_schedules[$day]) and !empty($medReminder->normal_schedules[$day]) and $medReminder->normal_schedules[$day] != "") {

                $timesToTakePerDay++;
            }
        }

        $numberOfTimes =  (int)ceil($numberOfTimes / $timesToTakePerDay);

        for ($i = 1; $i <= $numberOfTimes; $i++) {
            foreach ($dayTimes as $day) {
                if(isset($medReminder->normal_schedules[$day]) and !empty($medReminder->normal_schedules[$day]) and $medReminder->normal_schedules[$day]!="") {
                    $daytime = $day;
                    $time = $medReminder->normal_schedules[$day];
                    $reStartDate = date('Y-m-d', strtotime('+' . $i . " days", strtotime($startDate)));
                    $schedules[$daytime.' Dosage '.date('Y-m-d H:i:s', strtotime($reStartDate . " " . $time))] = date('Y-m-d H:i:s', strtotime($reStartDate . " " . $time));
                }
            }
        }

        return $schedules;
    }

    /**
     * @param MedReminder|int $medReminder
     * @return bool
     */
    public final function pushSchedulesToUsersPhone(MedReminder | int $medReminder) : bool
    {
        if(!$medReminder instanceof MedReminder) {
            $medReminder = MedReminder::find($medReminder);
        }

        $schedules = [];
        foreach ($medReminder->med_reminder_schedules as $schedule) {
            $schedules[] = [
                'id' => $schedule->id,
                'title' => $schedule->title,
                'schedule_time' => carbonize($schedule->scheduled_at)->toDateTimeString()
            ];
        }

        $data = [
            'schedules' => $schedules,
        ];

        $notifications = [
            ["title" => "New Medication Reminder Added", "body" => "A new medication schedule has been created for you. Review and accept the reminder now!"],
            ["title" => "Your Medication Plan is Ready", "body" => "A new med reminder has been created for you. Check the details and confirm your schedule."],
            ["title" => "Medication Reminder Assigned", "body" => "A medication plan has been set for you. Tap to review and accept it."],
            ["title" => "New Med Reminder Waiting for Approval", "body" => "A medication schedule has been added for you. Review it and start tracking your meds!"],
            ["title" => "Doctor Scheduled Your Medication", "body" => "Your doctor has set up a new medication reminder. Check it out and accept the schedule."],
            ["title" => "Important: Medication Schedule Created", "body" => "A new medication plan has been set for you. Tap to review and confirm your reminders."],
            ["title" => "Stay on Track! New Medication Reminder", "body" => "A doctor has set up a medication reminder for you. Open the app to view and approve it."],
            ["title" => "Your Medication Routine Just Got Updated", "body" => "A new med reminder has been added to your schedule. Tap to check and accept it."],
            ["title" => "Action Needed: Medication Schedule Added", "body" => "Your doctor has assigned a new medication reminder. Review and activate it now."],
            ["title" => "A New Prescription Reminder Awaits You", "body" => "A doctor has created a medication schedule for you. Open the app to review and accept it."]
        ];

        $randomNotification = $notifications[array_rand($notifications)];
        $randomNotification['status'] = 'DRAFT';
        $randomNotification['app_id'] = ApplicationEnvironment::$id;


        $this->pushNotificationService
            ->setApplicationEnvironment(ApplicationEnvironment::$id)
            ->createNotification($randomNotification)
            ->setAction("MED-REMINDER-SCHEDULES")
            ->setPayload($data)
            ->setUserCustomer($medReminder->user_id)
            ->approve()
            ->send();


        LivewireAlert::title('Success')
            ->text('Med Reminders have schedule successfully!')
            ->success()
            ->withConfirmButton('Ok')
            ->timer(3000)
            ->show();

        return true;
    }


    /**
     * @param User $user
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public final function listMedReminders(User $user) : LengthAwarePaginator
    {
        return MedReminder::query()->where('user_id', $user->id)->orderBy('id', 'desc')->paginate(config('app.pagination_limit'));
    }


    /**
     * @param User $user
     * @return LengthAwarePaginator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public final function listSchedules(User $user) : LengthAwarePaginator
    {
        $medReminderSchedules =  MedReminderSchedule::query()
            ->whereHas('med_reminder', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });

        if(request()->has('filter') && request()->get('filter') == 'today-history') {
            $medReminderSchedules->where(function ($query) {
               $query->orWhereBetween('scheduled_at', [now()->startOfDay()->toDateTimeString(), now()->endOfDay()->toDateTimeString()])
               ->orWhereBetween('snoozed_at', [now()->startOfDay()->toDateTimeString(), now()->endOfDay()->toDateTimeString()]);
            });
        }

        $medReminderSchedules ->orderBy('scheduled_at')->paginate(config('app.pagination_limit'));

        return $medReminderSchedules->paginate(config('app.pagination_limit'));
    }

}
