<?php
/**
 * JSONPath token matcher.
 *
 * Auto-generated file, please don't edit manually.
 * Run following command to update this file:
 *     vendor/bin/phing json-path-matcher
 *
 * Phing version: 2.16.1
 */

use Remorhaz\JSON\Path\TokenType;
use Remorhaz\UniLex\IO\CharBufferInterface;
use Remorhaz\UniLex\Lexer\TokenFactoryInterface;
use Remorhaz\UniLex\Lexer\TokenMatcherTemplate;

class TokenMatcher extends TokenMatcherTemplate
{

    public function match(CharBufferInterface $buffer, TokenFactoryInterface $tokenFactory): bool
    {
        $context = $this->createContext($buffer, $tokenFactory);
        goto state1;

        state1:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x24 == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::ROOT);
            return true;
        }
        if (0x2E == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::DOT);
            return true;
        }
        if (0x40 == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::SELF);
            return true;
        }
        if (0x28 == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::LEFT_BRACKET);
            return true;
        }
        if (0x29 == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::RIGHT_BRACKET);
            return true;
        }
        if (0x5B == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::LEFT_SQUARE_BRACKET);
            return true;
        }
        if (0x5D == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::RIGHT_SQUARE_BRACKET);
            return true;
        }
        if (0x2A == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::STAR);
            return true;
        }
        if (0x2C == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::COMMA);
            return true;
        }
        if (0x3F == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::QUESTION);
            return true;
        }
        if (0x3A == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::COLON);
            return true;
        }
        if (0x30 == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::INT);
            return true;
        }
        if (0x31 <= $char && $char <= 0x39) {
            $context->getBuffer()->nextSymbol();
            goto state14;
        }
        if (0x2D == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::HYPHEN);
            return true;
        }
        goto error;

        state14:
        if ($context->getBuffer()->isEnd()) {
            goto finish14;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x30 == $char) {
            $context->getBuffer()->nextSymbol();
            goto state16;
        }
        if (0x31 <= $char && $char <= 0x39) {
            $context->getBuffer()->nextSymbol();
            goto state16;
        }
        finish14:
        $context->setNewToken(TokenType::INT);
        return true;

        state16:
        if ($context->getBuffer()->isEnd()) {
            goto finish16;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x30 == $char) {
            $context->getBuffer()->nextSymbol();
            goto state16;
        }
        if (0x31 <= $char && $char <= 0x39) {
            $context->getBuffer()->nextSymbol();
            goto state16;
        }
        finish16:
        $context->setNewToken(TokenType::INT);
        return true;

        error:
        return false;
    }
}
