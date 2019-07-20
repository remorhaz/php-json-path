<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Export;

use Iterator;
use Remorhaz\JSON\Data\Value\ValueInterface;

interface ExporterInterface
{

    public function exportEvents(Iterator $iterator);

    public function exportValue(ValueInterface $value);
}
