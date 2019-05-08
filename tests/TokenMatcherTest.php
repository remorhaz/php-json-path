<?php

namespace Remorhaz\JSON\Path\Test;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\TokenMatcher;
use Remorhaz\JSON\Path\TokenType;
use Remorhaz\UniLex\Lexer\TokenFactory;
use Remorhaz\UniLex\Lexer\TokenReader;
use Remorhaz\UniLex\Unicode\CharBufferFactory;

/**
 * @covers \Remorhaz\JSON\Path\TokenMatcher
 */
class TokenMatcherTest extends TestCase
{

    /**
     * @param string $input
     * @param array $expectedValue
     * @throws \Remorhaz\UniLex\Exception
     * @dataProvider providerValidInputTokenTypeList
     */
    public function testMatch_TokenReaderUsedToMatchAllTokensFromValidInput_ProducesMatchingTokenTypeList(
        string $input,
        array $expectedValue
    ): void {
        $matcher = new TokenMatcher();
        $buffer = CharBufferFactory::createFromString($input);
        $tokenFactory = new TokenFactory(TokenType::EOI);
        $reader = new TokenReader($buffer, $matcher, $tokenFactory);
        $actualValue = [];
        do {
            $token = $reader->read();
            $actualValue[] = $token->getType();
        } while (!$token->isEoi());
        self::assertSame($expectedValue, $actualValue);
    }

    public function providerValidInputTokenTypeList(): array
    {
        return [
            "One-symbol name (root)" => ['$', [TokenType::ROOT_ABSOLUTE, TokenType::EOI]],
            "One-symbol name (self)" => ['@', [TokenType::ROOT_RELATIVE, TokenType::EOI]],
            "One-symbol name (generic)" => ['x', [TokenType::NAME, TokenType::EOI]],
            "Multi-symbol name (generic)" => ['abc', [TokenType::NAME, TokenType::EOI]],
            "Multi-symbol name (mixed)" => ['_a@bc01$', [TokenType::NAME, TokenType::EOI]],
            "Name after integer" => ['1ab', [TokenType::INT, TokenType::NAME, TokenType::EOI]],
            "Single dot" => ['.', [TokenType::DOT, TokenType::EOI]],
            "Double dot" => ['..', [TokenType::DOUBLE_DOT, TokenType::EOI]],
            "Single dot after double dot" => ['...', [TokenType::DOUBLE_DOT, TokenType::DOT, TokenType::EOI]],
            "Left bracket" => ['(', [TokenType::LEFT_BRACKET, TokenType::EOI]],
            "Right bracket" => [')', [TokenType::RIGHT_BRACKET, TokenType::EOI]],
            "Left square bracket" => ['[', [TokenType::LEFT_SQUARE_BRACKET, TokenType::EOI]],
            "Right square bracket" => [']', [TokenType::RIGHT_SQUARE_BRACKET, TokenType::EOI]],
            "Star" => ['*', [TokenType::STAR, TokenType::EOI]],
            "Comma" => [',', [TokenType::COMMA, TokenType::EOI]],
            "Question" => ['?', [TokenType::QUESTION, TokenType::EOI]],
            "Colon" => [':', [TokenType::COLON, TokenType::EOI]],
            "Zero integer" => ['0', [TokenType::INT, TokenType::EOI]],
            "Non-zero single-symbol integer" => ['1', [TokenType::INT, TokenType::EOI]],
            "Non-zero multi-symbol integer" => ['12', [TokenType::INT, TokenType::EOI]],
            "Non-zero multi-symbol integer after zero integer" => [
                '012',
                [TokenType::INT, TokenType::INT, TokenType::EOI],
            ],
            "Zero integer after zero integer" => ['00', [TokenType::INT, TokenType::INT, TokenType::EOI]],
            "Hyphen" => ['-', [TokenType::HYPHEN, TokenType::EOI]],
            "Single quote" => ['\'', [TokenType::SINGLE_QUOTE, TokenType::EOI]],
            "Empty single quoted string" => [
                '\'\'',
                [TokenType::SINGLE_QUOTE, TokenType::SINGLE_QUOTE, TokenType::EOI],
            ],
            "Non-empty open single-symbol single-quoted string" => [
                '\'a',
                [TokenType::SINGLE_QUOTE, TokenType::UNESCAPED, TokenType::EOI],
            ],
            "Non-empty open multi-symbol single-quoted string" => [
                '\'abc',
                [TokenType::SINGLE_QUOTE, TokenType::UNESCAPED, TokenType::EOI],
            ],
            "Non-empty single-quoted string" => [
                '\'abc\'',
                [TokenType::SINGLE_QUOTE, TokenType::UNESCAPED, TokenType::SINGLE_QUOTE, TokenType::EOI],
            ],
            "Dot inside single-quoted string" => [
                '\'.\'',
                [TokenType::SINGLE_QUOTE, TokenType::UNESCAPED, TokenType::SINGLE_QUOTE, TokenType::EOI],
            ],
            "Dot after empty single-quoted string" => [
                '\'\'.',
                [TokenType::SINGLE_QUOTE, TokenType::SINGLE_QUOTE, TokenType::DOT, TokenType::EOI],
            ],
            "Escaped backslash in single-quoted string" => [
                '\'\\\\a\'',
                [
                    TokenType::SINGLE_QUOTE,
                    TokenType::BACKSLASH,
                    TokenType::BACKSLASH,
                    TokenType::UNESCAPED,
                    TokenType::SINGLE_QUOTE,
                    TokenType::EOI,
                ],
            ],
            "Escaped single-quote in single-quoted string" => [
                '\'\\\'a\'',
                [
                    TokenType::SINGLE_QUOTE,
                    TokenType::BACKSLASH,
                    TokenType::SINGLE_QUOTE,
                    TokenType::UNESCAPED,
                    TokenType::SINGLE_QUOTE,
                    TokenType::EOI,
                ],
            ],
            "Double quote" => ['"', [TokenType::DOUBLE_QUOTE, TokenType::EOI]],
            "Empty Double quoted string" => [
                '""',
                [TokenType::DOUBLE_QUOTE, TokenType::DOUBLE_QUOTE, TokenType::EOI],
            ],
            "Non-empty open single-symbol double-quoted string" => [
                '"a',
                [TokenType::DOUBLE_QUOTE, TokenType::UNESCAPED, TokenType::EOI],
            ],
            "Non-empty open multi-symbol double-quoted string" => [
                '"abc',
                [TokenType::DOUBLE_QUOTE, TokenType::UNESCAPED, TokenType::EOI],
            ],
            "Non-empty double-quoted string" => [
                '"abc"',
                [TokenType::DOUBLE_QUOTE, TokenType::UNESCAPED, TokenType::DOUBLE_QUOTE, TokenType::EOI],
            ],
            "Dot inside double-quoted string" => [
                '"."',
                [TokenType::DOUBLE_QUOTE, TokenType::UNESCAPED, TokenType::DOUBLE_QUOTE, TokenType::EOI],
            ],
            "Dot after empty double-quoted string" => [
                '"".',
                [TokenType::DOUBLE_QUOTE, TokenType::DOUBLE_QUOTE, TokenType::DOT, TokenType::EOI],
            ],
            "Escaped backslash in double-quoted string" => [
                '"\\\\a"',
                [
                    TokenType::DOUBLE_QUOTE,
                    TokenType::BACKSLASH,
                    TokenType::BACKSLASH,
                    TokenType::UNESCAPED,
                    TokenType::DOUBLE_QUOTE,
                    TokenType::EOI,
                ],
            ],
            "Escaped double-quote in double-quoted string" => [
                '"\\"a"',
                [
                    TokenType::DOUBLE_QUOTE,
                    TokenType::BACKSLASH,
                    TokenType::DOUBLE_QUOTE,
                    TokenType::UNESCAPED,
                    TokenType::DOUBLE_QUOTE,
                    TokenType::EOI,
                ],
            ],
            "EQ operator" => ['==', [TokenType::OP_EQ, TokenType::EOI]],
            "NEQ operator" => ['!=', [TokenType::OP_NEQ, TokenType::EOI]],
            "G operator" => ['>', [TokenType::OP_G, TokenType::EOI]],
            "GE operator" => ['>=', [TokenType::OP_GE, TokenType::EOI]],
            "L operator" => ['<', [TokenType::OP_L, TokenType::EOI]],
            "LE operator" => ['<=', [TokenType::OP_LE, TokenType::EOI]],
            "REGEX operator" => ['=~', [TokenType::OP_REGEX, TokenType::EOI]],
            "AND operator" => ['&&', [TokenType::OP_AND, TokenType::EOI]],
            "OR operator" => ['||', [TokenType::OP_OR, TokenType::EOI]],
            "NOT operator" => ['!', [TokenType::OP_NOT, TokenType::EOI]],
            "NEQ operator after NOT operator" => ['!!=', [TokenType::OP_NOT, TokenType::OP_NEQ, TokenType::EOI]],
            "Slash" => ['/', [TokenType::SLASH, TokenType::EOI]],
            "Empty regexp" => ['//', [TokenType::SLASH, TokenType::REGEXP_MOD, TokenType::EOI]],
            "Non-empty open single-symbol regexp" => [
                '/a',
                [TokenType::SLASH, TokenType::UNESCAPED, TokenType::EOI],
            ],
            "Non-empty open multi-symbol regexp" => [
                '/abc',
                [TokenType::SLASH, TokenType::UNESCAPED, TokenType::EOI],
            ],
            "Non-empty regexp" => [
                '/abc/',
                [TokenType::SLASH, TokenType::UNESCAPED, TokenType::REGEXP_MOD, TokenType::EOI],
            ],
            "Dot inside regexp" => [
                '/./',
                [TokenType::SLASH, TokenType::UNESCAPED, TokenType::REGEXP_MOD, TokenType::EOI],
            ],
            "Dot after empty regexp" => [
                '//.',
                [TokenType::SLASH, TokenType::REGEXP_MOD, TokenType::DOT, TokenType::EOI],
            ],
            "Escaped backslash in regexp" => [
                '/\\\\a/',
                [
                    TokenType::SLASH,
                    TokenType::BACKSLASH,
                    TokenType::BACKSLASH,
                    TokenType::UNESCAPED,
                    TokenType::REGEXP_MOD,
                    TokenType::EOI,
                ],
            ],
            "Escaped slash in regexp" => [
                '/\\/a/',
                [
                    TokenType::SLASH,
                    TokenType::BACKSLASH,
                    TokenType::SLASH,
                    TokenType::UNESCAPED,
                    TokenType::REGEXP_MOD,
                    TokenType::EOI,
                ],
            ],
            "Escaped arbitrary symbol in regexp" => [
                '/\\ab/',
                [
                    TokenType::SLASH,
                    TokenType::BACKSLASH,
                    TokenType::UNESCAPED,
                    TokenType::UNESCAPED,
                    TokenType::REGEXP_MOD,
                    TokenType::EOI,
                ],
            ],
            "Single white-space" => [' ', [TokenType::WS, TokenType::EOI]],
            "Multiple white-space" => [" \t\f", [TokenType::WS, TokenType::EOI]],
            "Single zero integer" => ['0', [TokenType::INT, TokenType::EOI]],
            "Single-symbol non-zero integer" => ['1', [TokenType::INT, TokenType::EOI]],
            "Multi-symbol non-zero integer" => ['1203', [TokenType::INT, TokenType::EOI]],
            "Two zero integers" => ['00', [TokenType::INT, TokenType::INT, TokenType::EOI]],
            "Non-zero integer after zero integer" => ['01', [TokenType::INT, TokenType::INT, TokenType::EOI]],
        ];
    }

    /**
     * @param string $input
     * @dataProvider providerInvalidInputTokenTypeList
     */
    public function testMatch_TokenReaderUsedToMatchAllTokensFromInvalidInput_ReturnsFalse(string $input): void
    {
        $matcher = new TokenMatcher();
        $buffer = CharBufferFactory::createFromString($input);
        $tokenFactory = new TokenFactory(TokenType::EOI);
        $actualValue = $matcher->match($buffer, $tokenFactory);
        self::assertFalse($actualValue);
    }

    public function providerInvalidInputTokenTypeList(): array
    {
        return [
            "Empty input" => [''],
            "Unused symbol outside of string" => ['#'],
            "Broken operator =" => ['=.'],
            "Broken operator = at EOI" => ['='],
            "Broken operator AND" => ['&.'],
            "Broken operator AND at EOI" => ['&'],
            "Broken operator OR" => ['|.'],
            "Broken operator OR at EOI" => ['|'],
        ];
    }
}
