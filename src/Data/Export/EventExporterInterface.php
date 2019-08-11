<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Export;

use Iterator;
use Remorhaz\JSON\Data\Value\NodeValueInterface;

interface EventExporterInterface
{

    public function exportEvents(Iterator $events): ?NodeValueInterface;
}
