<?php

namespace App\Livewire\Backend\Admin\Settings;

use App\Classes\PermissionAttribute;
use App\Classes\Settings;
use Livewire\Component;
use Livewire\WithFileUploads;

class SystemSettingsComponent extends Component
{
    use WithFileUploads;

    public array $store;

    private Settings $settings;

    public bool $logoSelected = false;

    /**
     * @param Settings $settings
     * @return void
     */
    public function boot(Settings $settings)
    {
        $this->settings = $settings;
    }


    /**
     * @return void
     */
    public function mount()
    {
        $this->store = $this->settings->all();
        if(count($this->store)  === 0)
        {
            $this->store =  [
                'name' => NULL,
                'tax' => NULL,
                'new_arrival_count_trigger' => NULL,
                'first_address' => NULL,
                'second_address' => NULL,
                'contact_number' => NULL,
                'delivery_tariff' => NULL,
                'logo' => 'placholder.jpg',
                'footer_notes' => NULL,
                // ── Referral Bonus Defaults ──────────────────────────────────
                'retail_referral_bonus'    => 10,
                'wholesale_referral_bonus' => 20,
                // ── Mobile App Contact & Store Location Defaults ─────────────
                'store_title'              => '📍 Our Store',
                'contact_address'          => 'Muritala Muhamed Way, opposite Forte Oil, Ilorin 240101, Kwara State',
                'contact_phone'            => '+2349095950088',
                'contact_email'            => 'info@generaldrugcentre.com',
                'contact_whatsapp'         => '2349095950088',
                'store_latitude'           => 8.4915319,
                'store_longitude'          => 4.566907,
                'store_latitude_delta'     => 0.005,
                'store_longitude_delta'    => 0.005,
            ];
        } else {
            // Ensure referral bonus keys exist even on existing settings stores
            $this->store['retail_referral_bonus']    = $this->store['retail_referral_bonus'] ?? 10;
            $this->store['wholesale_referral_bonus'] = $this->store['wholesale_referral_bonus'] ?? 20;
            // Ensure contact & store location keys exist on existing settings stores
            $this->store['store_title']              = $this->store['store_title'] ?? '📍 Our Store';
            $this->store['contact_address']          = $this->store['contact_address'] ?? ($this->store['first_address'] ?: 'Muritala Muhamed Way, opposite Forte Oil, Ilorin 240101, Kwara State');
            $this->store['contact_phone']            = $this->store['contact_phone'] ?? ($this->store['contact_number'] ?: '+2349095950088');
            $this->store['contact_email']            = $this->store['contact_email'] ?? 'info@generaldrugcentre.com';
            $this->store['contact_whatsapp']         = $this->store['contact_whatsapp'] ?? '2349095950088';
            $this->store['store_latitude']           = $this->store['store_latitude'] ?? 8.4915319;
            $this->store['store_longitude']          = $this->store['store_longitude'] ?? 4.566907;
            $this->store['store_latitude_delta']     = $this->store['store_latitude_delta'] ?? 0.005;
            $this->store['store_longitude_delta']    = $this->store['store_longitude_delta'] ?? 0.005;
        }
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function render()
    {
        return view('livewire.pages.backend.admin.settings.system-settings-component');
    }

    #[PermissionAttribute('View', 'view', 'backend.admin.settings.system_settings')]
    public function view()
    {
    }


    /**
     * @return void
     */
    #[PermissionAttribute('Save', 'save', 'backend.admin.settings.save_system_settings')]
    public function update(){

        $validation = Settings::$validation;

        if(isset($this->store['logo']) && !is_string($this->store['logo']))
        {
            $validation['store.logo'] = 'mimes:jpeg,jpg|required|max:10000';
        }

        $this->validate($validation);

        if(isset($this->store['logo'])  && !is_string($this->store['logo'])) {
            $this->store['logo'] = $this->store['logo']->store('logo', 'real_public');
        }

        $this->settings->put($this->store);
        \Illuminate\Support\Facades\Cache::forget(\App\Http\Controllers\Api\General\ContactInfoController::CACHE_KEY);
        $this->alert( 'success',
            'Settings has been saved successfully!"',
        );
        //$this->dispatch("success", ['modal'=> null, "options" =>['title'=>'System Settings', 'message'=>"Settings has been saved successfully!"]]);
    }
}
