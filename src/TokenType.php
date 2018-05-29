<?php

namespace Remorhaz\JSON\Path;

abstract class TokenType
{

    public const ROOT = 0x01;
    public const DOT = 0x02;
    public const SELF = 0x03;
    public const LEFT_BRACKET = 0x04;
    public const RIGHT_BRACKET = 0x05;
    public const LEFT_SQUARE_BRACKET = 0x06;
    public const RIGHT_SQUARE_BRACKET = 0x07;
    public const STAR = 0x08;
    public const COMMA = 0x09;
    public const QUESTION = 0x0A;
    public const COLON = 0x0B;
    public const INT = 0x0C;
    public const HYPHEN = 0x0D;
    public const NAME = 0x0E;
    public const SINGLE_QUOTE = 0x0F;
    public const UNESCAPED = 0x10;
    public const SLASH = 0x11;
    public const OP_EQ = 0x12;
    public const OP_NEQ = 0x13;
    public const OP_L = 0x14;
    public const OP_LE = 0x15;
    public const OP_G = 0x16;
    public const OP_GE = 0x17;
    public const OP_REGEX = 0x18;
    public const BACKSLASH = 0x19;
    public const WS = 0x1A;
    public const DOUBLE_DOT = 0x1B;

    public const EOI = 0xFF;
}
