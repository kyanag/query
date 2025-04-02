<?php

namespace Kyanag\Query\With;

use Kyanag\Query\Interfaces\ConnectionInterface;

class RelationLoader implements RelationLoaderInterface
{
    protected ConnectionInterface $connection;


    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }


    public function load(array $records, array $relations = []): array
    {
        /** @var RelationInterface $relation */
        foreach ($relations as $key => $relation) {
            $relationRecords = $this->fetchRelationData($records, $relation);
            $records = $relation->match($records, $relationRecords, $key);
        }
        return $records;
    }


    public function fetchRelationData(array $records, RelationInterface $relation): array
    {
        $records = $relation->fetchData($records);
        $relations = $relation->getRelations();
        if (!empty($relations)) {
            $records = $this->load($records, $relations);
        }
        return $records;
    }
}
