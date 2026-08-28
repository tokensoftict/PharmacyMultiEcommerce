<div>
    <x-select-menu :value="$value" :edit-model="\App\Models\Stock::class" edit-column="name" :model="$wireModel" :id="$id" :placeholder="$placeholder" :class="$classname"  {{ $attributes }} :ajax="route(\App\Classes\ApplicationEnvironment::$storePrefix .'utilities.stock.select2search')"/>
</div>

