<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use function array_map;
use function implode;
use function is_int;
use function is_string;
use Remorhaz\JSON\Data\Path\PathInterface;
use function str_replace;

final class PathEncoder implements PathEncoderInterface
{

    public function encodePath(PathInterface $path): string
    {
        return '$' . implode('', array_map([$this, 'encodePathElement'], $path->getElements()));
    }

    private function encodePathElement($pathElement): string
    {
        if (is_int($pathElement)) {
            return "[{$pathElement}]";
        }

        if (is_string($pathElement)) {
            $escapedElement = str_replace(['\\', '\''], ['\\\\', '\\\''], $pathElement);

            return "['{$escapedElement}']";
        }

        throw new Exception\InvalidPathElementException($pathElement);
    }
}
