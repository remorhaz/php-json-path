<?php
/**
 * @var \Remorhaz\UniLex\Lexer\TokenMatcherContextInterface $context
 * @lexHeader
 * @lexTargetClass Remorhaz\JSON\Path\TokenMatcher
 */

use Remorhaz\JSON\Path\TokenType;
use Remorhaz\UniLex\Lexer\TokenMatcherInterface;

/**
 * @lexToken /\./
 */
$context->setNewToken(TokenType::DOT);

/**
 * @lexToken /\.{2}/
 */
$context->setNewToken(TokenType::DOUBLE_DOT);

/**
 * @lexToken /\(/
 */
$context->setNewToken(TokenType::LEFT_BRACKET);

/**
 * @lexToken /\)/
 */
$context->setNewToken(TokenType::RIGHT_BRACKET);

/**
 * @lexToken /\[/
 */
$context->setNewToken(TokenType::LEFT_SQUARE_BRACKET);

/**
 * @lexToken /]/
 */
$context->setNewToken(TokenType::RIGHT_SQUARE_BRACKET);

/**
 * @lexToken /\*()/
 */
$context->setNewToken(TokenType::STAR);

/**
 * @lexToken /,/
 */
$context->setNewToken(TokenType::COMMA);

/**
 * @lexToken /\?/
 */
$context->setNewToken(TokenType::QUESTION);

/**
 * @lexToken /:/
 */
$context->setNewToken(TokenType::COLON);

/**
 * @lexToken /0|[1-9][0-9]*()/
 */
$context->setNewToken(TokenType::INT);

/**
 * @lexToken /-/
 */
$context->setNewToken(TokenType::HYPHEN);

/**
 * @lexToken /[a-zA-Z_\$@][a-zA-Z_\$@0-9]*()/
 */
$context
    ->setNewToken(TokenType::NAME)
    ->setTokenAttribute('text', $context->getSymbolString());

/**
 * @lexToken /'/
 */
$context
    ->setNewToken(TokenType::SINGLE_QUOTE)
    ->setMode('sqString');

/**
 * @lexToken /'/
 * @lexMode sqString
 */
$context
    ->setNewToken(TokenType::SINGLE_QUOTE)
    ->setMode(TokenMatcherInterface::DEFAULT_MODE);

/**
 * @lexToken /[^'\\]+/
 * @lexMode sqString
 */
$context
    ->setNewToken(TokenType::UNESCAPED)
    ->setTokenAttribute('text', $context->getSymbolString());

/**
 * @lexToken /\\/
 * @lexMode sqString
 */
$context
    ->setNewToken(TokenType::BACKSLASH)
    ->setMode('sqEscape');

/**
 * @lexToken /\\/
 * @lexMode sqEscape
 */
$context
    ->setNewToken(TokenType::BACKSLASH)
    ->setMode('sqString');


/**
 * @lexToken /'/
 * @lexMode sqEscape
 */
$context
    ->setNewToken(TokenType::SINGLE_QUOTE)
    ->setMode('sqString');

/**
 * @lexToken /"/
 */
$context
    ->setNewToken(TokenType::DOUBLE_QUOTE)
    ->setMode('dqString');

/**
 * @lexToken /"/
 * @lexMode dqString
 */
$context
    ->setNewToken(TokenType::DOUBLE_QUOTE)
    ->setMode(TokenMatcherInterface::DEFAULT_MODE);

/**
 * @lexToken /[^"\\]+/
 * @lexMode dqString
 */
$context
    ->setNewToken(TokenType::UNESCAPED)
    ->setTokenAttribute('text', $context->getSymbolString());

/**
 * @lexToken /\\/
 * @lexMode dqString
 */
$context
    ->setNewToken(TokenType::BACKSLASH)
    ->setMode('dqEscape');

/**
 * @lexToken /\\/
 * @lexMode dqEscape
 */
$context
    ->setNewToken(TokenType::BACKSLASH)
    ->setMode('dqString');

/**
 * @lexToken /"/
 * @lexMode dqEscape
 */
$context
    ->setNewToken(TokenType::DOUBLE_QUOTE)
    ->setMode('dqString');

/**
 * @lexToken /==/
 */
$context->setNewToken(TokenType::OP_EQ);

/**
 * @lexToken /!=/
 */
$context->setNewToken(TokenType::OP_NEQ);

/**
 * @lexToken /</
 */
$context->setNewToken(TokenType::OP_L);

/**
 * @lexToken /<=/
 */
$context->setNewToken(TokenType::OP_LE);

/**
 * @lexToken />/
 */
$context->setNewToken(TokenType::OP_G);

/**
 * @lexToken />=/
 */
$context->setNewToken(TokenType::OP_GE);

/**
 * @lexToken /=~/
 */
$context->setNewToken(TokenType::OP_REGEX);

/**
 * @lexToken /\//
 */
$context
    ->setNewToken(TokenType::SLASH)
    ->setMode('reString');

/**
 * @lexToken /\//
 * @lexMode reString
 */
$context
    ->setNewToken(TokenType::SLASH)
    ->setMode(TokenMatcherInterface::DEFAULT_MODE);

/**
 * @lexToken /[^/\\]*()/
 * @lexMode reString
 */
$context
    ->setNewToken(TokenType::UNESCAPED)
    ->setTokenAttribute('text', $context->getSymbolString());

/**
 * @lexToken /\\/
 * @lexMode reString
 */
$context
    ->setNewToken(TokenType::BACKSLASH)
    ->setMode('reEscape');

/**
 * @lexToken /\\/
 * @lexMode reEscape
 */
$context
    ->setNewToken(TokenType::BACKSLASH)
    ->setMode('reString');

/**
 * @lexToken /\//
 * @lexMode reEscape
 */
$context
    ->setNewToken(TokenType::SLASH)
    ->setMode('reString');

/**
 * @lexToken /[^/\\]/
 * @lexMode reEscape
 */
$context
    ->setNewToken(TokenType::UNESCAPED)
    ->setTokenAttribute('text', $context->getSymbolString())
    ->setMode('reString');

/**
 * @lexToken /[\u0009\u000B\u000C\u0020\u00A0]+/
 */
$context->setNewToken(TokenType::WS);

/**
 * @lexToken /\|\|/
 */
$context->setNewToken(TokenType::OP_OR);

/**
 * @lexToken /&&/
 */
$context->setNewToken(TokenType::OP_AND);
