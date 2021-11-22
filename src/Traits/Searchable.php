<?php

namespace Mabrouk\Filterable\Traits;

Trait Searchable
{
    public $guideObject = null;
    public $searchableFields = [];

    public function scopeSearch($query1, $value = null, $typical = false)
    {
        return $query1->where(function ($query2) use ($value, $typical) {
            $this->guideObject = self::first();
            $this->searchableFields = $this->searchableFields();
            if ($this->guideObject == null || $value == null || $this->searchableFields == null) {
                return $query2;
            }
            return $this->appendToQuery($query2, $value, $typical)->sort();
        });
    }

    public function scopeSort($query, $direction = null)
    {
        $availableValues = ['asc', 'desc'];
        $direction = $direction != null ? $direction : request()->sort;
        $direction = \in_array($direction, $availableValues) ? $direction : 'asc';
        return $query->orderBy($this->sortingField(), $direction);
    }

    public function sortingField()
    {
        switch (true) {
            case request()->sort_by != null && \in_array(request()->sort_by, \array_keys($this->guideObject->getAttributes())) :
                return request()->sort_by;
            case \in_array('id', \array_keys($this->guideObject->getAttributes())) :
                return 'id';
            case \in_array('created_at', \array_keys($this->guideObject->getAttributes())) :
                return 'created_at';
            default :
                return \array_keys($this->guideObject->getAttributes())[0];
        }
    }

    private function appendToQuery($query1, $value, $typical = false)
    {
        $operator = $typical ? '=' : 'like';
        $value = $typical ? $value : "%{$value}%";
        for ($i = 0; $i < \count($this->searchableFields); $i++) {
            $queryStatement = $i == 0 ? 'where' : 'orWhere';
            switch (true) {
                case $this->isTranslatableAttribute($this->searchableFields[$i]) :
                    $queryStatement = "{$queryStatement}Has";
                    $query1->$queryStatement('translations', function ($query2) use ($value, $i, $operator) {
                        $query2->where($this->searchableFields[$i], $operator, $value);
                    });
                break;
                default :
                    $table = $this->guideObject->getTable();
                    $query1->$queryStatement("{$table}.{$this->searchableFields[$i]}", $operator, $value);
            }
        }
        return $query1->sort();
    }

    private function searchableFields()
    {
        switch (true) {
            case $this->guideObject == null :
                return [];
            break;
            case (bool) $this->searchableFields :
                return $this->searchableFields;
            break;
            case (bool) request()->searchables :
                $requestedFields = \explode(',', request()->searchables);
                $this->searchableFields = \array_filter($requestedFields, function ($fieldName) {
                    return $this->isSearchable($fieldName, $this->guideObject);
                });
            break;
            default :
                $translatedAttributes = $this->guideObject->translatedAttributes ?? [];
                $this->searchableFields = \array_merge(\array_keys($this->guideObject->getAttributes()), $translatedAttributes);
            break;
        }
        return $this->searchableFields;
    }

    private function isSearchable($attribute)
    {
        return \in_array($attribute, \array_keys($this->guideObject->getAttributes())) || $this->guideObject->isTranslatableAttribute($attribute, $this->guideObject);
    }

    private function isTranslatableAttribute($attribute)
    {
        return $this->guideObject->translatedAttributes ? \in_array($attribute, $this->guideObject->translatedAttributes) : false;
    }
}
