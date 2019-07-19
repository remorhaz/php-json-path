<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data;

use Remorhaz\JSON\Data\DecodedJson;
use Remorhaz\JSON\Data\NodeValueInterface;
use Remorhaz\JSON\Data\Path;
use Remorhaz\JSON\Data\PathInterface;

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
