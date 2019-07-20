<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Value\Exception;

use Remorhaz\JSON\Data\Exception\ExceptionInterface as DataExceptionInterface;
use Remorhaz\JSON\Path\Exception\ExceptionInterface as PathExceptionInterface;

interface ExceptionInterface extends DataExceptionInterface, PathExceptionInterface
{
}
