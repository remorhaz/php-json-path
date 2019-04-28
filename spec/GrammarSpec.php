<?php

namespace Remorhaz\JSON\Path;

use Remorhaz\UniLex\Grammar\ContextFree\GrammarLoader;

return [
    GrammarLoader::ROOT_SYMBOL_KEY => SymbolType::NT_ROOT,
    GrammarLoader::EOI_SYMBOL_KEY => SymbolType::T_EOI,
    GrammarLoader::START_SYMBOL_KEY => SymbolType::NT_JSON_PATH,

    GrammarLoader::TOKEN_MAP_KEY => [
            SymbolType::T_WS                    => TokenType::WS,
            SymbolType::T_DOT                   => TokenType::DOT,
            SymbolType::T_DOUBLE_DOT            => TokenType::DOUBLE_DOT,
            SymbolType::T_LEFT_BRACKET          => TokenType::LEFT_BRACKET,
            SymbolType::T_RIGHT_BRACKET         => TokenType::RIGHT_BRACKET,
            SymbolType::T_LEFT_SQUARE_BRACKET   => TokenType::LEFT_SQUARE_BRACKET,
            SymbolType::T_RIGHT_SQUARE_BRACKET  => TokenType::RIGHT_SQUARE_BRACKET,
            SymbolType::T_STAR                  => TokenType::STAR,
            SymbolType::T_COMMA                 => TokenType::COMMA,
            SymbolType::T_QUESTION              => TokenType::QUESTION,
            SymbolType::T_COLON                 => TokenType::COLON,
            SymbolType::T_HYPHEN                => TokenType::HYPHEN,
            SymbolType::T_SLASH                 => TokenType::SLASH,
            SymbolType::T_BACKSLASH             => TokenType::BACKSLASH,
            SymbolType::T_INT                   => TokenType::INT,
            SymbolType::T_NAME                  => TokenType::NAME,
            SymbolType::T_SINGLE_QUOTE          => TokenType::SINGLE_QUOTE,
            SymbolType::T_DOUBLE_QUOTE          => TokenType::DOUBLE_QUOTE,
            SymbolType::T_UNESCAPED             => TokenType::UNESCAPED,
            SymbolType::T_OP_EQ                 => TokenType::OP_EQ,
            SymbolType::T_OP_NEQ                => TokenType::OP_NEQ,
            SymbolType::T_OP_L                  => TokenType::OP_L,
            SymbolType::T_OP_LE                 => TokenType::OP_LE,
            SymbolType::T_OP_G                  => TokenType::OP_G,
            SymbolType::T_OP_GE                 => TokenType::OP_GE,
            SymbolType::T_OP_AND                => TokenType::OP_AND,
            SymbolType::T_OP_OR                 => TokenType::OP_OR,
            SymbolType::T_OP_REGEX              => TokenType::OP_REGEX,
            SymbolType::T_OP_NOT                => TokenType::OP_NOT,
            SymbolType::T_ROOT_ABSOLUTE         => TokenType::ROOT_ABSOLUTE,
            SymbolType::T_ROOT_RELATIVE         => TokenType::ROOT_RELATIVE,
            SymbolType::T_NULL                  => TokenType::NULL,
            SymbolType::T_TRUE                  => TokenType::TRUE,
            SymbolType::T_FALSE                 => TokenType::FALSE,

            SymbolType::T_EOI                   => TokenType::EOI,
    ],

    GrammarLoader::PRODUCTION_MAP_KEY => [
        SymbolType::NT_ROOT => [
            [SymbolType::NT_JSON_PATH, SymbolType::T_EOI],
        ],
        SymbolType::NT_JSON_PATH => [
            [SymbolType::NT_PATH],
        ],
        SymbolType::NT_PATH => [
            [SymbolType::T_ROOT_ABSOLUTE, SymbolType::NT_FILTER_LIST],
            [SymbolType::T_ROOT_RELATIVE, SymbolType::NT_FILTER_LIST],
        ],
        SymbolType::NT_FILTER_LIST => [
            [SymbolType::T_DOT, SymbolType::NT_DOT_FILTER],
            [SymbolType::T_DOUBLE_DOT, SymbolType::NT_NAME, SymbolType::NT_FILTER_LIST],
            [
                SymbolType::T_LEFT_SQUARE_BRACKET,
                SymbolType::NT_WS_OPT,
                SymbolType::NT_BRACKET_FILTER,
                SymbolType::T_RIGHT_SQUARE_BRACKET,
                SymbolType::NT_FILTER_LIST
            ],
            [],
        ],
        SymbolType::NT_DOT_FILTER => [
            [SymbolType::T_NAME, SymbolType::NT_DOT_FILTER_NEXT],
            [SymbolType::T_STAR, SymbolType::NT_FILTER_LIST],
            [SymbolType::T_NULL, SymbolType::NT_DOT_FILTER_NEXT],
            [SymbolType::T_TRUE, SymbolType::NT_DOT_FILTER_NEXT],
            [SymbolType::T_FALSE, SymbolType::NT_DOT_FILTER_NEXT],
        ],
        SymbolType::NT_DOT_FILTER_NEXT => [
            [SymbolType::T_LEFT_BRACKET, SymbolType::T_RIGHT_BRACKET],
            [SymbolType::NT_FILTER_LIST],
        ],
        SymbolType::NT_NAME => [
            [SymbolType::T_STAR],
            [SymbolType::T_NAME],
            [SymbolType::T_NULL],
            [SymbolType::T_TRUE],
            [SymbolType::T_FALSE],
        ],
        SymbolType::NT_BRACKET_FILTER => [
            [SymbolType::T_STAR, SymbolType::NT_WS_OPT],
            [SymbolType::NT_STRING_LIST],
            [SymbolType::NT_INT, SymbolType::NT_INT_NEXT],
            [SymbolType::NT_INT_SLICE],
            [SymbolType::T_LEFT_BRACKET, SymbolType::NT_WS_OPT, SymbolType::NT_EXPR, SymbolType::T_RIGHT_BRACKET],
            [
                SymbolType::T_QUESTION,
                SymbolType::T_LEFT_BRACKET,
                SymbolType::NT_WS_OPT,
                SymbolType::NT_EXPR,
                SymbolType::T_RIGHT_BRACKET,
            ],
        ],
        SymbolType::NT_STRING_LIST => [
            [SymbolType::NT_STRING, SymbolType::NT_WS_OPT, SymbolType::NT_STRING_NEXT],
        ],
        SymbolType::NT_STRING_NEXT => [
            [
                SymbolType::T_COMMA,
                SymbolType::NT_WS_OPT,
                SymbolType::NT_STRING,
                SymbolType::NT_WS_OPT,
                SymbolType::NT_STRING_NEXT,
            ],
            [],
        ],
        SymbolType::NT_INT_NEXT => [
            [SymbolType::NT_WS_OPT, SymbolType::NT_INT_NEXT_LIST],
            [SymbolType::NT_INT_SLICE],
        ],
        SymbolType::NT_INT_NEXT_LIST => [
            [
                SymbolType::T_COMMA,
                SymbolType::NT_WS_OPT,
                SymbolType::NT_INT,
                SymbolType::NT_WS_OPT,
                SymbolType::NT_INT_NEXT_LIST,
            ],
            [],
        ],
        SymbolType::NT_INT_SLICE => [
            [SymbolType::T_COLON, SymbolType::NT_INT_OPT, SymbolType::NT_INT_SLICE_STEP, SymbolType::NT_WS_OPT],
        ],
        SymbolType::NT_INT_SLICE_STEP => [
            [SymbolType::T_COLON, SymbolType::NT_INT_OPT],
            [],
        ],
        SymbolType::NT_WS_OPT => [
            [SymbolType::T_WS],
            [],
        ],
        SymbolType::NT_INT => [
            [SymbolType::T_HYPHEN, SymbolType::T_INT],
            [SymbolType::T_INT],
        ],
        SymbolType::NT_INT_OPT => [
            [SymbolType::NT_INT],
            [],
        ],
        SymbolType::NT_EXPR => [
            [SymbolType::NT_EXPR_ARG_OR, SymbolType::NT_EXPR_ARG_OR_TAIL],
        ],
        SymbolType::NT_EXPR_ARG_OR => [
            [SymbolType::NT_EXPR_ARG_AND, SymbolType::NT_EXPR_ARG_AND_TAIL],
        ],
        SymbolType::NT_EXPR_ARG_OR_TAIL => [
            [SymbolType::T_OP_OR, SymbolType::NT_WS_OPT, SymbolType::NT_EXPR_ARG_OR],
            [],
        ],
        SymbolType::NT_EXPR_ARG_AND => [
            [SymbolType::NT_EXPR_ARG_COMP, SymbolType::NT_EXPR_ARG_COMP_TAIL],
        ],
        SymbolType::NT_EXPR_ARG_AND_TAIL => [
            [SymbolType::T_OP_AND, SymbolType::NT_WS_OPT, SymbolType::NT_EXPR_ARG_AND],
            [],
        ],
        SymbolType::NT_EXPR_ARG_COMP => [
            [SymbolType::T_OP_NOT, SymbolType::NT_EXPR_ARG_SCALAR],
            [SymbolType::NT_EXPR_ARG_SCALAR],
        ],
        SymbolType::NT_EXPR_ARG_SCALAR => [
            [SymbolType::NT_EXPR_GROUP, SymbolType::NT_WS_OPT],
            [SymbolType::NT_PATH, SymbolType::NT_WS_OPT],
            [SymbolType::NT_INT, SymbolType::NT_WS_OPT],
            [SymbolType::NT_ARRAY, SymbolType::NT_WS_OPT],
            [SymbolType::T_NULL, SymbolType::NT_WS_OPT],
            [SymbolType::T_TRUE, SymbolType::NT_WS_OPT],
            [SymbolType::T_FALSE, SymbolType::NT_WS_OPT],
        ],
        SymbolType::NT_EXPR_ARG_COMP_TAIL => [
            [SymbolType::T_OP_EQ, SymbolType::NT_WS_OPT, SymbolType::NT_EXPR_ARG_COMP],
            [SymbolType::T_OP_NEQ, SymbolType::NT_WS_OPT, SymbolType::NT_EXPR_ARG_COMP],
            [SymbolType::T_OP_L, SymbolType::NT_WS_OPT, SymbolType::NT_EXPR_ARG_COMP],
            [SymbolType::T_OP_LE, SymbolType::NT_WS_OPT, SymbolType::NT_EXPR_ARG_COMP],
            [SymbolType::T_OP_G, SymbolType::NT_WS_OPT, SymbolType::NT_EXPR_ARG_COMP],
            [SymbolType::T_OP_GE, SymbolType::NT_WS_OPT, SymbolType::NT_EXPR_ARG_COMP],
            [SymbolType::T_OP_REGEX, SymbolType::NT_WS_OPT, SymbolType::NT_EXPR_ARG_COMP],
            [SymbolType::T_NAME, SymbolType::NT_WS_OPT, SymbolType::NT_EXPR_ARG_COMP],
            [],
        ],
        SymbolType::NT_EXPR_GROUP => [
            [SymbolType::T_LEFT_BRACKET, SymbolType::NT_WS_OPT, SymbolType::NT_EXPR, SymbolType::T_RIGHT_BRACKET],
        ],
        SymbolType::NT_ARRAY => [
            [
                SymbolType::T_LEFT_SQUARE_BRACKET,
                SymbolType::NT_WS_OPT,
                SymbolType::NT_ARRAY_CONTENT,
                SymbolType::T_RIGHT_SQUARE_BRACKET
            ],
        ],
        SymbolType::NT_ARRAY_CONTENT => [
            [SymbolType::NT_STRING_LIST],
            [SymbolType::NT_INT, SymbolType::NT_WS_OPT, SymbolType::NT_INT_NEXT_LIST],
            [],
        ],
        SymbolType::NT_STRING => [
            [SymbolType::T_SINGLE_QUOTE, SymbolType::NT_STRING_CONTENT, SymbolType::T_SINGLE_QUOTE],
            [SymbolType::T_DOUBLE_QUOTE, SymbolType::NT_STRING_CONTENT, SymbolType::T_DOUBLE_QUOTE],
        ],
        SymbolType::NT_STRING_CONTENT => [
            [SymbolType::T_UNESCAPED, SymbolType::NT_STRING_CONTENT],
            [SymbolType::T_BACKSLASH, SymbolType::NT_ESCAPED, SymbolType::NT_STRING_CONTENT],
            [],
        ],
        SymbolType::NT_ESCAPED => [
            [SymbolType::T_BACKSLASH],
            [SymbolType::T_SINGLE_QUOTE],
            [SymbolType::T_DOUBLE_QUOTE],
            [SymbolType::T_UNESCAPED],
        ],
    ],
];
