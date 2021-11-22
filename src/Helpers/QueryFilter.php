<?php

namespace Mabrouk\Filterable\Helpers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

abstract class QueryFilter
{
    protected $request;
    protected $builder;
    protected $availableBooleanValues = [
        'yes' => 1,
        'no' => 0,
    ];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query)
    {
        $this->builder = $query;
        foreach ($this->filters() as $name => $value) {
            method_exists($this, $name) ?
            call_user_func_array([$this, $name], array_filter([$value])) : '';
        }
        return $this->builder;
    }

    public function filters()
    {
        return $this->request->all();
    }


    public function search($value = null)
    {
        return $value != null ? $this->builder->search($value) : $this->builder;
    }

    public function trashed()
    {
        return $this->builder->withTrashed();
    }
}
