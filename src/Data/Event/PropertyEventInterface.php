<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Event;

interface PropertyEventInterface extends ChildEventInterface
{

    public function getName(): string;
}