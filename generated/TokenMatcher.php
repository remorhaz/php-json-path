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
        if ($context->getMode() == 'sqString') {
            goto stateSqString1;
        }
        if ($context->getMode() == 'sqEscape') {
            goto stateSqEscape1;
        }
        if ($context->getMode() == 'dqString') {
            goto stateDqString1;
        }
        if ($context->getMode() == 'dqEscape') {
            goto stateDqEscape1;
        }
        if ($context->getMode() == 'reString') {
            goto stateReString1;
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
        if (0x2E == $char) {
            $context->getBuffer()->nextSymbol();
            goto state2;
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
            $context
                ->setNewToken(TokenType::INT)
                ->setTokenAttribute('text', $context->getSymbolString());
            return true;
        }
        if (0x31 <= $char && $char <= 0x39) {
            $context->getBuffer()->nextSymbol();
            goto state12;
        }
        if (0x2D == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::HYPHEN);
            return true;
        }
        if (0x24 == $char || 0x40 <= $char && $char <= 0x5A || 0x5F == $char || 0x61 <= $char && $char <= 0x7A) {
            $context->getBuffer()->nextSymbol();
            goto state14;
        }
        if (0x27 == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::SINGLE_QUOTE)
                ->setMode('sqString');
            return true;
        }
        if (0x22 == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::DOUBLE_QUOTE)
                ->setMode('dqString');
            return true;
        }
        if (0x3D == $char) {
            $context->getBuffer()->nextSymbol();
            goto state17;
        }
        if (0x21 == $char) {
            $context->getBuffer()->nextSymbol();
            goto state18;
        }
        if (0x3C == $char) {
            $context->getBuffer()->nextSymbol();
            goto state19;
        }
        if (0x3E == $char) {
            $context->getBuffer()->nextSymbol();
            goto state20;
        }
        if (0x2F == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::SLASH)
                ->setMode('reString');
            return true;
        }
        if (0x09 == $char || 0x0B == $char || 0x0C == $char || 0x20 == $char || 0xA0 == $char) {
            $context->getBuffer()->nextSymbol();
            goto state22;
        }
        if (0x7C == $char) {
            $context->getBuffer()->nextSymbol();
            goto state23;
        }
        if (0x26 == $char) {
            $context->getBuffer()->nextSymbol();
            goto state24;
        }
        goto error;

        state2:
        if ($context->getBuffer()->isEnd()) {
            goto finish2;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x2E == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::DOUBLE_DOT);
            return true;
        }
        finish2:
        $context->setNewToken(TokenType::DOT);
        return true;

        state12:
        if ($context->getBuffer()->isEnd()) {
            goto finish12;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x30 == $char) {
            $context->getBuffer()->nextSymbol();
            goto state34;
        }
        if (0x31 <= $char && $char <= 0x39) {
            $context->getBuffer()->nextSymbol();
            goto state34;
        }
        finish12:
        $context
            ->setNewToken(TokenType::INT)
            ->setTokenAttribute('text', $context->getSymbolString());
        return true;

        state14:
        if ($context->getBuffer()->isEnd()) {
            goto finish14;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x30 == $char) {
            $context->getBuffer()->nextSymbol();
            goto state33;
        }
        if (0x31 <= $char && $char <= 0x39) {
            $context->getBuffer()->nextSymbol();
            goto state33;
        }
        if (0x24 == $char || 0x40 <= $char && $char <= 0x5A || 0x5F == $char || 0x61 <= $char && $char <= 0x7A) {
            $context->getBuffer()->nextSymbol();
            goto state33;
        }
        finish14:
        $context
            ->setNewToken(TokenType::NAME)
            ->setTokenAttribute('text', $context->getSymbolString());
        return true;

        state17:
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

        state18:
        if ($context->getBuffer()->isEnd()) {
            goto finish18;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x3D == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::OP_NEQ);
            return true;
        }
        finish18:
        $context->setNewToken(TokenType::OP_NOT);
        return true;

        state19:
        if ($context->getBuffer()->isEnd()) {
            goto finish19;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x3D == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::OP_LE);
            return true;
        }
        finish19:
        $context->setNewToken(TokenType::OP_L);
        return true;

        state20:
        if ($context->getBuffer()->isEnd()) {
            goto finish20;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x3D == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::OP_GE);
            return true;
        }
        finish20:
        $context->setNewToken(TokenType::OP_G);
        return true;

        state22:
        if ($context->getBuffer()->isEnd()) {
            goto finish22;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x09 == $char || 0x0B == $char || 0x0C == $char || 0x20 == $char || 0xA0 == $char) {
            $context->getBuffer()->nextSymbol();
            goto state27;
        }
        finish22:
        $context->setNewToken(TokenType::WS);
        return true;

        state23:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x7C == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::OP_OR);
            return true;
        }
        goto error;

        state24:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x26 == $char) {
            $context->getBuffer()->nextSymbol();
            $context->setNewToken(TokenType::OP_AND);
            return true;
        }
        goto error;

        state27:
        if ($context->getBuffer()->isEnd()) {
            goto finish27;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x09 == $char || 0x0B == $char || 0x0C == $char || 0x20 == $char || 0xA0 == $char) {
            $context->getBuffer()->nextSymbol();
            goto state27;
        }
        finish27:
        $context->setNewToken(TokenType::WS);
        return true;

        state33:
        if ($context->getBuffer()->isEnd()) {
            goto finish33;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x30 == $char) {
            $context->getBuffer()->nextSymbol();
            goto state33;
        }
        if (0x31 <= $char && $char <= 0x39) {
            $context->getBuffer()->nextSymbol();
            goto state33;
        }
        if (0x24 == $char || 0x40 <= $char && $char <= 0x5A || 0x5F == $char || 0x61 <= $char && $char <= 0x7A) {
            $context->getBuffer()->nextSymbol();
            goto state33;
        }
        finish33:
        $context
            ->setNewToken(TokenType::NAME)
            ->setTokenAttribute('text', $context->getSymbolString());
        return true;

        state34:
        if ($context->getBuffer()->isEnd()) {
            goto finish34;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x30 == $char) {
            $context->getBuffer()->nextSymbol();
            goto state34;
        }
        if (0x31 <= $char && $char <= 0x39) {
            $context->getBuffer()->nextSymbol();
            goto state34;
        }
        finish34:
        $context
            ->setNewToken(TokenType::INT)
            ->setTokenAttribute('text', $context->getSymbolString());
        return true;

        stateSqString1:
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
            goto stateSqString3;
        }
        if (0x5C == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::BACKSLASH)
                ->setMode('sqEscape');
            return true;
        }
        goto error;

        stateSqString3:
        if ($context->getBuffer()->isEnd()) {
            goto finishSqString3;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x00 <= $char && $char <= 0x26 || 0x28 <= $char && $char <= 0x5B || 0x5D <= $char && $char <= 0x10FFFF) {
            $context->getBuffer()->nextSymbol();
            goto stateSqString5;
        }
        finishSqString3:
        $context
            ->setNewToken(TokenType::UNESCAPED)
            ->setTokenAttribute('text', $context->getSymbolString());
        return true;

        stateSqString5:
        if ($context->getBuffer()->isEnd()) {
            goto finishSqString5;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x00 <= $char && $char <= 0x26 || 0x28 <= $char && $char <= 0x5B || 0x5D <= $char && $char <= 0x10FFFF) {
            $context->getBuffer()->nextSymbol();
            goto stateSqString5;
        }
        finishSqString5:
        $context
            ->setNewToken(TokenType::UNESCAPED)
            ->setTokenAttribute('text', $context->getSymbolString());
        return true;

        stateSqEscape1:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x5C == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::BACKSLASH)
                ->setMode('sqString');
            return true;
        }
        if (0x27 == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::SINGLE_QUOTE)
                ->setMode('sqString');
            return true;
        }
        goto error;

        stateDqString1:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x22 == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::DOUBLE_QUOTE)
                ->setMode(TokenMatcherInterface::DEFAULT_MODE);
            return true;
        }
        if (0x00 <= $char && $char <= 0x21 || 0x23 <= $char && $char <= 0x5B || 0x5D <= $char && $char <= 0x10FFFF) {
            $context->getBuffer()->nextSymbol();
            goto stateDqString3;
        }
        if (0x5C == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::BACKSLASH)
                ->setMode('dqEscape');
            return true;
        }
        goto error;

        stateDqString3:
        if ($context->getBuffer()->isEnd()) {
            goto finishDqString3;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x00 <= $char && $char <= 0x21 || 0x23 <= $char && $char <= 0x5B || 0x5D <= $char && $char <= 0x10FFFF) {
            $context->getBuffer()->nextSymbol();
            goto stateDqString5;
        }
        finishDqString3:
        $context
            ->setNewToken(TokenType::UNESCAPED)
            ->setTokenAttribute('text', $context->getSymbolString());
        return true;

        stateDqString5:
        if ($context->getBuffer()->isEnd()) {
            goto finishDqString5;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x00 <= $char && $char <= 0x21 || 0x23 <= $char && $char <= 0x5B || 0x5D <= $char && $char <= 0x10FFFF) {
            $context->getBuffer()->nextSymbol();
            goto stateDqString5;
        }
        finishDqString5:
        $context
            ->setNewToken(TokenType::UNESCAPED)
            ->setTokenAttribute('text', $context->getSymbolString());
        return true;

        stateDqEscape1:
        if ($context->getBuffer()->isEnd()) {
            goto error;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x5C == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::BACKSLASH)
                ->setMode('dqString');
            return true;
        }
        if (0x22 == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::DOUBLE_QUOTE)
                ->setMode('dqString');
            return true;
        }
        goto error;

        stateReString1:
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
            goto stateReString3;
        }
        if (0x5C == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::BACKSLASH)
                ->setMode('reEscape');
            return true;
        }
        goto error;

        stateReString3:
        if ($context->getBuffer()->isEnd()) {
            goto finishReString3;
        }
        $char = $context->getBuffer()->getSymbol();
        if (0x00 <= $char && $char <= 0x2E || 0x30 <= $char && $char <= 0x5B || 0x5D <= $char && $char <= 0x10FFFF) {
            $context->getBuffer()->nextSymbol();
            goto stateReString3;
        }
        finishReString3:
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
                ->setMode('reString');
            return true;
        }
        if (0x2F == $char) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::SLASH)
                ->setMode('reString');
            return true;
        }
        if (0x00 <= $char && $char <= 0x2E || 0x30 <= $char && $char <= 0x5B || 0x5D <= $char && $char <= 0x10FFFF) {
            $context->getBuffer()->nextSymbol();
            $context
                ->setNewToken(TokenType::UNESCAPED)
                ->setTokenAttribute('text', $context->getSymbolString())
                ->setMode('reString');
            return true;
        }
        goto error;

        error:
        return false;
    }
}
