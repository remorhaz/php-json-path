<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data;

use Remorhaz\JSON\Data\Value\DecodedJson;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Path\Path;
use Remorhaz\JSON\Data\Path\PathInterface;

final class JsonFactory
{

    private $path;

    private $decodedJsonNodeValueFactory;

    public static function create(): self
    {
        return new self(new Path, new DecodedJson\NodeValueFactory);
    }

    public function __construct(
        PathInterface $path,
        DecodedJson\NodeValueFactoryInterface $decodedJsonNodeValueFactory
    ) {
        $this->path = $path;
        $this->decodedJsonNodeValueFactory = $decodedJsonNodeValueFactory;
    }

    public function fromDecodedJson($json): NodeValueInterface
    {
        return $this
            ->decodedJsonNodeValueFactory
            ->createValue($json, $this->path);
    }
}
