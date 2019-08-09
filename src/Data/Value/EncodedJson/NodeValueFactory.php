<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Value\EncodedJson;

use function json_decode;
use const JSON_THROW_ON_ERROR;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeValueFactory as DecodedJsonNodeValueFactory;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeValueFactoryInterface as DecodedJsonNodeValueFactoryInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Throwable;

final class NodeValueFactory implements NodeValueFactoryInterface
{

    private $decodedJsonNodeValueFactory;

    public static function create(): NodeValueFactoryInterface
    {
        return new self(DecodedJsonNodeValueFactory::create());
    }

    public function __construct(DecodedJsonNodeValueFactoryInterface $decodedJsonNodeValueFactory)
    {
        $this->decodedJsonNodeValueFactory = $decodedJsonNodeValueFactory;
    }

    public function createValue(string $json, ?PathInterface $path = null): NodeValueInterface
    {
        try {
            $decodedData = json_decode($json, false, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $e) {
            throw new Exception\JsonDecodingFailedException($json, $e);
        }

        return $this
            ->decodedJsonNodeValueFactory
            ->createValue($decodedData, $path);
    }
}
