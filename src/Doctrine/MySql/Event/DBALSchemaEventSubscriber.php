<?php

declare(strict_types = 1);

namespace DevTools\Doctrine\MySql\Event;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Event\SchemaIndexDefinitionEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\DBAL\Schema\Index;

class DBALSchemaEventSubscriber implements EventSubscriber
{
    private const SPATIAL_FLAG = 'SPATIAL';

    public function getSubscribedEvents()
    {
        return [Events::onSchemaIndexDefinition];
    }

    public function onSchemaIndexDefinition(SchemaIndexDefinitionEventArgs $args): void
    {
        $index = $args->getTableIndex();

        if (in_array(self::SPATIAL_FLAG, $index['flags'])) {
            $spatialIndex = new Index(
                $index['name'],
                $index['columns'],
                $index['unique'],
                $index['primary'],
                $index['flags']
            );

            $args
                ->setIndex($spatialIndex)
                ->preventDefault()
            ;
        }
    }
}
