<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data;

use Remorhaz\JSON\Data\Value\DecodedJson;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Value\Path;
use Remorhaz\JSON\Data\Value\PathInterface;

final class JsonFactory
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
