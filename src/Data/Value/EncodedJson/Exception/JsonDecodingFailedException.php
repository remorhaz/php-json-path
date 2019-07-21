<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Value\EncodedJson\Exception;

use RuntimeException;
use Throwable;

final class JsonDecodingFailedException extends RuntimeException implements ExceptionInterface
{

    private $json;

    public function __construct(string $json, Throwable $previous = null)
    {
        $this->json = $json;
        parent::__construct("Failed to decode JSON", 0, $previous);
    }

    public function getJson(): string
    {
        return $this->json;
    }
}
