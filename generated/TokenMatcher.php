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

namespace Remorhaz\JSON\Path;

use Remorhaz\UniLex\IO\CharBufferInterface;
use Remorhaz\UniLex\Lexer\TokenFactoryInterface;
use Remorhaz\UniLex\Lexer\TokenMatcherInterface;
use Remorhaz\UniLex\Lexer\TokenMatcherTemplate;

class TokenMatcher extends TokenMatcherTemplate
{

    public function match(CharBufferInterface $buffer, TokenFactoryInterface $tokenFactory): bool
    {
        $context = $this->createContext($buffer, $tokenFactory);
        if ($context->getMode() == 'string') {
            goto stateString1;
        }
        if ($context->getMode() == 'escape') {
            goto stateEscape1;
        }
        if ($context->getMode() == 'regexp') {
            goto stateRegexp1;
        }
        if ($context->getMode() == 'reEscape') {
            goto stateReEscape1;
        }
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
            goto state3;
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
        if (0x41 <= $char && $char <= 0x5A || 0x5F == $char || 0x61 <= $char && $char <= 0x7A) {
            $context->getBuffer()->nextSymbol();
            goto state16;
        }
        if (0x27 == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::SINGLE_QUOTE)
                ->setMode('string');
            return true;
        }
        if (0x3D == $char) {
            $context->getBuffer()->nextSymbol();
            goto state18;
        }
        if (0x21 == $char) {
            $context->getBuffer()->nextSymbol();
            goto state19;
        }
        if (0x3C == $char) {
            $context->getBuffer()->nextSymbol();
            goto state20;
        }
        if (0x3E == $char) {
            $context->getBuffer()->nextSymbol();
            goto state21;
        }
        if (0x2F == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::SLASH)
                ->setMode('regexp');
            return true;
        }
        if (0x09 == $char || 0x0B == $char || 0x0C == $char || 0x20 == $char || 0xA0 == $char) {
            $context->getBuffer()->nextSymbol();
            goto state23;
        }
        goto error;

        state3:
        if ($context->getBuffer()->isEnd()) {
            goto finish3;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x2E == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::DOUBLE_DOT);
            return true;
        }
        finish3:
        $context->setNewToken(TokenType::DOT);
        return true;

        state14:
        if ($context->getBuffer()->isEnd()) {
            goto finish14;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x30 == $char) {
            $context->getBuffer()->nextSymbol();
            goto state31;
        }
        if (0x31 <= $char && $char <= 0x39) {
            $context->getBuffer()->nextSymbol();
            goto state31;
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
            goto state30;
        }
        if (0x31 <= $char && $char <= 0x39) {
            $context->getBuffer()->nextSymbol();
            goto state30;
        }
        if (0x41 <= $char && $char <= 0x5A || 0x5F == $char || 0x61 <= $char && $char <= 0x7A) {
            $context->getBuffer()->nextSymbol();
            goto state30;
        }
        finish16:
        $context
            ->setNewToken(TokenType::NAME)
            ->setTokenAttribute('text', $context->getSymbolString());
        return true;

        state18:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x3D == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::OP_EQ);
            return true;
        }
        if (0x7E == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::OP_REGEX);
            return true;
        }
        goto error;

        state19:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x3D == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::OP_NEQ);
            return true;
        }
        goto error;

        state20:
        if ($context->getBuffer()->isEnd()) {
            goto finish20;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x3D == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::OP_LE);
            return true;
        }
        finish20:
        $context->setNewToken(TokenType::OP_L);
        return true;

        state21:
        if ($context->getBuffer()->isEnd()) {
            goto finish21;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x3D == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::OP_GE);
            return true;
        }
        finish21:
        $context->setNewToken(TokenType::OP_G);
        return true;

        state23:
        if ($context->getBuffer()->isEnd()) {
            goto finish23;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x09 == $char || 0x0B == $char || 0x0C == $char || 0x20 == $char || 0xA0 == $char) {
            $context->getBuffer()->nextSymbol();
            goto state24;
        }
        finish23:
        $context
            ->setNewToken(TokenType::WS);
        return true;

        state24:
        if ($context->getBuffer()->isEnd()) {
            goto finish24;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x09 == $char || 0x0B == $char || 0x0C == $char || 0x20 == $char || 0xA0 == $char) {
            $context->getBuffer()->nextSymbol();
            goto state24;
        }
        finish24:
        $context
            ->setNewToken(TokenType::WS);
        return true;

        state30:
        if ($context->getBuffer()->isEnd()) {
            goto finish30;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x30 == $char) {
            $context->getBuffer()->nextSymbol();
            goto state30;
        }
        if (0x31 <= $char && $char <= 0x39) {
            $context->getBuffer()->nextSymbol();
            goto state30;
        }
        if (0x41 <= $char && $char <= 0x5A || 0x5F == $char || 0x61 <= $char && $char <= 0x7A) {
            $context->getBuffer()->nextSymbol();
            goto state30;
        }
        finish30:
        $context
            ->setNewToken(TokenType::NAME)
            ->setTokenAttribute('text', $context->getSymbolString());
        return true;

        state31:
        if ($context->getBuffer()->isEnd()) {
            goto finish31;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x30 == $char) {
            $context->getBuffer()->nextSymbol();
            goto state31;
        }
        if (0x31 <= $char && $char <= 0x39) {
            $context->getBuffer()->nextSymbol();
            goto state31;
        }
        finish31:
        $context->setNewToken(TokenType::INT);
        return true;

        stateString1:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x27 == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::SINGLE_QUOTE)
                ->setMode(TokenMatcherInterface::DEFAULT_MODE);
            return true;
        }
        if (0x00 <= $char && $char <= 0x26 || 0x28 <= $char && $char <= 0x5B || 0x5D <= $char && $char <= 0x10FFFF) {
            $context->getBuffer()->nextSymbol();
            goto stateString3;
        }
        if (0x5C == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::BACKSLASH)
                ->setMode('escape');
            return true;
        }
        goto error;

        stateString3:
        if ($context->getBuffer()->isEnd()) {
            goto finishString3;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x00 <= $char && $char <= 0x26 || 0x28 <= $char && $char <= 0x5B || 0x5D <= $char && $char <= 0x10FFFF) {
            $context->getBuffer()->nextSymbol();
            goto stateString5;
        }
        finishString3:
        $context
            ->setNewToken(TokenType::UNESCAPED)
            ->setTokenAttribute('text', $context->getSymbolString());
        return true;

        stateString5:
        if ($context->getBuffer()->isEnd()) {
            goto finishString5;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x00 <= $char && $char <= 0x26 || 0x28 <= $char && $char <= 0x5B || 0x5D <= $char && $char <= 0x10FFFF) {
            $context->getBuffer()->nextSymbol();
            goto stateString5;
        }
        finishString5:
        $context
            ->setNewToken(TokenType::UNESCAPED)
            ->setTokenAttribute('text', $context->getSymbolString());
        return true;

        stateEscape1:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x5C == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::BACKSLASH)
                ->setMode('string');
            return true;
        }
        if (0x27 == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::SINGLE_QUOTE)
                ->setMode('string');
            return true;
        }
        goto error;

        stateRegexp1:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x2F == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::SLASH)
                ->setMode(TokenMatcherInterface::DEFAULT_MODE);
            return true;
        }
        if (0x00 <= $char && $char <= 0x2E || 0x30 <= $char && $char <= 0x5B || 0x5D <= $char && $char <= 0x10FFFF) {
            $context->getBuffer()->nextSymbol();
            goto stateRegexp3;
        }
        if (0x5C == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::BACKSLASH)
                ->setMode('reEscape');
            return true;
        }
        goto error;

        stateRegexp3:
        if ($context->getBuffer()->isEnd()) {
            goto finishRegexp3;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x00 <= $char && $char <= 0x2E || 0x30 <= $char && $char <= 0x5B || 0x5D <= $char && $char <= 0x10FFFF) {
            $context->getBuffer()->nextSymbol();
            goto stateRegexp3;
        }
        finishRegexp3:
        $context
            ->setNewToken(TokenType::UNESCAPED)
            ->setTokenAttribute('text', $context->getSymbolString());
        return true;

        stateReEscape1:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x5C == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::BACKSLASH)
                ->setMode('regexp');
            return true;
        }
        if (0x2F == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::SLASH)
                ->setMode('regexp');
            return true;
        }
        if (0x00 <= $char && $char <= 0x2E || 0x30 <= $char && $char <= 0x5B || 0x5D <= $char && $char <= 0x10FFFF) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::UNESCAPED)
                ->setTokenAttribute('text', $context->getSymbolString())
                ->setMode('regexp');
            return true;
        }
        goto error;

        error:
        return false;
    }
}
