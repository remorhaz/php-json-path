<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\UniLex\AST\TranslatorListenerInterface;

interface CallbackBuilderInterface extends TranslatorListenerInterface
{

    public function getQueryCallback(): callable;

    /**
     * @return bool
     * @deprecated
     */
    public function isDefinite(): bool;

    /**
     * @return CapabilitiesInterface
     * @deprecated
     */
    public function getQueryProperties(): CapabilitiesInterface;

    public function getQueryCapabilities(): CapabilitiesInterface;
}
