<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\UniLex\AST\TranslatorListenerInterface;

interface QueryCallbackBuilderInterface extends TranslatorListenerInterface
{

    public function getQueryCallback(): callable;

    public function isDefinite(): bool;
}
