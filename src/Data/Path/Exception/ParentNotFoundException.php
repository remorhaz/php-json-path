<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Path\Exception;

use LogicException;
use Remorhaz\JSON\Data\Path\PathInterface;
use Throwable;

final class ParentNotFoundException extends LogicException implements ExceptionInterface
{

    private $path;

    public function __construct(PathInterface $path, Throwable $previous = null)
    {
        $this->path = $path;
        parent::__construct("Parent not found in path", 0, $previous);
    }

    public function getPath(): PathInterface
    {
        return $this->path;
    }
}
