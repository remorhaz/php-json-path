<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use function array_map;
use Remorhaz\JSON\Data\Path\PathInterface;

final class SelectPathsResult implements SelectPathsResultInterface
{

    private $paths;

    private $encoder;

    public function __construct(PathEncoder $encoder, PathInterface ...$paths)
    {
        $this->encoder = $encoder;
        $this->paths = $paths;
    }

    public function get(): array
    {
        return $this->paths;
    }

    public function encode(): array
    {
        return array_map([$this->encoder, 'encodePath'], $this->paths);
    }
}
