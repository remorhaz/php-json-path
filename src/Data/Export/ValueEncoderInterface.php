<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Export;

use Remorhaz\JSON\Data\Value\ValueInterface;

interface ValueEncoderInterface extends ValueExporterInterface
{

    public function exportValue(ValueInterface $value): string;
}
