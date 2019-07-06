<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Parser;

use Remorhaz\JSON\Path\Iterator\NodeValueInterface;
use Remorhaz\UniLex\Grammar\SDD\TranslationSchemeInterface as SddTranslationScheme;
use Remorhaz\UniLex\Parser\LL1\Parser;

interface TranslatorFactoryInterface
{

    public function createTranslationScheme(NodeValueInterface $rootValue): TranslationSchemeInterface;

    public function createParser(string $path, SddTranslationScheme $scheme): Parser;
}
