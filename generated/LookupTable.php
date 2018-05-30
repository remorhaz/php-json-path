<?php
/**
 * JSONPath parser LL(1) lookup table.
 *
 * Auto-generated file, please don't edit manually.
 * Run following command to update this file:
 *     vendor/bin/phing json-path-lookup
 *
 * Phing version: 2.16.1
 */

use Remorhaz\JSON\Path\SymbolType;
use Remorhaz\JSON\Path\TokenType;

return [
    SymbolType::NT_JSON_PATH => [
        TokenType::NAME => 0,
    ],
    SymbolType::NT_PATH => [
        TokenType::NAME => 0,
    ],
    SymbolType::NT_FILTER_LIST => [
        TokenType::DOT => 0,
        TokenType::DOUBLE_DOT => 1,
        TokenType::LEFT_SQUARE_BRACKET => 2,
        TokenType::EOI => 3,
    ],
    SymbolType::NT_DOT_FILTER => [
        TokenType::NAME => 0,
        TokenType::STAR => 1,
    ],
    SymbolType::NT_DOT_FILTER_NEXT => [
        TokenType::LEFT_BRACKET => 0,
        TokenType::DOT => 1,
        TokenType::DOUBLE_DOT => 1,
        TokenType::LEFT_SQUARE_BRACKET => 1,
        TokenType::EOI => 1,
    ],
    SymbolType::NT_NAME => [
        TokenType::STAR => 0,
        TokenType::NAME => 1,
    ],
    SymbolType::NT_BRACKET_FILTER => [
        TokenType::STAR => 0,
        TokenType::HYPHEN => 2,
        TokenType::INT => 2,
        TokenType::COLON => 3,
        TokenType::LEFT_BRACKET => 4,
        TokenType::QUESTION => 5,
    ],
    SymbolType::NT_STRING_NEXT => [
        TokenType::COMMA => 0,
        TokenType::RIGHT_SQUARE_BRACKET => 1,
    ],
    SymbolType::NT_INT_NEXT => [
        TokenType::WS => 0,
        TokenType::COMMA => 0,
        TokenType::RIGHT_SQUARE_BRACKET => 0,
        TokenType::COLON => 1,
    ],
    SymbolType::NT_INT_NEXT_LIST => [
        TokenType::COMMA => 0,
        TokenType::RIGHT_SQUARE_BRACKET => 1,
    ],
    SymbolType::NT_INT_SLICE => [
        TokenType::COLON => 0,
    ],
    SymbolType::NT_INT_SLICE_STEP => [
        TokenType::COLON => 0,
        TokenType::WS => 1,
        TokenType::RIGHT_SQUARE_BRACKET => 1,
    ],
    SymbolType::NT_WS_OPT => [
        TokenType::WS => 0,
        TokenType::STAR => 1,
        TokenType::LEFT_BRACKET => 1,
        TokenType::QUESTION => 1,
        TokenType::HYPHEN => 1,
        TokenType::INT => 1,
        TokenType::COLON => 1,
        TokenType::RIGHT_SQUARE_BRACKET => 1,
        TokenType::COMMA => 1,
    ],
    SymbolType::NT_INT => [
        TokenType::HYPHEN => 0,
        TokenType::INT => 1,
    ],
    SymbolType::NT_INT_OPT => [
        TokenType::HYPHEN => 0,
        TokenType::INT => 0,
        TokenType::COLON => 1,
        TokenType::WS => 1,
        TokenType::RIGHT_SQUARE_BRACKET => 1,
    ],
];
