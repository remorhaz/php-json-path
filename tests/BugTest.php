<?php

use PHPUnit\Framework\TestCase;

class BugTest extends TestCase
{

    public function testBug(): void
    {
        $object = new class implements ArrayAccess {

            public function offsetGet($offset)
            {
                return [1, 2];
            }

            public function offsetExists($offset)
            {
                return true;
            }

            public function offsetUnset($offset)
            {
            }

            public function offsetSet($offset, $value)
            {
            }
        };

        self::assertSame(2, max(...$object[0]));
    }
}