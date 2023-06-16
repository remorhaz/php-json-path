<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Result;

use Remorhaz\JSON\Data\Export\ValueDecoderInterface;
use Remorhaz\JSON\Data\Export\ValueEncoderInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;

use function array_map;
use function array_values;

final class SelectResult implements SelectResultInterface
{
    /**
     * @var list<ValueInterface>
     */
    private array $values;

    public function __construct(
        private ValueEncoderInterface $encoder,
        private ValueDecoderInterface $decoder,
        ValueInterface ...$values,
    ) {
        $this->values = array_values($values);
    }

    /**
     * {@inheritDoc}
     *
     * @return list<mixed>
     */
    public function decode(): array
    {
        return array_map([$this->decoder, 'exportValue'], $this->values);
    }

    /**
     * {@inheritDoc}
     *
     * @return list<string>
     */
    public function encode(): array
    {
        return array_map([$this->encoder, 'exportValue'], $this->values);
    }

    /**
     * {@inheritDoc}
     *
     * @return list<ValueInterface>
     */
    public function get(): array
    {
        return $this->values;
    }
}
