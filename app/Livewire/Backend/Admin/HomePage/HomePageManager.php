<?php

namespace App\Livewire\Backend\Admin\HomePage;

use App\Classes\ApplicationEnvironment;
use App\Models\App;
use App\Models\Stock;
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

    public $selectedApp, $storeName;
    public $component_name, $type, $component_id, $label, $limit = 15, $see_all_link, $sort_order = 0, $status = true;
    public $editingId = null;
    public $isModalOpen = false;

    public function mount()
    {
        $this->selectedApp = (int) ApplicationEnvironment::$id;
        $this->storeName = ApplicationEnvironment::$name;
    }

    public $itemsForSelect = [];
    public $availableTypes = [];

    public $componentDisplayNames = [
        'Horizontal_List' => 'Scrolling Product Gallery',
        'ImageSlider' => 'Hero Banner Carousel',
        'topBrands' => 'Featured Brands Grid',
        'FlashDeals' => 'Daily Hot Deals',
        'PromoCarousel' => 'Promotion Spotlight',
    ];

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

        return view('livewire.backend.admin.home-page.home-page-manager', [
            'components' => $components,
        ])->layout('layout.app');
    }

    public function openModal()
    {
        $this->resetInputFields();
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
        if (!$this->selectedApp) {
            $this->selectedApp = (int) ApplicationEnvironment::$id;
        }

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
        $component = HomePageComponent::find($id);
        if ($component) {
            $component->delete();
            LivewireAlert::title('Success')
                ->text('Component deleted successfully.')
                ->show();
        } else {
            LivewireAlert::title('Error')
                ->text('Component not found.')
                ->show();
        }
    }

    public function getComponentPreviewItems($compObj = null)
    {
        $type = $compObj ? $compObj->type : $this->type;
        $component_id = $compObj ? $compObj->component_id : $this->component_id;
        $component_name = $compObj ? $compObj->component_name : $this->component_name;

        if (!$type) return [];

        $ids = is_array($component_id) ? $component_id : (empty($component_id) ? [] : explode(',', $component_id));
        $ids = array_filter($ids);

        if (empty($ids) && !$compObj) {
            return [['name' => 'Sample Product 1'], ['name' => 'Sample Product 2']];
        }

        // If it's a product gallery or flash deals, show actual products
        if ($component_name === 'Horizontal_List' || $component_name === 'FlashDeals') {
            $query = Stock::query();
            
            if ($type === 'classifications') {
                $query->whereIn('classification_id', $ids);
            } elseif ($type === 'manufacturers') {
                $query->whereIn('manufacturer_id', $ids);
            } elseif ($type === 'productcategories') {
                $query->whereIn('productcategory_id', $ids);
            } elseif ($type === 'lowestClassifications') {
                 $query->whereIn('classification_id', $ids);
            } elseif ($type === 'mixed') {
                $classIds = []; $mfrIds = []; $catIds = [];
                foreach($ids as $idStr) {
                    $parts = explode(':', $idStr);
                    if(count($parts) == 2) {
                        if($parts[0] == 'classification') $classIds[] = $parts[1];
                        elseif($parts[0] == 'manufacturer') $mfrIds[] = $parts[1];
                        elseif($parts[0] == 'productcategory') $catIds[] = $parts[1];
                    }
                }
                $query->where(function($q) use ($classIds, $mfrIds, $catIds) {
                    if(!empty($classIds)) $q->orWhereIn('classification_id', $classIds);
                    if(!empty($mfrIds)) $q->orWhereIn('manufacturer_id', $mfrIds);
                    if(!empty($catIds)) $q->orWhereIn('productcategory_id', $catIds);
                });
            }

            $products = $query->limit(5)->get(['id', 'name'])->toArray();
            return !empty($products) ? $products : [['name' => 'No products found in this source']];
        }

        if ($type === 'classifications' || $type === 'lowestClassifications') {
            return Classification::whereIn('id', $ids)->limit(5)->get(['id', 'name'])->toArray();
        } elseif ($type === 'manufacturers') {
            return Manufacturer::whereIn('id', $ids)->limit(5)->get(['id', 'name'])->toArray();
        } elseif ($type === 'productcategories') {
            return Productcategory::whereIn('id', $ids)->limit(5)->get(['id', 'name'])->toArray();
        } elseif ($type === 'ImageSlider') {
            return Slider::whereIn('id', $ids)->limit(5)->get(['id', 'title as name'])->toArray();
        } elseif ($type === 'mixed') {
            $items = [];
            foreach (array_slice($ids, 0, 5) as $idStr) {
                $parts = explode(':', $idStr);
                if (count($parts) === 2) {
                    $model = null;
                    if ($parts[0] === 'classification') $model = Classification::find($parts[1]);
                    elseif ($parts[0] === 'manufacturer') $model = Manufacturer::find($parts[1]);
                    elseif ($parts[0] === 'productcategory') $model = Productcategory::find($parts[1]);
                    
                    if ($model) $items[] = ['name' => $model->name];
                }
            }
            return $items;
        }

        return [];
    }
}
