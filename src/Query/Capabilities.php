<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

final class Capabilities implements CapabilitiesInterface
{

    private $isDefinite;

    private $isPath;

    public function __construct(bool $isDefinite, bool $isPath)
    {
        $this->isDefinite = $isDefinite;
        $this->isPath = $isPath;
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
        return $this->isPath;
    }
}
