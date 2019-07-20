<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use function array_map;
use Remorhaz\JSON\Data\Export\DecoderInterface;
use Remorhaz\JSON\Data\Export\EncoderInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;

final class SelectResult implements SelectResultInterface
{

    private $encoder;

    private $decoder;

    private $values;

    public function __construct(EncoderInterface $encoder, DecoderInterface $decoder, ValueInterface ...$values)
    {
        $this->encoder = $encoder;
        $this->decoder = $decoder;
        $this->values = $values;
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function decode(): array
    {
        return array_map([$this->decoder, 'exportValue'], $this->values);
    }

    /**
     * {@inheritDoc}
     *
     * @return array
     */
    public function asJson(): array
    {
        return array_map([$this->encoder, 'exportValue'], $this->values);
    }
}
