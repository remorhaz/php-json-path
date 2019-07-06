<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

interface ProcessorInterface
{

    public function readDecoded(string $path, $decodedJson): ResultInterface;
}
