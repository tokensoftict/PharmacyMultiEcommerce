<?php

namespace App\View\Components\Utilities\General;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StockSearchComponent extends Component
{
    public String $wireModel ="";
    public String $placeholder ="";

    public String $classname ="";

    public String $id = "";
    public function __construct($wireModel, $placeholder, $classname, $id)
    {
        $this->wireModel = $wireModel;
        $this->placeholder = $placeholder;
        $this->classname = $classname;
        $this->id = $id;
    }

    public function render(): View|Closure|string
    {
        return view('components.utilities.general.stock-search-component');
    }
}
