<?php

namespace App\Livewire\Backend\Admin\Stock;

use App\Classes\ApplicationEnvironment;
use App\Models\SupermarketsStockPrice;
use App\Models\WholessalesStockPrice;
use Livewire\Component;

class ShowStock extends Component
{

    public int $stock_id;

    public WholessalesStockPrice | SupermarketsStockPrice $selectedStock;


    public function mount()
    {
        if(ApplicationEnvironment::$stock_model == WholessalesStockPrice::class)
        {
            $this->selectedStock = WholessalesStockPrice::find($this->stock_id);
        } else {
            $this->selectedStock = SupermarketsStockPrice::find($this->stock_id);
        }
    }

    public function render()
    {
        return view('livewire.backend.admin.stock.show-stock');
    }
}
