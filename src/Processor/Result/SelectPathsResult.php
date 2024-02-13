<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Result;

use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Path\Processor\PathEncoderInterface;

use function array_map;
use function array_values;

final class SelectPathsResult implements SelectPathsResultInterface
{
    /**
     * @var list<PathInterface>
     */
    private readonly array $paths;

    public function __construct(
        private readonly PathEncoderInterface $encoder,
        PathInterface ...$paths,
    ) {
        $this->paths = array_values($paths);
    }

    /**
     * @return list<PathInterface>
     */
    public function get(): array
    {
        return $this->paths;
    }

    /**
     * @return list<string>
     */
    public function encode(): array
    {
        return array_map($this->encoder->encodePath(...), $this->paths);
    }
}
