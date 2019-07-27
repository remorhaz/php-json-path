<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

interface QueryCapabilitiesInterface
{

    public function isDefinite(): bool;

    public function isPath(): bool;
}
