<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Exception;

use Remorhaz\JSON\Data\Value\PathInterface;

trait PathAwareExceptionTrait
{

    /**
     * @var PathInterface
     */
    protected $path;

    public function getPath(): PathInterface
    {
        return $this->path;
    }

    protected function buildPath(): string
    {
        return '/' . implode('/', $this->path->getElements());
    }
}
