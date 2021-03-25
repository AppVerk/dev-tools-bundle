<?php

declare(strict_types = 1);

namespace DevTools\Doctrine\MySql\Functions;

class ST_Distance_Sphere extends AbstractDualGeometry
{
    protected string $functionName = 'ST_Distance_Sphere';
}
