<?php

namespace Kyanag\Query\Interfaces;

interface QueryBuilderInterface
{
    /**
     * @return array
     */
    public function toSql(): array;
}
