<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Result;

use Remorhaz\JSON\Data\Export\ValueDecoderInterface;
use Remorhaz\JSON\Data\Export\ValueEncoderInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;

use function array_map;

final class SelectResult implements SelectResultInterface
{

    private $encoder;

    private $decoder;

    private $values;

    public function __construct(
        ValueEncoderInterface $encoder,
        ValueDecoderInterface $decoder,
        ValueInterface ...$values
    ) {
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
     * @return string[]
     */
    public function encode(): array
    {
        return array_map([$this->encoder, 'exportValue'], $this->values);
    }

    /**
     * {@inheritDoc}
     *
     * @return ValueInterface[]
     */
    public function get(): array
    {
        return $this->values;
    }
}
