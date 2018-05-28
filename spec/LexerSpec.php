<?php
/**
 * @var \Remorhaz\UniLex\Lexer\TokenMatcherContextInterface $context
 * @lexHeader
 * @lexTargetClass TokenMatcher
 */

use Remorhaz\JSON\Path\TokenType;

/**
 * @lexToken /\$/
 */
$context->setNewToken(TokenType::ROOT);

/**
 * @lexToken /\./
 */
$context->setNewToken(TokenType::DOT);

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
