<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Result;

use Remorhaz\JSON\Data\Export\ValueDecoderInterface;
use Remorhaz\JSON\Data\Export\ValueEncoderInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;

final class ExistingValueResult implements ValueResultInterface
{
    public function __construct(
        private ValueEncoderInterface $jsonEncoder,
        private ValueDecoderInterface $jsonDecoder,
        private ValueInterface $value
    ) {
    }

    public function exists(): bool
    {
        return true;
    }

    public function encode(): string
    {
        return $this
            ->jsonEncoder
            ->exportValue($this->value);
    }

    public function decode(): mixed
    {
        return $this
            ->jsonDecoder
            ->exportValue($this->value);
    }

    public function get(): ValueInterface
    {
        return $this->value;
    }
}
