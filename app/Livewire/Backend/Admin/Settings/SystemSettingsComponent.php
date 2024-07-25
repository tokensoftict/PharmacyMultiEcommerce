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
            ];
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
        $this->alert( 'success',
            'Settings has been saved successfully!"',
        );
        //$this->dispatch("success", ['modal'=> null, "options" =>['title'=>'System Settings', 'message'=>"Settings has been saved successfully!"]]);
    }
}
