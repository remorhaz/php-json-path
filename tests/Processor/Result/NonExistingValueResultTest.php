<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Processor\Result;

use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Processor\Result\Exception\SelectedValueNotFoundException;
use Remorhaz\JSON\Path\Processor\Result\NonExistingValueResult;

/**
 * @covers \Remorhaz\JSON\Path\Processor\Result\NonExistingValueResult
 */
class NonExistingValueResultTest extends TestCase
{

    public function testExists_Always_ReturnsFalse(): void
    {
        $result = new NonExistingValueResult();
        self::assertFalse($result->exists());
    }

    public function testGet_Constructed_ThrowsException(): void
    {
        $result = new NonExistingValueResult();
        $this->expectException(SelectedValueNotFoundException::class);
        $result->get();
    }

    public function testEncode_Constructed_ThrowsException(): void
    {
        $result = new NonExistingValueResult();
        $this->expectException(SelectedValueNotFoundException::class);
        $result->encode();
    }

    public function testDecode_Constructed_ThrowsException(): void
    {
        $result = new NonExistingValueResult();
        $this->expectException(SelectedValueNotFoundException::class);
        $result->decode();
    }
}
