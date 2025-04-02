<?php

namespace Kyanag\Query\With;

interface RelationLoaderInterface
{
    public function load(array $records, array $relations = []): array;
}
