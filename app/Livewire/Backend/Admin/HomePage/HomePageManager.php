<?php

namespace App\Livewire\Backend\Admin\HomePage;

use App\Models\App;
use App\Models\HomePageComponent;
use Livewire\Component;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class HomePageManager extends Component
{
    use WithPagination;

    public $selectedApp = 6; // Default to Supermarket
    public $component_name, $type, $component_id, $label, $limit = 15, $see_all_link, $sort_order = 0, $status = true;
    public $editingId = null;
    public $isModalOpen = false;

    protected $rules = [
        'selectedApp' => 'required|integer',
        'component_name' => 'required|string',
        'type' => 'required|string',
        'component_id' => 'nullable|string',
        'label' => 'nullable|string',
        'limit' => 'required|integer',
        'see_all_link' => 'nullable|string',
        'sort_order' => 'required|integer',
        'status' => 'required|boolean',
    ];

    public function render()
    {
        $components = HomePageComponent::where('app_id', $this->selectedApp)
            ->orderBy('sort_order', 'asc')
            ->paginate(20);

        $apps = App::all();

        return view('livewire.backend.admin.home-page.home-page-manager', [
            'components' => $components,
            'apps' => $apps
        ])->layout('layout.app');
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->component_name = '';
        $this->type = '';
        $this->component_id = '';
        $this->label = '';
        $this->limit = 15;
        $this->see_all_link = '';
        $this->sort_order = 0;
        $this->status = true;
        $this->editingId = null;
    }

    public function store()
    {
        $this->validate();

        HomePageComponent::updateOrCreate(['id' => $this->editingId], [
            'app_id' => $this->selectedApp,
            'component_name' => $this->component_name,
            'type' => $this->type,
            'component_id' => $this->component_id,
            'label' => $this->label,
            'limit' => $this->limit,
            'see_all_link' => $this->see_all_link,
            'sort_order' => $this->sort_order,
            'status' => $this->status,
        ]);

        LivewireAlert::title('Success')
            ->text($this->editingId ? 'Component updated successfully.' : 'Component created successfully.')
            ->show();

        $this->closeModal();
    }

    public function edit($id)
    {
        $component = HomePageComponent::findOrFail($id);
        $this->editingId = $id;
        $this->selectedApp = $component->app_id;
        $this->component_name = $component->component_name;
        $this->type = $component->type;
        $this->component_id = $component->component_id;
        $this->label = $component->label;
        $this->limit = $component->limit;
        $this->see_all_link = $component->see_all_link;
        $this->sort_order = $component->sort_order;
        $this->status = $component->status;

        $this->openModal();
    }

    public function delete($id)
    {
        HomePageComponent::find($id)->delete();
        LivewireAlert::title('Success')
            ->text('Component deleted successfully.')
            ->show();
    }
}
