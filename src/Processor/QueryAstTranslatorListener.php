<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use Remorhaz\UniLex\AST\AbstractTranslatorListener;
use Remorhaz\UniLex\AST\Symbol;
use Remorhaz\UniLex\Stack\PushInterface;

final class QueryAstTranslatorListener extends AbstractTranslatorListener
{

    public function onSymbol(Symbol $symbol, PushInterface $stack): void
    {

    }
}
