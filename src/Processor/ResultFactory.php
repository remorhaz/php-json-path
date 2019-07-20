<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use Remorhaz\JSON\Data\Export\DecoderInterface;
use Remorhaz\JSON\Data\Export\EncoderInterface;
use Remorhaz\JSON\Path\Value\ValueListInterface;

final class ResultFactory implements ResultFactoryInterface
{

    private $encoder;

    private $decoder;

    public function __construct(EncoderInterface $encoder, DecoderInterface $decoder)
    {
        $this->encoder = $encoder;
        $this->decoder = $decoder;
    }

    public function createResult(ValueListInterface $values): SelectResultInterface
    {
        return new SelectResult($this->encoder, $this->decoder, ...$values->getValues());
    }
}
