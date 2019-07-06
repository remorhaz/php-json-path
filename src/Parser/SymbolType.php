<?php

namespace Remorhaz\JSON\Path\Parser;

abstract class SymbolType
{
    public const NT_ROOT                = 0x00;

    public const T_WS                   = 0x01;
    public const T_DOT                  = 0x02;
    public const T_DOUBLE_DOT           = 0x03;
    public const T_LEFT_BRACKET         = 0x04;
    public const T_RIGHT_BRACKET        = 0x05;
    public const T_LEFT_SQUARE_BRACKET  = 0x06;
    public const T_RIGHT_SQUARE_BRACKET = 0x07;
    public const T_STAR                 = 0x08;
    public const T_COMMA                = 0x09;
    public const T_QUESTION             = 0x0A;
    public const T_COLON                = 0x0B;
    public const T_HYPHEN               = 0x0C;
    public const T_SLASH                = 0x0D;
    public const T_BACKSLASH            = 0x0E;
    public const T_INT                  = 0x0F;
    public const T_NAME                 = 0x10;
    public const T_SINGLE_QUOTE         = 0x11;
    public const T_DOUBLE_QUOTE         = 0x12;
    public const T_UNESCAPED            = 0x13;
    public const T_OP_EQ                = 0x14;
    public const T_OP_NEQ               = 0x15;
    public const T_OP_L                 = 0x16;
    public const T_OP_LE                = 0x17;
    public const T_OP_G                 = 0x18;
    public const T_OP_GE                = 0x19;
    public const T_OP_AND               = 0x1A;
    public const T_OP_OR                = 0x1B;
    public const T_OP_REGEX             = 0x1C;
    public const T_OP_NOT               = 0x1D;
    public const T_ROOT_ABSOLUTE        = 0x1E;
    public const T_ROOT_RELATIVE        = 0x1F;
    public const T_NULL                 = 0x20;
    public const T_TRUE                 = 0x21;
    public const T_FALSE                = 0x22;
    public const T_REGEXP_MOD           = 0x23;

    public const NT_JSON_PATH           = 0x80;
    public const NT_PATH                = 0x81;
    public const NT_FILTER_LIST         = 0x82;
    public const NT_NAME                = 0x83;
    public const NT_BRACKET_FILTER      = 0x84;
    public const NT_DOT_FILTER          = 0x85;
    public const NT_DOT_FILTER_NEXT     = 0x86;
    public const NT_STRING              = 0x87;
    public const NT_STRING_NEXT         = 0x88;
    public const NT_INT_NEXT            = 0x89;
    public const NT_WS_OPT              = 0x8A;
    public const NT_INT_NEXT_LIST       = 0x8B;
    public const NT_INT_SLICE           = 0x8C;
    public const NT_INT_SLICE_STEP      = 0x8D;
    public const NT_INT                 = 0x8E;
    public const NT_INT_OPT             = 0x8F;
    public const NT_EXPR                = 0x90;
    public const NT_EXPR_GROUP          = 0x91;
    public const NT_EXPR_ARG_OR         = 0x92;
    public const NT_EXPR_ARG_OR_TAIL    = 0x93;
    public const NT_EXPR_ARG_AND        = 0x94;
    public const NT_EXPR_ARG_AND_TAIL   = 0x95;
    public const NT_EXPR_ARG_COMP       = 0x96;
    public const NT_EXPR_ARG_COMP_TAIL  = 0x97;
    public const NT_ARRAY               = 0x98;
    public const NT_ARRAY_CONTENT       = 0x99;
    public const NT_EXPR_ARG_SCALAR     = 0x9A;
    public const NT_STRING_CONTENT      = 0x9B;
    public const NT_ESCAPED             = 0x9C;
    public const NT_STRING_LIST         = 0x9D;
    public const NT_ARRAY_CONTENT_TAIL  = 0x9E;
    public const NT_DOUBLE_DOT_FILTER   = 0x9F;
    public const NT_REGEXP              = 0xA0;
    public const NT_REGEXP_STRING       = 0xA1;
    public const NT_REGEXP_ESCAPED      = 0xA2;

    public const T_EOI                  = 0xFF;
}
