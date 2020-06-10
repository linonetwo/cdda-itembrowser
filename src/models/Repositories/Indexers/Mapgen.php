<?php

namespace Repositories\Indexers;

use Repositories\RepositoryWriterInterface;

class Mapgen implements IndexerInterface
{
    const DEFAULT_INDEX = "mapgen";

    public function onNewObject(RepositoryWriterInterface $repo, $object)
    {
        if ($object->type == "overmap_terrain") {
            $repo->append(self::DEFAULT_INDEX, $object->id);
            $repo->set(self::DEFAULT_INDEX.".$object->id", $object->repo_id);
            // $repo->set("all.$object->id", $object->repo_id);
        }
        if ($object->type == "mapgen") {
            if (isset($object->nested_mapgen_id)) {
                echo "nested mapgen: $object->nested_mapgen_id\n";
                return;
            }
            if (isset($object->object->items)) {
                foreach ($object->object->items as $_ => $value) {
                    if (is_object($value)) {
                        $repo->appendUnique("itemgroup.dropfrommap.$value->item", $object->om_terrain);
                    } else {
                        foreach ($value as $v) {
                            $repo->appendUnique("itemgroup.dropfrommap.$v->item", $object->om_terrain);
                        }
                    }
                }
            }
        }
    }

    public function onFinishedLoading(RepositoryWriterInterface $repo)
    {
    }
}
