<?php

namespace Kyanag\Query\Adapters\Traits;

use Latitude\QueryBuilder\Query\Capability\HasOrderBy;

/**
 * @property HasOrderBy $query
 */
trait HasOrderTrait
{
    public function orderBy($field, $type = "asc"): self
    {
        $this->query->orderBy($field, $type);
        return $this;
    }


    public function orderByDesc($field): self
    {
        $this->query->orderBy($field, "desc");
        return $this;
    }


    public function orderByRaw($raw)
    {
        $this->query->orderBy(query_raw($raw));
        return $this;
    }
}
