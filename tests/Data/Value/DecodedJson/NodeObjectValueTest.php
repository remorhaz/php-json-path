<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Test\Value\DecodedJson;

use function iterator_to_array;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Export\Decoder;
use Remorhaz\JSON\Data\Export\ExporterInterface;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeObjectValue;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeScalarValue;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeValueFactory;
use Remorhaz\JSON\Data\Event\ScalarEvent;
use Remorhaz\JSON\Data\Event\ValueEventInterface;
use Remorhaz\JSON\Data\Event\AfterObjectEvent;
use Remorhaz\JSON\Data\Event\BeforeObjectEvent;
use Remorhaz\JSON\Data\Event\PropertyEvent;
use Remorhaz\JSON\Data\Event\DataEventInterface;
use Remorhaz\JSON\Data\Event\ElementEventInterface;
use Remorhaz\JSON\Data\Event\PropertyEventInterface;
use Remorhaz\JSON\Data\Path\Path;
use Remorhaz\JSON\Data\Path\PathAwareInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;
use Remorhaz\JSON\Data\Iterator\ValueIteratorFactory;
use stdClass;

/**
 * @covers \Remorhaz\JSON\Data\Value\DecodedJson\NodeObjectValue
 */
class NodeObjectValueTest extends TestCase
{

    /**
     * @var ExporterInterface
     */
    private $exporter;

    public function setUp(): void
    {
        $this->exporter = new Decoder(new ValueIteratorFactory);
    }

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

        $actualEvents = iterator_to_array($value->createEventIterator(), false);
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
            'data' => $this->exportData($this->exporter->exportValue($value)),
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
}
