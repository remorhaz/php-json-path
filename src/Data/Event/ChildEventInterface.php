<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Event;

use Remorhaz\JSON\Data\Value\PathAwareInterface;

interface ChildEventInterface extends PathAwareInterface, DataEventInterface
{
}
