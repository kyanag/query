<?php

namespace Kyanag\Query\Adapters\SubQuery;

use Kyanag\Query\Adapters\Traits\HasWhereTrait;
use Latitude\QueryBuilder\CriteriaInterface;

class WhereQuery
{
    use HasWhereTrait;

    /**
     * @var CriteriaInterface|null
     */
    protected $criteria;


    public function addCondition(CriteriaInterface $condition, $type = "and"): self
    {
        if ($this->criteria === null) {
            $this->criteria = $condition;
        } else {
            if ($type == "and") {
                $this->criteria = $this->criteria->and($condition);
            } else {
                $this->criteria = $this->criteria->or($condition);
            }
        }
        return $this;
    }


    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->criteria === null;
    }


    public function toQuery()
    {
        return $this->criteria;
    }
}
