<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Parser;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Parser\Ll1ParserFactory;
use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\Parser\LL1\UnexpectedTokenException;

#[CoversClass(Ll1ParserFactory::class)]
class Ll1ParserFactoryTest extends TestCase
{
    /**
     * @throws UnexpectedTokenException
     * @throws UniLexException
     */
    #[DataProvider('providerCreateParserIsDefinite')]
    public function testCreateParser_Constructed_ResultBuildsAstWithMatchingIsDefiniteFlagOnRun(
        string $source,
        bool $isDefinite,
    ): void {
        $queryAst = new Tree();
        $ll1Parser = (new Ll1ParserFactory())->createParser($source, $queryAst);
        $ll1Parser->run();

        $astRootNode = $queryAst->getRootNode();
        self::assertSame($isDefinite, $astRootNode->getAttribute('is_definite'));
    }

    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function providerCreateParserIsDefinite(): iterable
    {
        return [
            'Definite path' => ['$', true],
            'Indefinite path' => ['$.*', false],
        ];
    }

    /**
     * @throws UnexpectedTokenException
     * @throws UniLexException
     */
    #[DataProvider('providerCreateParserIsAddressable')]
    public function testCreateParser_Constructed_ResultBuildsAstWithMatchingIsAddressableFlagOnRun(
        string $source,
        bool $isAddressable,
    ): void {
        $queryAst = new Tree();
        $ll1Parser = (new Ll1ParserFactory())->createParser($source, $queryAst);
        $ll1Parser->run();

        $astRootNode = $queryAst->getRootNode();
        self::assertSame($isAddressable, $astRootNode->getAttribute('is_addressable'));
    }

    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function providerCreateParserIsAddressable(): iterable
    {
        return [
            'Addressable path' => ['$', true],
            'Not addressable path' => ['$.length()', false],
        ];
    }
}
