<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\UniLex\AST\TranslatorListenerInterface;

interface CallbackBuilderInterface extends TranslatorListenerInterface
{

    public function getQueryCallback(): callable;

    public function getQueryCapabilities(): CapabilitiesInterface;
}
