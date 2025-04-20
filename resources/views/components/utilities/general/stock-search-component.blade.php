<div>
    <x-dropdown-select-menu :value="$value" :edit-model="\App\Models\Stock::class" edit-column="name" :model="$wireModel" :id="$id" :placeholder="$placeholder" :class="$classname"  {{ $attributes }} :ajax="route('utilities.stock.select2search')"/>
</div>

