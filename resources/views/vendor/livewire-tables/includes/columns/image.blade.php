<a href="#" {!! count($attributes) ? $column->arrayToAttributes($attributes) : '' !!}>
    <img src="{{ $path }}" alt="{{ $attributes['alt'] }}" width="100" />
</a>
