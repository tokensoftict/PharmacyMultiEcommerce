<?php

namespace App\Livewire\Backend\Admin\HomePage;

use App\Models\App;
use App\Models\Classification;
use App\Models\HomePageComponent;
use App\Models\Manufacturer;
use App\Models\Productcategory;
use App\Models\Slider;
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

    public $itemsForSelect = [];
    public $availableTypes = [];

    protected $componentTypeMapping = [
        'Horizontal_List' => [
            'classifications' => 'Classifications',
            'manufacturers' => 'Manufacturers',
            'productcategories' => 'Product Categories',
            'new_arrivals' => 'New Arrivals',
            'specialOffers' => 'Special Offers',
        ],
        'ImageSlider' => [
            'ImageSlider' => 'Image Slider',
        ],
        'topBrands' => [
            'manufacturers' => 'Manufacturers',
        ],
        'FlashDeals' => [
            'classifications' => 'Classifications',
            'manufacturers' => 'Manufacturers',
            'productcategories' => 'Product Categories',
            'lowestClassifications' => 'Lowest Classifications',
            'mixed' => 'Mixed (Class/Mfr/Cat)',
        ],
    ];

    protected $rules = [
        'selectedApp' => 'required|integer',
        'component_name' => 'required|string',
        'type' => 'nullable|string',
        'component_id' => 'nullable', // Can be string or array
        'label' => 'nullable|string',
        'limit' => 'required|integer',
        'see_all_link' => 'nullable|string',
        'sort_order' => 'required|integer',
        'status' => 'required|boolean',
    ];

    public function updatedComponentName($value)
    {
        $this->availableTypes = $this->componentTypeMapping[$value] ?? [];
        $this->type = '';
        $this->component_id = '';
        $this->itemsForSelect = [];

        // If only one automatic type, set it
        if (count($this->availableTypes) === 1) {
            $this->type = array_key_first($this->availableTypes);
            $this->updatedType($this->type);
        }
    }

    public function updatedType($value)
    {
        $isMultiple = in_array($this->component_name, ['topBrands', 'FlashDeals', 'ImageSlider']);
        $this->component_id = $isMultiple ? [] : '';
        $this->itemsForSelect = [];

        if ($value === 'classifications' || $value === 'lowestClassifications') {
            $this->itemsForSelect = Classification::where('status', true)->orderBy('name', 'asc')->get(['id', 'name'])->toArray();
        } elseif ($value === 'manufacturers') {
            $this->itemsForSelect = Manufacturer::where('status', true)->orderBy('name', 'asc')->get(['id', 'name'])->toArray();
        } elseif ($value === 'productcategories') {
            $this->itemsForSelect = Productcategory::where('status', true)->orderBy('name', 'asc')->get(['id', 'name'])->toArray();
        } elseif ($value === 'mixed') {
            $this->itemsForSelect = $this->fetchMixedItems();
        }
    }

    private function fetchMixedItems()
    {
        $classifications = Classification::where('status', true)->orderBy('name', 'asc')->get(['id', 'name'])->map(fn($item) => ['id' => 'classification:'.$item->id, 'name' => 'Class: '.$item->name]);
        $manufacturers = Manufacturer::where('status', true)->orderBy('name', 'asc')->get(['id', 'name'])->map(fn($item) => ['id' => 'manufacturer:'.$item->id, 'name' => 'Mfr: '.$item->name]);
        $categories = Productcategory::where('status', true)->orderBy('name', 'asc')->get(['id', 'name'])->map(fn($item) => ['id' => 'productcategory:'.$item->id, 'name' => 'Cat: '.$item->name]);
        
        return $classifications->concat($manufacturers)->concat($categories)->toArray();
    }

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
        $this->itemsForSelect = [];
        $this->availableTypes = [];
    }

    public function store()
    {
        $this->validate();

        $comp_id = is_array($this->component_id) ? implode(',', $this->component_id) : $this->component_id;

        HomePageComponent::updateOrCreate(['id' => $this->editingId], [
            'app_id' => $this->selectedApp,
            'component_name' => $this->component_name,
            'type' => $this->type,
            'component_id' => $comp_id,
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
        
        $isMultiple = in_array($this->component_name, ['topBrands', 'FlashDeals', 'ImageSlider']);
        
        $this->component_id = $isMultiple 
            ? ($component->component_id ? explode(',', $component->component_id) : [])
            : $component->component_id;

        $this->label = $component->label;
        $this->limit = $component->limit;
        $this->see_all_link = $component->see_all_link;
        $this->sort_order = $component->sort_order;
        $this->status = $component->status;

        $this->availableTypes = $this->componentTypeMapping[$this->component_name] ?? [];

        // Populate itemsForSelect if needed
        if ($this->type === 'classifications' || $this->type === 'lowestClassifications') {
            $this->itemsForSelect = Classification::where('status', true)->orderBy('name', 'asc')->get(['id', 'name'])->toArray();
        } elseif ($this->type === 'manufacturers') {
            $this->itemsForSelect = Manufacturer::where('status', true)->orderBy('name', 'asc')->get(['id', 'name'])->toArray();
        } elseif ($this->type === 'productcategories') {
            $this->itemsForSelect = Productcategory::where('status', true)->orderBy('name', 'asc')->get(['id', 'name'])->toArray();
        } elseif ($this->type === 'ImageSlider') {
            $this->itemsForSelect = Slider::where('status', true)->orderBy('title', 'asc')->get(['id', 'title as name'])->toArray();
        } elseif ($this->type === 'mixed') {
            $this->itemsForSelect = $this->fetchMixedItems();
        }

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
