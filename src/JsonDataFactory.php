<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path;

use Remorhaz\JSON\Path\Iterator\DecodedJson;
use Remorhaz\JSON\Path\Iterator\NodeValueInterface;
use Remorhaz\JSON\Path\Iterator\Path;
use Remorhaz\JSON\Path\Iterator\PathInterface;

final class JsonDataFactory
{

    private $emptyPath;

    private $decodedJsonNodeValueFactory;

    public static function create(): self
    {
        return new self(new Path, new DecodedJson\NodeValueFactory);
    }

    public function __construct(
        PathInterface $emptyPath,
        DecodedJson\NodeValueFactoryInterface $decodedJsonNodeValueFactory
    ) {
        $this->emptyPath = $emptyPath;
        $this->decodedJsonNodeValueFactory = $decodedJsonNodeValueFactory;
    }

    public function fromDecodedJson($json): NodeValueInterface
    {
        return $this
            ->decodedJsonNodeValueFactory
            ->createValue($json, $this->emptyPath);
    }
}