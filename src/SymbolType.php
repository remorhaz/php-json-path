<?php

namespace Remorhaz\JSON\Path;

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

    public const NT_JSON_PATH           = 0x1D;
    public const NT_PATH                = 0x1E;
    public const NT_FILTER_LIST         = 0x1F;
    public const NT_NAME                = 0x22;
    public const NT_BRACKET_FILTER      = 0x23;
    public const NT_DOT_FILTER          = 0x24;
    public const NT_DOT_FILTER_NEXT     = 0x25;
    public const NT_STRING              = 0x26;
    public const NT_STRING_NEXT         = 0x27;
    public const NT_INT_NEXT            = 0x28;
    public const NT_WS_OPT              = 0x29;
    public const NT_INT_NEXT_LIST       = 0x2A;
    public const NT_INT_SLICE           = 0x2B;
    public const NT_INT_SLICE_STEP      = 0x2C;
    public const NT_INT                 = 0x2D;
    public const NT_INT_OPT             = 0x2E;
    public const NT_EXPRESSION          = 0x2F;

    public const T_EOI                  = 0xFF;
}
