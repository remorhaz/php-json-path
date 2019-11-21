<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Result;

use Remorhaz\JSON\Data\Export\ValueDecoderInterface;
use Remorhaz\JSON\Data\Export\ValueEncoderInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;

final class ExistingValueResult implements ValueResultInterface
{

    private $jsonEncoder;

    private $jsonDecoder;

    private $value;

    public function __construct(
        ValueEncoderInterface $jsonEncoder,
        ValueDecoderInterface $jsonDecoder,
        ValueInterface $value
    ) {
        $this->jsonEncoder = $jsonEncoder;
        $this->jsonDecoder = $jsonDecoder;
        $this->value = $value;
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

    public function decode()
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
