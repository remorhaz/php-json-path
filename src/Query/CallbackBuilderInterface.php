<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\UniLex\AST\TranslatorListenerInterface;

interface CallbackBuilderInterface extends TranslatorListenerInterface
{

    public function getCallback(): callable;

    public function getCallbackCode(): string;

    public function getCapabilities(): CapabilitiesInterface;
}
