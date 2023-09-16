<?php

class QueryJoin extends Query
{
    public $type;
    public $table;
    protected $parentClass;

    public function __construct(Query $parentQuery, $type, $table)
    {
        $this->type = $type;
        $this->table = $table;
        $this->parentClass = get_class($parentQuery);
    }

    public function on($first, $operator = null, $second = null, $boolean = 'and')
    {
        if ($first instanceof Closure) {
            return $this->whereNested($first, $boolean);
        }

        return $this->whereColumn($first, $operator, $second, $boolean);
    }

    public function newQuery()
    {
        return new static($this->newParentQuery(), $this->type, $this->table);
    }

    protected function forSubQuery()
    {
        return $this->newParentQuery()->newQuery();
    }

    protected function newParentQuery()
    {
        $class = $this->parentClass;

        return new $class();
    }
}