<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Iterator\DecodedJson;

use Iterator;
use function iterator_to_array;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\EventExporter;
use Remorhaz\JSON\Data\DecodedJson\NodeArrayValue;
use Remorhaz\JSON\Data\DecodedJson\NodeScalarValue;
use Remorhaz\JSON\Data\DecodedJson\NodeValueFactory;
use Remorhaz\JSON\Data\Event\ScalarEvent;
use Remorhaz\JSON\Data\Event\ValueEventInterface;
use Remorhaz\JSON\Data\Event\ElementEvent;
use Remorhaz\JSON\Data\Event\DataEventInterface;
use Remorhaz\JSON\Data\Event\AfterArrayEvent;
use Remorhaz\JSON\Data\Event\BeforeArrayEvent;
use Remorhaz\JSON\Data\DecodedJson\Exception\InvalidElementKeyException;
use Remorhaz\JSON\Data\Event\ElementEventInterface;
use Remorhaz\JSON\Data\Event\PropertyEventInterface;
use Remorhaz\JSON\Data\Path;
use Remorhaz\JSON\Data\PathAwareInterface;
use Remorhaz\JSON\Data\ValueInterface;
use Remorhaz\JSON\Data\ValueIteratorFactory;

/**
 * @covers \Remorhaz\JSON\Data\DecodedJson\NodeArrayValue
 */
class NodeArrayValueTest extends TestCase
{

    /**
     * @param array $data
     * @param array $expectedValue
     * @dataProvider providerValidData
     */
    public function testCreateIterator_Constructed_GeneratesMatchingEventList(
        array $data,
        array $expectedValue
    ): void {
        $value = new NodeArrayValue($data, new Path, new NodeValueFactory);

        $actualEvents = iterator_to_array($value->createIterator(), false);
        self::assertSame($expectedValue, $this->exportEvents(...$actualEvents));
    }

    public function providerValidData(): array
    {
        return [
            'Empty array' => [
                [],
                [
                    [
                        'class' => BeforeArrayEvent::class,
                        'value' => [
                            'class' => NodeArrayValue::class,
                            'data' => [],
                            'path' => [],
                        ],
                    ],
                    [
                        'class' => AfterArrayEvent::class,
                        'value' => [
                            'class' => NodeArrayValue::class,
                            'data' => [],
                            'path' => [],
                        ],
                    ],
                ],
            ],
            'Array with scalar element' => [
                [1],
                [
                    [
                        'class' => BeforeArrayEvent::class,
                        'value' => [
                            'class' => NodeArrayValue::class,
                            'data' => [1],
                            'path' => [],
                        ],
                    ],
                    ['class' => ElementEvent::class, 'path' => [], 'index' => 0],
                    [
                        'class' => ScalarEvent::class,
                        'value' => [
                            'class' => NodeScalarValue::class,
                            'data' => 1,
                            'path' => [0],
                        ],
                    ],
                    [
                        'class' => AfterArrayEvent::class,
                        'value' => [
                            'class' => NodeArrayValue::class,
                            'data' => [1],
                            'path' => [],
                        ],
                    ],
                ],
            ],
            'Array with array element' => [
                [[1]],
                [
                    [
                        'class' => BeforeArrayEvent::class,
                        'value' => [
                            'class' => NodeArrayValue::class,
                            'data' => [[1]],
                            'path' => [],
                        ],
                    ],
                    ['class' => ElementEvent::class, 'path' => [], 'index' => 0],
                    [
                        'class' => BeforeArrayEvent::class,
                        'value' => [
                            'class' => NodeArrayValue::class,
                            'data' => [1],
                            'path' => [0],
                        ],
                    ],
                    ['class' => ElementEvent::class, 'path' => [0], 'index' => 0],
                    [
                        'class' => ScalarEvent::class,
                        'value' => [
                            'class' => NodeScalarValue::class,
                            'data' => 1,
                            'path' => [0, 0],
                        ],
                    ],
                    [
                        'class' => AfterArrayEvent::class,
                        'value' => [
                            'class' => NodeArrayValue::class,
                            'data' => [1],
                            'path' => [0],
                        ],
                    ],
                    [
                        'class' => AfterArrayEvent::class,
                        'value' => [
                            'class' => NodeArrayValue::class,
                            'data' => [[1]],
                            'path' => [],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array $data
     * @dataProvider providerArrayWithInvalidIndex
     */
    public function testCreateIterator_ArrayDataWithInvalidIndex_ThrowsException(array $data): void
    {
        $value = new NodeArrayValue($data, new Path, new NodeValueFactory);

        $this->expectException(InvalidElementKeyException::class);
        iterator_to_array($value->createIterator());
    }

    public function providerArrayWithInvalidIndex(): array
    {
        return [
            'Non-zero first index' => [[1 => 'a']],
            'Non-integer first index' => [['a' => 'b']],
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
        return (new EventExporter(new ValueIteratorFactory()))->export($iterator);
    }
}
