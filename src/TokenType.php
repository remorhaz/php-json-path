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

    public const EOI = 0xFF;
}
