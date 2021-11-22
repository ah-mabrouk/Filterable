<?php

namespace Mabrouk\Filterable\Traits;

use Mabrouk\Filterable\Helpers\QueryFilter;

Trait Filterable
{
    use Searchable;

    public function scopeFilter($query, QueryFilter $filters)
    {
        return $filters->apply($query);
    }
}
