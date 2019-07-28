<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Value;

use function array_map;
use function get_class;
use Iterator;
use function iterator_to_array;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Event\DataEventInterface;
use Remorhaz\JSON\Data\Event\ScalarEvent;
use Remorhaz\JSON\Data\Event\ScalarEventInterface;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;
use Remorhaz\JSON\Path\Value\Exception\InvalidScalarDataException;
use Remorhaz\JSON\Path\Value\LiteralScalarValue;

/**
 * @covers \Remorhaz\JSON\Path\Value\LiteralScalarValue
 */
class LiteralScalarValueTest extends TestCase
{

    public function testConstruct_InvalidValue_ThrowsException(): void
    {
        $this->expectException(InvalidScalarDataException::class);
        new LiteralScalarValue([]);
    }

    /**
     * @param mixed $value
     * @param mixed $expectedValue
     * @dataProvider providerGetData
     */
    public function testGetData_ConstructedWithGivenValue_ReturnsSameValue($value, $expectedValue): void
    {
        $value = new LiteralScalarValue($value);
        self::assertSame($expectedValue, $value->getData());
    }

    public function providerGetData(): array
    {
        return [
            'TRUE' => [true, true],
            'FALSE' => [false, false],
            'NULL' => [null, null],
            'Integer' => [1, 1],
            'Float' => [1.5, 1.5],
            'String' => ['a', 'a'],
        ];
    }

    /**
     * @param mixed $value
     * @param array $expectedValue
     * @dataProvider providerCreateIterator
     */
    public function testCreateIterator_ConstructedWithGivenValue_GeneratesMatchingEvents(
        $value,
        array $expectedValue
    ): void {
        $value = new LiteralScalarValue($value);
        self::assertSame($expectedValue, $this->exportEvents($value->createIterator()));
    }

    public function providerCreateIterator(): array
    {
        return [
            'Integer' => [
                1,
                [
                    [
                        'class' => ScalarEvent::class,
                        'value' => [
                            'class' => LiteralScalarValue::class,
                            'data' => 1,
                        ],
                    ],
                ],
            ],
        ];
    }

    private function exportEvents(Iterator $iterator): array
    {
        return array_map(
            [$this, 'exportEvent'],
            iterator_to_array($iterator)
        );
    }

    private function exportEvent(DataEventInterface $event): array
    {
        $data = [];
        if ($event instanceof ScalarEventInterface) {
            $data['value'] = $this->exportValue($event->getValue());
        }

        return [
            'class' => get_class($event),
        ] + $data;
    }

    private function exportValue(ValueInterface $value): array
    {
        $data = [];
        if ($value instanceof ScalarValueInterface) {
            $data['data'] = $value->getData();
        }

        return [
            'class' => get_class($value),
        ] + $data;
    }
}
