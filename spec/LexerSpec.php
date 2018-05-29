<?php
/**
 * @var \Remorhaz\UniLex\Lexer\TokenMatcherContextInterface $context
 * @lexHeader
 * @lexTargetClass Remorhaz\JSON\Path\TokenMatcher
 */

use Remorhaz\JSON\Path\TokenType;
use Remorhaz\UniLex\Lexer\TokenMatcherInterface;

/**
 * @lexToken /\$/
 */
$context->setNewToken(TokenType::ROOT);

/**
 * @lexToken /\./
 */
$context->setNewToken(TokenType::DOT);

/**
 * @lexToken /\.{2}/
 */
$context->setNewToken(TokenType::DOUBLE_DOT);

/**
 * @lexToken /@/
 */
$context->setNewToken(TokenType::SELF);

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
 * @lexToken /[a-zA-Z_][a-zA-Z_0-9]*()/
 */
$context
    ->setNewToken(TokenType::NAME)
    ->setTokenAttribute('text', $context->getSymbolString());

/**
 * @lexToken /'/
 */
$context
    ->setNewToken(TokenType::SINGLE_QUOTE)
    ->setMode('string');

/**
 * @lexToken /'/
 * @lexMode string
 */
$context
    ->setNewToken(TokenType::SINGLE_QUOTE)
    ->setMode(TokenMatcherInterface::DEFAULT_MODE);

/**
 * @lexToken /[^'\\]+/
 * @lexMode string
 */
$context
    ->setNewToken(TokenType::UNESCAPED)
    ->setTokenAttribute('text', $context->getSymbolString());

/**
 * @lexToken /\\/
 * @lexMode string
 */
$context
    ->setNewToken(TokenType::BACKSLASH)
    ->setMode('escape');

/**
 * @lexToken /\\/
 * @lexMode escape
 */
$context
    ->setNewToken(TokenType::BACKSLASH)
    ->setMode('string');


/**
 * @lexToken /'/
 * @lexMode escape
 */
$context
    ->setNewToken(TokenType::SINGLE_QUOTE)
    ->setMode('string');

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
    ->setMode('regexp');

/**
 * @lexToken /\//
 * @lexMode regexp
 */
$context
    ->setNewToken(TokenType::SLASH)
    ->setMode(TokenMatcherInterface::DEFAULT_MODE);

/**
 * @lexToken /[^/\\]*()/
 * @lexMode regexp
 */
$context
    ->setNewToken(TokenType::UNESCAPED)
    ->setTokenAttribute('text', $context->getSymbolString());

/**
 * @lexToken /\\/
 * @lexMode regexp
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
    ->setMode('regexp');

/**
 * @lexToken /\//
 * @lexMode reEscape
 */
$context
    ->setNewToken(TokenType::SLASH)
    ->setMode('regexp');

/**
 * @lexToken /[^/\\]/
 * @lexMode reEscape
 */
$context
    ->setNewToken(TokenType::UNESCAPED)
    ->setTokenAttribute('text', $context->getSymbolString())
    ->setMode('regexp');

/**
 * @lexToken /[\u0009\u000B\u000C\u0020\u00A0]+/
 */
$context
    ->setNewToken(TokenType::WS);
