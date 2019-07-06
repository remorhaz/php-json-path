<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Parser;

use Remorhaz\JSON\Path\Iterator\NodeValueInterface;
use Remorhaz\UniLex\Grammar\SDD\TranslationSchemeInterface as SddTranslationSchemeInterface;

interface TranslationSchemeInterface extends SddTranslationSchemeInterface
{

    /**
     * @return NodeValueInterface[]
     */
    public function getOutput(): array;
}
