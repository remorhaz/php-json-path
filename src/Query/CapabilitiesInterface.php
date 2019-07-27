<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

interface CapabilitiesInterface
{

    public function isDefinite(): bool;

    public function isPath(): bool;
}
