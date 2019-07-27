<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Result;

use function array_map;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Path\Processor\PathEncoderInterface;

final class SelectPathsResult implements SelectPathsResultInterface
{

    private $paths;

    private $encoder;

    public function __construct(PathEncoderInterface $encoder, PathInterface ...$paths)
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
