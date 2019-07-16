<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Iterator\DecodedJson;

use Iterator;
use function iterator_to_array;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Iterator\EventExporter;
use Remorhaz\JSON\Path\Iterator\DecodedJson\NodeObjectValue;
use Remorhaz\JSON\Path\Iterator\DecodedJson\NodeScalarValue;
use Remorhaz\JSON\Path\Iterator\DecodedJson\NodeValueFactory;
use Remorhaz\JSON\Path\Iterator\Event\ScalarEvent;
use Remorhaz\JSON\Path\Iterator\Event\ValueEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\AfterObjectEvent;
use Remorhaz\JSON\Path\Iterator\Event\BeforeObjectEvent;
use Remorhaz\JSON\Path\Iterator\Event\PropertyEvent;
use Remorhaz\JSON\Path\Iterator\Event\DataEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\ElementEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\PropertyEventInterface;
use Remorhaz\JSON\Path\Iterator\Path;
use Remorhaz\JSON\Path\Iterator\PathAwareInterface;
use Remorhaz\JSON\Path\Iterator\ValueInterface;
use Remorhaz\JSON\Path\Iterator\ValueIteratorFactory;
use stdClass;

/**
 * @covers \Remorhaz\JSON\Path\Iterator\DecodedJson\NodeObjectValue
 */
class NodeObjectValueTest extends TestCase
{

    /**
     * @param $data
     * @param array $expectedValue
     * @dataProvider providerValidData
     */
    public function testCreateIterator_ConstructedWithValidData_GeneratesMatchingEventList(
        $data,
        array $expectedValue
    ): void {
        $value = new NodeObjectValue($data, new Path, new NodeValueFactory);

        $actualEvents = iterator_to_array($value->createIterator(), false);
        self::assertSame($expectedValue, $this->exportEvents(...$actualEvents));
    }

    public function providerValidData(): array
    {
        return [
            'Empty object' => [
                (object) [],
                [
                    [
                        'class' => BeforeObjectEvent::class,
                        'value' => [
                            'class' => NodeObjectValue::class,
                            'data' => ['class' => stdClass::class, 'data' => []],
                            'path' => [],
                        ],
                    ],
                    [
                        'class' => AfterObjectEvent::class,
                        'value' => [
                            'class' => NodeObjectValue::class,
                            'data' => ['class' => stdClass::class, 'data' => []],
                            'path' => [],
                        ],
                    ],
                ],
            ],
            'Object with scalar property' => [
                (object) ['a' => 1],
                [
                    [
                        'class' => BeforeObjectEvent::class,
                        'value' => [
                            'class' => NodeObjectValue::class,
                            'data' => ['class' => stdClass::class, 'data' => ['a' => 1]],
                            'path' => [],
                        ],
                    ],
                    ['class' => PropertyEvent::class, 'path' => [], 'name' => 'a'],
                    [
                        'class' => ScalarEvent::class,
                        'value' => [
                            'class' => NodeScalarValue::class,
                            'data' => 1,
                            'path' => ['a'],
                        ],
                    ],
                    [
                        'class' => AfterObjectEvent::class,
                        'value' => [
                            'class' => NodeObjectValue::class,
                            'data' => ['class' => stdClass::class, 'data' => ['a' => 1]],
                            'path' => [],
                        ],
                    ],
                ],
            ],
            'Object with object property' => [
                (object) ['a' => (object) ['b' => 1]],
                [
                    [
                        'class' => BeforeObjectEvent::class,
                        'value' => [
                            'class' => NodeObjectValue::class,
                            'data' => [
                                'class' => stdClass::class,
                                'data' => ['a' => ['class' => stdClass::class, 'data' => ['b' => 1]]],
                            ],
                            'path' => [],
                        ],
                    ],
                    ['class' => PropertyEvent::class, 'path' => [], 'name' => 'a'],
                    [
                        'class' => BeforeObjectEvent::class,
                        'value' => [
                            'class' => NodeObjectValue::class,
                            'data' => ['class' => stdClass::class, 'data' => ['b' => 1]],
                            'path' => ['a'],
                        ],
                    ],
                    ['class' => PropertyEvent::class, 'path' => ['a'], 'name' => 'b'],
                    [
                        'class' => ScalarEvent::class,
                        'value' => [
                            'class' => NodeScalarValue::class,
                            'data' => 1,
                            'path' => ['a', 'b'],
                        ],
                    ],
                    [
                        'class' => AfterObjectEvent::class,
                        'value' => [
                            'class' => NodeObjectValue::class,
                            'data' => ['class' => stdClass::class, 'data' => ['b' => 1]],
                            'path' => ['a'],
                        ],
                    ],
                    [
                        'class' => AfterObjectEvent::class,
                        'value' => [
                            'class' => NodeObjectValue::class,
                            'data' => [
                                'class' => stdClass::class,
                                'data' => ['a' => ['class' => stdClass::class, 'data' => ['b' => 1]]],
                            ],
                            'path' => [],
                        ],
                    ],
                ],
            ],
        ];
    }

    private function exportEvents(DataEventInterface ...$events): array
    {
        $result = [];
        foreach ($events as $event) {
            $result[] = $this->exportEvent($event);
        }

        return $result;
    }

    private function exportEvent(DataEventInterface $event): array
    {
        $result = [
            'class' => get_class($event),
        ];

        if ($event instanceof PathAwareInterface) {
            $result += ['path' => $event->getPath()->getElements()];
        }

        if ($event instanceof ValueEventInterface) {
            $result += ['value' => $this->exportValue($event->getValue())];
        }

        if ($event instanceof ElementEventInterface) {
            $result += ['index' => $event->getIndex()];
        }

        if ($event instanceof PropertyEventInterface) {
            $result += ['name' => $event->getName()];
        }

        return $result;
    }

    private function exportValue(ValueInterface $value): array
    {
        $result = [
            'class' => get_class($value),
            'data' => $this->exportData($this->exportIterator($value->createIterator())),
        ];

        if ($value instanceof PathAwareInterface) {
            $result += ['path' => $value->getPath()->getElements()];
        }

        return $result;
    }

    private function exportData($data)
    {
        if (is_array($data)) {
            $result = [];
            foreach ($data as $index => $element) {
                $result[$index] = $this->exportData($element);
            }
            return $result;
        }

        if (is_object($data)) {
            $result = [
                'class' => get_class($data),
                'data' => [],
            ];
            foreach (get_object_vars($data) as $name => $property) {
                $result['data'][$name] = $this->exportData($property);
            }
            return $result;
        }

        return $data;
    }

    private function exportIterator(Iterator $iterator)
    {
        return (new EventExporter(new ValueIteratorFactory))->export($iterator);
    }
}
