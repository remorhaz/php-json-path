<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

interface QueryPropertiesInterface
{

    public function isDefinite(): bool;

    public function isPath(): bool;
}
