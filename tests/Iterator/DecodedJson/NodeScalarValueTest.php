<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Iterator\DecodedJson;

use function get_class;
use Iterator;
use function iterator_to_array;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Iterator\EventExporter;
use Remorhaz\JSON\Path\Iterator\DecodedJson\NodeScalarValue;
use Remorhaz\JSON\Path\Iterator\Event\ScalarEvent;
use Remorhaz\JSON\Path\Iterator\Event\ValueEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\DataEventInterface;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Exception\InvalidNodeDataException;
use Remorhaz\JSON\Path\Iterator\Event\ElementEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\PropertyEventInterface;
use Remorhaz\JSON\Path\Iterator\Path;
use Remorhaz\JSON\Path\Iterator\PathAwareInterface;
use Remorhaz\JSON\Path\Iterator\ValueInterface;
use Remorhaz\JSON\Path\Iterator\ValueIteratorFactory;

/**
 * @covers \Remorhaz\JSON\Path\Iterator\DecodedJson\NodeScalarValue
 */
class NodeScalarValueTest extends TestCase
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
        $iteratorFactory = new NodeScalarValue($data, Path::createEmpty());

        $actualEvents = iterator_to_array($iteratorFactory->createIterator(), false);
        self::assertSame($expectedValue, $this->exportEvents(...$actualEvents));
    }

    public function providerValidData(): array
    {
        return [
            'Integer data' => [
                1,
                [
                    [
                        'class' => ScalarEvent::class,
                        'value' => [
                            'class' => NodeScalarValue::class,
                            'data' => 1,
                            'path' => [],
                        ],
                    ],
                ],
            ],
            'String data' => [
                'a',
                [
                    [
                        'class' => ScalarEvent::class,
                        'value' => [
                            'class' => NodeScalarValue::class,
                            'data' => 'a',
                            'path' => [],
                        ]
                    ]
                ],
            ],
            'Float data' => [
                1.2,
                [
                    [
                        'class' => ScalarEvent::class,
                        'value' => [
                            'class' => NodeScalarValue::class,
                            'data' => 1.2,
                            'path' => [],
                        ]
                    ],
                ],
            ],
            'Boolean data' => [
                true,
                [
                    [
                        'class' => ScalarEvent::class,
                        'value' => [
                            'class' => NodeScalarValue::class,
                            'data' => true,
                            'path' => [],
                        ],
                    ],
                ],
            ],
            'NULL data' => [
                null,
                [
                    [
                        'class' => ScalarEvent::class,
                        'value' => [
                            'class' => NodeScalarValue::class,
                            'data' => null,
                            'path' => [],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param $data
     * @dataProvider providerInvalidData
     */
    public function testConstruct_InvalidData_ThrowsMatchingException($data): void
    {
        $this->expectException(InvalidNodeDataException::class);
        new NodeScalarValue($data, Path::createEmpty());
    }

    public function providerInvalidData(): array
    {
        return [
            'Resource' => [STDERR],
            'Invalid object' => [new class {
            }],
            'Array' => [[]],
            'Object' => [(object) []],
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
