<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

final class Capabilities implements CapabilitiesInterface
{
    public function __construct(
        private readonly bool $isDefinite,
        private readonly bool $isAddressable,
    ) {
    }

    public function isDefinite(): bool
    {
        return $this->isDefinite;
    }

    public function isAddressable(): bool
    {
        return $this->isAddressable;
    }
}
