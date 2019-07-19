<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Event;

use Remorhaz\JSON\Data\ValueInterface;

interface ValueEventInterface extends DataEventInterface
{

    public function getValue(): ValueInterface;
}
