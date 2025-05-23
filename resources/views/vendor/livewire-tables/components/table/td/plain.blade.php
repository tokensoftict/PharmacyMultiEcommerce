@aware(['component', 'rowIndex', 'rowID'])
@props(['column' => null, 'customAttributes' => [], 'displayMinimisedOnReorder' => false, 'hideUntilReorder' => false])

@if ($component->isTailwind())
    <td x-cloak {{ $attributes
        ->merge($customAttributes)
        ->class(['px-6 py-4 whitespace-nowrap text-sm font-medium dark:text-white' => $customAttributes['default'] ?? true])
        ->class(['hidden' => $column && $column->shouldCollapseAlways()])
        ->class(['hidden sm:table-cell' => $column && $column->shouldCollapseOnMobile()])
        ->class(['hidden md:table-cell' => $column && $column->shouldCollapseOnTablet()])
        ->except('default')
    }} @if($hideUntilReorder) x-show="reorderDisplayColumn" @endif >
        {{ $slot }}
    </td>
@elseif ($component->isBootstrap())
    <td {{ $attributes
        ->merge($customAttributes)
        ->class(['fs-9 align-middle ps-0 py-3' => $customAttributes['default'] ?? true])
        ->class(['d-none fs-9 align-middle ps-0 py-3' => $column && $column->shouldCollapseAlways()])
        ->class(['d-none d-sm-table-cell fs-9 align-middle ps-0 py-3' => $column && $column->shouldCollapseOnMobile()])
        ->class(['d-none d-md-table-cell fs-9 align-middle ps-0 py-3' => $column && $column->shouldCollapseOnTablet()])
        ->except('default')
    }}>
        {{ $slot }}
    </td>
@endif
