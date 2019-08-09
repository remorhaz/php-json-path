<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Export;

use function json_encode;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;
use Remorhaz\JSON\Data\Value\ValueInterface;
use Throwable;

/**
 * @todo Don't use decoder
 */
final class Encoder implements EncoderInterface
{

    private $decoder;

    public function __construct(DecoderInterface $decoder)
    {
        $this->decoder = $decoder;
    }

    public function exportValue(ValueInterface $value): string
    {
        $decodedValue = $this
            ->decoder
            ->exportValue($value);
        try {
            return json_encode(
                $decodedValue,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
            );
        } catch (Throwable $e) {
            throw new Exception\EncodingFailedException($decodedValue, $e);
        }
    }
}
