<?php

namespace App\Services\Api\MedReminder;

use App\Classes\ApplicationEnvironment;
use App\Classes\Settings;
use App\Enums\PushNotificationAction;
use App\Http\Resources\Api\MedReminder\MedReminderResource;
use App\Jobs\TriggerGenerateMedReminderDiscount;
use App\Models\MedReminder;
use App\Models\MedReminderSchedule;
use App\Models\User;
use App\Services\Utilities\PushNotificationService;
use Carbon\Carbon;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use NumberFormatter;

class MedReminderService
{
    public MedReminder $medReminder;
    public PushNotificationService $pushNotificationService;
    public MedReminderSchedule $medReminderSchedule;

    public static array $dosageForm = [
        'Tablet' => 'mg',
        'Syrup' => 'mL',
        'Cream' => 'mg',
        'Eye drops' => 'mL',
        'Ear drops' => 'mL',
        'Injection' => 'mL',
        'Capsule' => 'mg',
        'Ointments' => 'mg',
        'Gels' => 'mg',
        'Lotions' => 'mg',
        'Powders' => 'mg',
        'Chewable' => 'mg',
        'Lozenges' => 'mL',
        'Infusions' => 'mL',
    ];


    public static array $repeatDuration = [
        'Daily' => "1 day",
        'Every Two Days' => "2 days",
        'Weekly' => "1 week",
    ];

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
    {   $every = $data['interval'] ?? NULL;
        $interval = $data['every'] ?? NULL;
        $data['date_create'] = date('Y-m-d');
        $data['user_id'] = request()->user()->id;

        $data['normal_schedules'] =  json_decode($data['normal_schedules'], true) ?? NULL;
        $data['interval'] = $interval;
        $data['every'] = $every;

        $data['dosage_form'] = self::$dosageForm[$data['dosage_form']] ?? NULL;
        return $data;
    }

    /**
     * @param array $data
     * @return MedReminder
     * @throws \Throwable
     */
    public final function create(array $data) : MedReminder
    {
        return DB::transaction(function () use ($data) {
            $medReminder = $this->medReminder->create($this->prepareMedReminderData($data));
            $this->createSchedules($medReminder);
            return $medReminder;
        });
    }

    /**
     * @param MedReminder $medReminder
     * @param array $data
     * @return MedReminder
     * @throws \Throwable
     */
    public final function update(MedReminder $medReminder, array $data) : MedReminder
    {
       return DB::transaction(function () use ($medReminder, $data) {
           $medReminder->update($this->prepareMedReminderData($data));
           $medReminder->med_reminder_schedules()->delete();
           $this->createSchedules($medReminder);
           return $medReminder->fresh();
       });
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
            $schedules = $this->normalScheduleGenerator($medReminder);
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
        $totalDosage = (float)$medReminder->total_dosage_in_package - (float)$medReminder->total_dosage_taken;
        $dosage = (float)$medReminder->dosage;
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
    private function normalScheduleGeneratorDEPRECATED(MedReminder $medReminder) : array
    {
        $startDate = date('Y-m-d', strtotime($medReminder->start_date_time));
        $totalDosage = (float)$medReminder->total_dosage_in_package;
        $dosage = (float)$medReminder->dosage;

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

        for ($i = 0; $i <= $numberOfTimes; $i++) {
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
     * @param MedReminder $medReminder
     * @return array
     */
    private function normalScheduleGenerator(MedReminder $medReminder) : array
    {
        $totalDosage = (float)$medReminder->total_dosage_in_package;
        $dosage = (float)$medReminder->dosage;
        $interval = $medReminder->interval.' '.$medReminder->every;
        $startDate = date('Y-m-d', strtotime($medReminder->start_date_time));

        $schedules = [];
        $numberOfTimes = (int)ceil($totalDosage/$dosage);

        if(is_string($medReminder->normal_schedules)) {
            $medReminder->normal_schedules = json_decode($medReminder->normal_schedules, true);
        }

        $frequency = count($medReminder->normal_schedules);

        $times = 1;
        $reStartDate = $startDate;
        while ($times <= ((int)ceil($numberOfTimes/ $frequency))) {
            foreach ($medReminder->normal_schedules as $schedule) {
                $schedules[key($schedule)." - Date : ".(new Carbon($reStartDate))->format('F jS, Y')] = date('Y-m-d H:i:s', strtotime($reStartDate." ".$schedule[key($schedule)]));
            }
            $times++;
            $reStartDate = date('Y-m-d', strtotime("+".$interval, strtotime($reStartDate)));
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


        $data = (new MedReminderResource($medReminder))->toArray(request());

        $notifications = [
            ["title" => "New Medication Reminder Added", "body" => "A new medication schedule has been created for you. Review and accept the reminder now!"],
            ["title" => "Your Medication Plan is Ready", "body" => "A new med reminder has been created for you. Check the details and confirm your schedule."],
            ["title" => "Medication Reminder Assigned", "body" => "A medication plan has been set for you. Tap to review and accept it."],
            ["title" => "New Med Reminder Waiting for Approval", "body" => "A medication schedule has been added for you. Review it and start tracking your meds!"],
            ["title" => "A New Scheduled Your Medication", "body" => "A new medication plan has set up a new medication reminder. Check it out and accept the schedule."],
            ["title" => "Important: Medication Schedule Created", "body" => "A new medication plan has been set for you. review and confirm your reminders."],
            ["title" => "Stay on Track! New Medication Reminder", "body" => "A new medication has set up a medication reminder for you. Open the app to view and approve it."],
            ["title" => "Your Medication Routine Just Got Updated", "body" => "A new med reminder has been added to your schedule. Tap to check and accept it."],
            ["title" => "Action Needed: Medication Schedule Added", "body" => "A new medication plan has assigned a new medication reminder. Review and activate it now."],
            ["title" => "A New Prescription Reminder Awaits You", "body" => "A doctor has created a medication schedule for you. Open the app to review and accept it."]
        ];

        $randomNotification = $notifications[array_rand($notifications)];
        $randomNotification['status'] = 'DRAFT';
        $randomNotification['app_id'] = ApplicationEnvironment::$id;


        $this->pushNotificationService
            ->setApplicationEnvironment(ApplicationEnvironment::$id)
            ->createNotification($randomNotification)
            ->setAction(PushNotificationAction::APPROVE_MED_REMINDER_SCHEDULES)
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
     * @return Collection
     */
    public final function listMedReminders(User $user) : Collection
    {
        return MedReminder::query()->with(['stock'])->where('user_id', $user->id)->orderBy('id', 'desc')->get();
    }


    /**
     * @param User $user
     * @return Collection
     */
    public final function getMedRemindersLocalNotification(User $user) : Collection
    {
       return  MedReminderSchedule::query()
            ->whereHas('med_reminder', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where(function ($query) {
                $query->orWhere("scheduled_at", ">", Carbon::now())
                    ->orWhere("snoozed_at", ">", Carbon::now());
            })
            ->whereIn('status', ['Pending', 'Cancelled'])->get();
    }


    /**
     * @param User $user
     * @return Collection
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public final function listSchedules(User $user) : Collection
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

        if(request()->has('filter') && request()->get('filter') == 'custom-date') {
            $medReminderSchedules->where(function ($query) {
                $query->orWhereBetween('scheduled_at', [(new Carbon(request()->get('custom-date')))->startOfDay()->toDateTimeString(), (new Carbon(request()->get('custom-date')))->endOfDay()->toDateTimeString()])
                    ->orWhereBetween('snoozed_at', [(new Carbon(request()->get('custom-date')))->startOfDay()->toDateTimeString(), (new Carbon(request()->get('custom-date')))->endOfDay()->toDateTimeString()]);
            });
        }

        if(request()->has('filter') && request()->get('filter') == 'reminder-id') {
            $medReminderSchedules->where("med_reminder_id", request()->get('reminder-id'));
        }

        return $medReminderSchedules ->orderBy('scheduled_at')->get();
    }


    /**
     * @param MedReminderSchedule $medReminderSchedule
     * @param array $data
     * @return MedReminderSchedule
     * @throws \Throwable
     */
    public final function updateSchedule(MedReminderSchedule $medReminderSchedule, array $data) : MedReminderSchedule
    {
        return DB::transaction(function () use ($medReminderSchedule, $data) {
            if(\Arr::has($data, 'snoozed_at')) {
                $snoozed_at = date('Y-m-d H:i:s', strtotime($data['snoozed_at']));
                $medReminderSchedule->snoozed_at = (new Carbon($snoozed_at))->toDateTimeString();
            }

            if(\Arr::has($data, 'status') and !\Arr::has($data, 'snoozed_at')) {
                $medReminderSchedule->status = $data['status'];

                if($data['status'] == 'Completed') {
                    $medReminderSchedule->med_reminder->increment('total_dosage_taken', $medReminderSchedule->med_reminder->dosage);
                    $medReminderSchedule->med_reminder->fresh();
                    $settings = app(Settings::class);
                    if(
                        $medReminderSchedule->med_reminder->type === "CONTINUES" &&
                        ($medReminderSchedule->med_reminder->total_dosage_in_package - $medReminderSchedule->med_reminder->total_dosage_taken) <= (int)$settings->get("dosage_trigger") &&
                        !$medReminderSchedule->med_reminder->is_discount_generated
                    ) {
                        dispatch(new TriggerGenerateMedReminderDiscount($medReminderSchedule->med_reminder));
                    }
                }

            }

            $medReminderSchedule->save();
            return $medReminderSchedule->refresh();
        });
    }

}
