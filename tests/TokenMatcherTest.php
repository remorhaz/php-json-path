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
            [
                '$.a.b[?]',
                [
                    TokenType::ROOT,
                    TokenType::DOT,
                    TokenType::NAME,
                    TokenType::DOT,
                    TokenType::NAME,
                    TokenType::LEFT_SQUARE_BRACKET,
                    TokenType::QUESTION,
                    TokenType::RIGHT_SQUARE_BRACKET,
                    TokenType::EOI,
                ],
            ],
            [
                '$[\'a\\\'bc\'].length()',
                [
                    TokenType::ROOT,
                    TokenType::LEFT_SQUARE_BRACKET,
                    TokenType::SINGLE_QUOTE,
                    TokenType::UNESCAPED,
                    TokenType::BACKSLASH,
                    TokenType::SINGLE_QUOTE,
                    TokenType::UNESCAPED,
                    TokenType::SINGLE_QUOTE,
                    TokenType::RIGHT_SQUARE_BRACKET,
                    TokenType::DOT,
                    TokenType::NAME,
                    TokenType::LEFT_BRACKET,
                    TokenType::RIGHT_BRACKET,
                    TokenType::EOI,
                ],
            ],
            [
                '$..abc',
                [
                    TokenType::ROOT,
                    TokenType::DOUBLE_DOT,
                    TokenType::NAME,
                    TokenType::EOI,
                ],
            ],
            [
                '$[?(@.x =~ /ab\\/c.*/i)]',
                [
                    TokenType::ROOT,
                    TokenType::LEFT_SQUARE_BRACKET,
                    TokenType::QUESTION,
                    TokenType::LEFT_BRACKET,
                    TokenType::SELF,
                    TokenType::DOT,
                    TokenType::NAME,
                    TokenType::WS,
                    TokenType::OP_REGEX,
                    TokenType::WS,
                    TokenType::SLASH,
                    TokenType::UNESCAPED,
                    TokenType::BACKSLASH,
                    TokenType::SLASH,
                    TokenType::UNESCAPED,
                    TokenType::SLASH,
                    TokenType::NAME,
                    TokenType::RIGHT_BRACKET,
                    TokenType::RIGHT_SQUARE_BRACKET,
                    TokenType::EOI,
                ],
            ],
        ];
    }
}
