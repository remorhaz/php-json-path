<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Parser;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Parser\Ll1ParserFactory;
use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\Parser\LL1\UnexpectedTokenException;

/**
 * @covers \Remorhaz\JSON\Path\Parser\Ll1ParserFactory
 */
class Ll1ParserFactoryTest extends TestCase
{

    /**
     * @param string $source
     * @param bool $isDefinite
     * @throws UnexpectedTokenException
     * @throws UniLexException
     * @dataProvider providerCreateParserIsDefinite
     */
    public function testCreateParser_Constructed_ResultBuildsAstWithMatchingIsDefiniteFlagOnRun(
        string $source,
        bool $isDefinite
    ): void {
        $queryAst = new Tree();
        $ll1Parser = (new Ll1ParserFactory())->createParser($source, $queryAst);
        $ll1Parser->run();

        $astRootNode = $queryAst->getRootNode();
        self::assertSame($isDefinite, $astRootNode->getAttribute('is_definite'));
    }

    public function providerCreateParserIsDefinite(): array
    {
        return [
            'Definite path' => ['$', true],
            'Indefinite path' => ['$.*', false],
        ];
    }

    /**
     * @param string $source
     * @param bool $isAddressable
     * @throws UnexpectedTokenException
     * @throws UniLexException
     * @dataProvider providerCreateParserIsAddressable
     */
    public function testCreateParser_Constructed_ResultBuildsAstWithMatchingIsAddressableFlagOnRun(
        string $source,
        bool $isAddressable
    ): void {
        $queryAst = new Tree();
        $ll1Parser = (new Ll1ParserFactory())->createParser($source, $queryAst);
        $ll1Parser->run();

        $astRootNode = $queryAst->getRootNode();
        self::assertSame($isAddressable, $astRootNode->getAttribute('is_addressable'));
    }

    public function providerCreateParserIsAddressable(): array
    {
        return [
            'Addressable path' => ['$', true],
            'Not addressable path' => ['$.length()', false],
        ];
    }
}
