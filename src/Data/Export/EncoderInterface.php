<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Export;

use Iterator;
use Remorhaz\JSON\Data\Value\ValueInterface;

interface EncoderInterface extends ExporterInterface
{

    public function exportEvents(Iterator $iterator): string;

    public function exportValue(ValueInterface $value): string;
}