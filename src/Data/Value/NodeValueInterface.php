<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Value;

use Remorhaz\JSON\Data\Path\PathAwareInterface;

interface NodeValueInterface extends ValueInterface, PathAwareInterface
{
}
