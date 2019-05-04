<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Iterator\DecodedJson;

use Iterator;
use function iterator_to_array;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Iterator\DecodedJson\EventExporter;
use Remorhaz\JSON\Path\Iterator\DecodedJson\NodeObjectValue;
use Remorhaz\JSON\Path\Iterator\DecodedJson\NodeValueFactory;
use Remorhaz\JSON\Path\Iterator\Event\ValueEventInterface;
use Remorhaz\JSON\Path\Iterator\Fetcher;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\AfterObjectEvent;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\BeforeObjectEvent;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\PropertyEvent;
use Remorhaz\JSON\Path\Iterator\Event\DataAwareEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\DataEventInterface;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\NodeScalarEvent;
use Remorhaz\JSON\Path\Iterator\Event\ElementEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\PropertyEventInterface;
use Remorhaz\JSON\Path\Iterator\Path;
use Remorhaz\JSON\Path\Iterator\PathAwareInterface;
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
        $value = new NodeObjectValue($data, Path::createEmpty(), new NodeValueFactory);

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
                        'path' => [],
                        'data' => ['class' => stdClass::class, 'data' => []],
                    ],
                    [
                        'class' => AfterObjectEvent::class,
                        'path' => [],
                        'data' => ['class' => stdClass::class, 'data' => []],
                    ],
                ],
            ],
            'Object with scalar property' => [
                (object) ['a' => 1],
                [
                    [
                        'class' => BeforeObjectEvent::class,
                        'path' => [],
                        'data' => ['class' => stdClass::class, 'data' => ['a' => 1]],
                    ],
                    ['class' => PropertyEvent::class, 'path' => [], 'name' => 'a'],
                    ['class' => NodeScalarEvent::class, 'path' => ['a'], 'data' => 1],
                    [
                        'class' => AfterObjectEvent::class,
                        'path' => [],
                        'data' => ['class' => stdClass::class, 'data' => ['a' => 1]],
                    ],
                ],
            ],
            'Object with object property' => [
                (object) ['a' => (object) ['b' => 1]],
                [
                    [
                        'class' => BeforeObjectEvent::class,
                        'path' => [],
                        'data' => [
                            'class' => stdClass::class,
                            'data' => ['a' => ['class' => stdClass::class, 'data' => ['b' => 1]]],
                        ],
                    ],
                    ['class' => PropertyEvent::class, 'path' => [], 'name' => 'a'],
                    [
                        'class' => BeforeObjectEvent::class,
                        'path' => ['a'],
                        'data' => ['class' => stdClass::class, 'data' => ['b' => 1]],
                    ],
                    ['class' => PropertyEvent::class, 'path' => ['a'], 'name' => 'b'],
                    ['class' => NodeScalarEvent::class, 'path' => ['a', 'b'], 'data' => 1],
                    [
                        'class' => AfterObjectEvent::class,
                        'path' => ['a'],
                        'data' => ['class' => stdClass::class, 'data' => ['b' => 1]],
                    ],
                    [
                        'class' => AfterObjectEvent::class,
                        'path' => [],
                        'data' => [
                            'class' => stdClass::class,
                            'data' => ['a' => ['class' => stdClass::class, 'data' => ['b' => 1]]],
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
            $value = $event->getValue();
            if ($value instanceof PathAwareInterface) {
                $result += ['path' => $value->getPath()->getElements()];
            }
        }

        if ($event instanceof DataAwareEventInterface) {
            $result += ['data' => $this->exportData($event->getData())];
        }

        if ($event instanceof ValueEventInterface) {
            $result += ['data' => $this->exportData($this->exportIterator($event->getValue()->createIterator()))];
        }

        if ($event instanceof ElementEventInterface) {
            $result += ['index' => $event->getIndex()];
        }

        if ($event instanceof PropertyEventInterface) {
            $result += ['name' => $event->getName()];
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
        return (new EventExporter(new Fetcher))->export($iterator);
    }
}
