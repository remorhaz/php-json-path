<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Parser;

use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Grammar\SDD\TranslationSchemeInterface;
use Remorhaz\UniLex\Parser\LL1\Parser;

interface TranslatorFactoryInterface
{

    public function createTranslationScheme(Tree $queryAst): TranslationSchemeInterface;

    public function createParser(string $path, TranslationSchemeInterface $scheme): Parser;
}
