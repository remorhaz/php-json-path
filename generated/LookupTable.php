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
        TokenType::ROOT_ABSOLUTE => 0,
        TokenType::ROOT_RELATIVE => 0,
    ],
    SymbolType::NT_PATH => [
        TokenType::ROOT_ABSOLUTE => 0,
        TokenType::ROOT_RELATIVE => 1,
    ],
    SymbolType::NT_FILTER_LIST => [
        TokenType::DOT => 0,
        TokenType::DOUBLE_DOT => 1,
        TokenType::LEFT_SQUARE_BRACKET => 2,
        TokenType::EOI => 3,
        TokenType::WS => 3,
        TokenType::OP_EQ => 3,
        TokenType::OP_NEQ => 3,
        TokenType::OP_L => 3,
        TokenType::OP_LE => 3,
        TokenType::OP_G => 3,
        TokenType::OP_GE => 3,
        TokenType::OP_REGEX => 3,
        TokenType::NAME => 3,
        TokenType::NULL => 3,
        TokenType::TRUE => 3,
        TokenType::FALSE => 3,
        TokenType::OP_AND => 3,
        TokenType::OP_OR => 3,
        TokenType::RIGHT_BRACKET => 3,
        TokenType::COMMA => 3,
        TokenType::RIGHT_SQUARE_BRACKET => 3,
    ],
    SymbolType::NT_DOT_FILTER => [
        TokenType::NAME => 0,
        TokenType::NULL => 0,
        TokenType::TRUE => 0,
        TokenType::FALSE => 0,
        TokenType::STAR => 1,
    ],
    SymbolType::NT_DOUBLE_DOT_FILTER => [
        TokenType::NAME => 0,
        TokenType::NULL => 0,
        TokenType::TRUE => 0,
        TokenType::FALSE => 0,
        TokenType::STAR => 1,
    ],
    SymbolType::NT_DOT_FILTER_NEXT => [
        TokenType::LEFT_BRACKET => 0,
        TokenType::DOT => 1,
        TokenType::DOUBLE_DOT => 1,
        TokenType::LEFT_SQUARE_BRACKET => 1,
        TokenType::EOI => 1,
        TokenType::WS => 1,
        TokenType::OP_EQ => 1,
        TokenType::OP_NEQ => 1,
        TokenType::OP_L => 1,
        TokenType::OP_LE => 1,
        TokenType::OP_G => 1,
        TokenType::OP_GE => 1,
        TokenType::OP_REGEX => 1,
        TokenType::NAME => 1,
        TokenType::NULL => 1,
        TokenType::TRUE => 1,
        TokenType::FALSE => 1,
        TokenType::OP_AND => 1,
        TokenType::OP_OR => 1,
        TokenType::RIGHT_BRACKET => 1,
        TokenType::COMMA => 1,
        TokenType::RIGHT_SQUARE_BRACKET => 1,
    ],
    SymbolType::NT_NAME => [
        TokenType::NAME => 0,
        TokenType::NULL => 1,
        TokenType::TRUE => 2,
        TokenType::FALSE => 3,
    ],
    SymbolType::NT_BRACKET_FILTER => [
        TokenType::STAR => 0,
        TokenType::SINGLE_QUOTE => 1,
        TokenType::DOUBLE_QUOTE => 1,
        TokenType::HYPHEN => 2,
        TokenType::INT => 2,
        TokenType::COLON => 3,
        TokenType::LEFT_BRACKET => 4,
        TokenType::QUESTION => 5,
    ],
    SymbolType::NT_STRING_LIST => [
        TokenType::SINGLE_QUOTE => 0,
        TokenType::DOUBLE_QUOTE => 0,
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
        TokenType::SINGLE_QUOTE => 1,
        TokenType::DOUBLE_QUOTE => 1,
        TokenType::RIGHT_SQUARE_BRACKET => 1,
        TokenType::OP_NOT => 1,
        TokenType::ROOT_ABSOLUTE => 1,
        TokenType::ROOT_RELATIVE => 1,
        TokenType::NULL => 1,
        TokenType::TRUE => 1,
        TokenType::FALSE => 1,
        TokenType::LEFT_SQUARE_BRACKET => 1,
        TokenType::COMMA => 1,
        TokenType::SLASH => 1,
        TokenType::OP_EQ => 1,
        TokenType::OP_NEQ => 1,
        TokenType::OP_L => 1,
        TokenType::OP_LE => 1,
        TokenType::OP_G => 1,
        TokenType::OP_GE => 1,
        TokenType::OP_REGEX => 1,
        TokenType::NAME => 1,
        TokenType::OP_AND => 1,
        TokenType::OP_OR => 1,
        TokenType::RIGHT_BRACKET => 1,
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
    SymbolType::NT_EXPR => [
        TokenType::OP_NOT => 0,
        TokenType::ROOT_ABSOLUTE => 0,
        TokenType::ROOT_RELATIVE => 0,
        TokenType::HYPHEN => 0,
        TokenType::INT => 0,
        TokenType::NULL => 0,
        TokenType::TRUE => 0,
        TokenType::FALSE => 0,
        TokenType::LEFT_BRACKET => 0,
        TokenType::LEFT_SQUARE_BRACKET => 0,
        TokenType::SINGLE_QUOTE => 0,
        TokenType::DOUBLE_QUOTE => 0,
    ],
    SymbolType::NT_EXPR_ARG_OR => [
        TokenType::OP_NOT => 0,
        TokenType::ROOT_ABSOLUTE => 0,
        TokenType::ROOT_RELATIVE => 0,
        TokenType::HYPHEN => 0,
        TokenType::INT => 0,
        TokenType::NULL => 0,
        TokenType::TRUE => 0,
        TokenType::FALSE => 0,
        TokenType::LEFT_BRACKET => 0,
        TokenType::LEFT_SQUARE_BRACKET => 0,
        TokenType::SINGLE_QUOTE => 0,
        TokenType::DOUBLE_QUOTE => 0,
    ],
    SymbolType::NT_EXPR_ARG_AND => [
        TokenType::OP_NOT => 0,
        TokenType::ROOT_ABSOLUTE => 0,
        TokenType::ROOT_RELATIVE => 0,
        TokenType::HYPHEN => 0,
        TokenType::INT => 0,
        TokenType::NULL => 0,
        TokenType::TRUE => 0,
        TokenType::FALSE => 0,
        TokenType::LEFT_BRACKET => 0,
        TokenType::LEFT_SQUARE_BRACKET => 0,
        TokenType::SINGLE_QUOTE => 0,
        TokenType::DOUBLE_QUOTE => 0,
    ],
    SymbolType::NT_EXPR_ARG_COMP => [
        TokenType::OP_NOT => 0,
        TokenType::ROOT_ABSOLUTE => 1,
        TokenType::ROOT_RELATIVE => 1,
        TokenType::HYPHEN => 1,
        TokenType::INT => 1,
        TokenType::NULL => 1,
        TokenType::TRUE => 1,
        TokenType::FALSE => 1,
        TokenType::LEFT_BRACKET => 1,
        TokenType::LEFT_SQUARE_BRACKET => 1,
        TokenType::SINGLE_QUOTE => 1,
        TokenType::DOUBLE_QUOTE => 1,
    ],
    SymbolType::NT_EXPR_ARG_OR_TAIL => [
        TokenType::OP_OR => 0,
        TokenType::RIGHT_BRACKET => 1,
        TokenType::COMMA => 1,
        TokenType::RIGHT_SQUARE_BRACKET => 1,
    ],
    SymbolType::NT_EXPR_ARG_AND_TAIL => [
        TokenType::OP_AND => 0,
        TokenType::OP_OR => 1,
        TokenType::RIGHT_BRACKET => 1,
        TokenType::COMMA => 1,
        TokenType::RIGHT_SQUARE_BRACKET => 1,
    ],
    SymbolType::NT_EXPR_ARG_COMP_TAIL => [
        TokenType::OP_EQ => 0,
        TokenType::OP_NEQ => 1,
        TokenType::OP_L => 2,
        TokenType::OP_LE => 3,
        TokenType::OP_G => 4,
        TokenType::OP_GE => 5,
        TokenType::OP_REGEX => 6,
        TokenType::NAME => 7,
        TokenType::NULL => 7,
        TokenType::TRUE => 7,
        TokenType::FALSE => 7,
        TokenType::OP_AND => 8,
        TokenType::OP_OR => 8,
        TokenType::RIGHT_BRACKET => 8,
        TokenType::COMMA => 8,
        TokenType::RIGHT_SQUARE_BRACKET => 8,
    ],
    SymbolType::NT_EXPR_ARG_SCALAR => [
        TokenType::LEFT_BRACKET => 0,
        TokenType::ROOT_ABSOLUTE => 1,
        TokenType::ROOT_RELATIVE => 1,
        TokenType::HYPHEN => 2,
        TokenType::INT => 2,
        TokenType::LEFT_SQUARE_BRACKET => 3,
        TokenType::NULL => 4,
        TokenType::TRUE => 5,
        TokenType::FALSE => 6,
        TokenType::SINGLE_QUOTE => 7,
        TokenType::DOUBLE_QUOTE => 7,
    ],
    SymbolType::NT_EXPR_GROUP => [
        TokenType::LEFT_BRACKET => 0,
    ],
    SymbolType::NT_ARRAY => [
        TokenType::LEFT_SQUARE_BRACKET => 0,
    ],
    SymbolType::NT_ARRAY_CONTENT => [
        TokenType::OP_NOT => 0,
        TokenType::ROOT_ABSOLUTE => 0,
        TokenType::ROOT_RELATIVE => 0,
        TokenType::HYPHEN => 0,
        TokenType::INT => 0,
        TokenType::NULL => 0,
        TokenType::TRUE => 0,
        TokenType::FALSE => 0,
        TokenType::LEFT_BRACKET => 0,
        TokenType::LEFT_SQUARE_BRACKET => 0,
        TokenType::SINGLE_QUOTE => 0,
        TokenType::DOUBLE_QUOTE => 0,
        TokenType::RIGHT_SQUARE_BRACKET => 1,
    ],
    SymbolType::NT_ARRAY_CONTENT_TAIL => [
        TokenType::COMMA => 0,
        TokenType::RIGHT_SQUARE_BRACKET => 1,
    ],
    SymbolType::NT_STRING => [
        TokenType::SINGLE_QUOTE => 0,
        TokenType::DOUBLE_QUOTE => 1,
    ],
    SymbolType::NT_STRING_CONTENT => [
        TokenType::UNESCAPED => 0,
        TokenType::BACKSLASH => 1,
        TokenType::SINGLE_QUOTE => 2,
        TokenType::DOUBLE_QUOTE => 2,
    ],
    SymbolType::NT_ESCAPED => [
        TokenType::BACKSLASH => 0,
        TokenType::SINGLE_QUOTE => 1,
        TokenType::DOUBLE_QUOTE => 2,
        TokenType::UNESCAPED => 3,
    ],
    SymbolType::NT_REGEXP => [
        TokenType::SLASH => 0,
    ],
    SymbolType::NT_REGEXP_STRING => [
        TokenType::UNESCAPED => 0,
        TokenType::BACKSLASH => 1,
        TokenType::REGEXP_MOD => 2,
    ],
    SymbolType::NT_REGEXP_ESCAPED => [
        TokenType::SLASH => 0,
        TokenType::BACKSLASH => 1,
        TokenType::UNESCAPED => 2,
    ],
];
