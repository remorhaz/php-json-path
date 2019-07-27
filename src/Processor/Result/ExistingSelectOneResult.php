<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Result;

use Remorhaz\JSON\Data\Export\DecoderInterface;
use Remorhaz\JSON\Data\Export\EncoderInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;

final class ExistingSelectOneResult implements SelectOneResultInterface
{

    private $jsonEncoder;

    private $jsonDecoder;

    private $value;

    public function __construct(EncoderInterface $jsonEncoder, DecoderInterface $jsonDecoder, ValueInterface $value)
    {
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
}
