<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

final class Capabilities implements CapabilitiesInterface
{

    private $isDefinite;

    private $isAddressable;

    public function __construct(bool $isDefinite, bool $isAddressable)
    {
        $this->isDefinite = $isDefinite;
        $this->isAddressable = $isAddressable;
    }

    /**
     * @return bool
     */
    public function isDefinite(): bool
    {
        return $this->isDefinite;
    }

    /**
     * @return bool
     */
    public function isAddressable(): bool
    {
        return $this->isAddressable;
    }
}
