<?php

declare(strict_types = 1);

namespace DevTools\Doctrine\MySql\Functions;

class MBRContains extends AbstractDualGeometry
{
    protected string $functionName = 'MBRContains';
}
