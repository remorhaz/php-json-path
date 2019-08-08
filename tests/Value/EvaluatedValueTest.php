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
use Remorhaz\JSON\Data\Value\ValueInterface;
use Remorhaz\JSON\Path\Value\EvaluatedValue;
use Remorhaz\JSON\Path\Value\EvaluatedValueInterface;

/**
 * @covers \Remorhaz\JSON\Path\Value\EvaluatedValue
 */
class EvaluatedValueTest extends TestCase
{

    /**
     * @param bool $value
     * @param bool $expectedValue
     * @dataProvider providerGetData
     */
    public function testGetData_ConstructedWithGivenValue_ReturnsSameValue(bool $value, bool $expectedValue): void
    {
        $value = new EvaluatedValue($value);
        self::assertSame($expectedValue, $value->getData());
    }

    public function providerGetData(): array
    {
        return [
            'TRUE' => [true, true],
            'FALSE' => [false, false],
        ];
    }

    /**
     * @param bool $value
     * @param array $expectedValue
     * @dataProvider providerCreateIterator
     */
    public function testCreateIterator_ConstructedWithGivenValue_GeneratesMatchingEvents(
        bool $value,
        array $expectedValue
    ): void {
        $value = new EvaluatedValue($value);
        self::assertSame($expectedValue, $this->exportEvents($value->createEventIterator()));
    }

    public function providerCreateIterator(): array
    {
        return [
            'TRUE' => [
                true,
                [
                    [
                        'class' => ScalarEvent::class,
                        'value' => [
                            'class' => EvaluatedValue::class,
                            'data' => true,
                        ],
                    ],
                ],
            ],
            'FALSE' => [
                false,
                [
                    [
                        'class' => ScalarEvent::class,
                        'value' => [
                            'class' => EvaluatedValue::class,
                            'data' => false,
                        ],
                    ],
                ],
            ],
        ];
    }

    private function exportEvents(Iterator $eventIterator): array
    {
        return array_map(
            [$this, 'exportEvent'],
            iterator_to_array($eventIterator)
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
        if ($value instanceof EvaluatedValueInterface) {
            $data['data'] = $value->getData();
        }

        return [
            'class' => get_class($value),
        ] + $data;
    }
}
