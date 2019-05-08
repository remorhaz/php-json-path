<?php

namespace Remorhaz\JSON\Path;

abstract class TokenType
{

    public const WS                     = 0x01;
    public const DOT                    = 0x02;
    public const DOUBLE_DOT             = 0x03;
    public const LEFT_BRACKET           = 0x04;
    public const RIGHT_BRACKET          = 0x05;
    public const LEFT_SQUARE_BRACKET    = 0x06;
    public const RIGHT_SQUARE_BRACKET   = 0x07;
    public const STAR                   = 0x08;
    public const COMMA                  = 0x09;
    public const QUESTION               = 0x0A;
    public const COLON                  = 0x0B;
    public const HYPHEN                 = 0x0C;
    public const SLASH                  = 0x0D;
    public const BACKSLASH              = 0x0E;
    public const INT                    = 0x0F;
    public const NAME                   = 0x10;
    public const SINGLE_QUOTE           = 0x11;
    public const DOUBLE_QUOTE           = 0x12;
    public const UNESCAPED              = 0x13;
    public const OP_EQ                  = 0x14;
    public const OP_NEQ                 = 0x15;
    public const OP_L                   = 0x16;
    public const OP_LE                  = 0x17;
    public const OP_G                   = 0x18;
    public const OP_GE                  = 0x19;
    public const OP_AND                 = 0x1A;
    public const OP_OR                  = 0x1B;
    public const OP_REGEX               = 0x1C;
    public const OP_NOT                 = 0x1D;
    public const ROOT_ABSOLUTE          = 0x1E;
    public const ROOT_RELATIVE          = 0x1F;
    public const NULL                   = 0x20;
    public const TRUE                   = 0x21;
    public const FALSE                  = 0x22;
    public const REGEXP_MOD             = 0x23;

    public const EOI                    = 0xFF;
}
