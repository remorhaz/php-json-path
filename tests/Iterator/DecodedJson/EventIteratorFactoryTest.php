<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Iterator\DecodedJson;

use Iterator;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Iterator\DecodedJson\EventExporter;
use Remorhaz\JSON\Path\Iterator\DecodedJson\EventIteratorFactory;
use Remorhaz\JSON\Path\Iterator\Fetcher;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\AfterObjectEvent;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\BeforeObjectEvent;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\ElementEvent;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\PropertyEvent;
use Remorhaz\JSON\Path\Iterator\Event\DataAwareEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\DataEventInterface;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\AfterArrayEvent;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\BeforeArrayEvent;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Exception\InvalidDataException;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Exception\InvalidElementKeyException;
use Remorhaz\JSON\Path\Iterator\DecodedJson\Event\ScalarEvent;
use Remorhaz\JSON\Path\Iterator\Event\ElementEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\IteratorAwareEventInterface;
use Remorhaz\JSON\Path\Iterator\Event\PropertyEventInterface;
use Remorhaz\JSON\Path\Iterator\Path;
use stdClass;

/**
 * @covers \Remorhaz\JSON\Path\Iterator\DecodedJson\EventIteratorFactory
 */
class EventIteratorFactoryTest extends TestCase
{

    /**
     * @param $data
     * @param array $expectedValue
     * @dataProvider providerValidData
     */
    public function testCreate_ConstructedWithValidData_GeneratesMatchingEventList(
        $data,
        array $expectedValue
    ): void {
        $iteratorFactory = new EventIteratorFactory($data, Path::createEmpty());

        $actualEvents = [];
        foreach ($iteratorFactory->createIterator() as $event) {
            $actualEvents[] = $event;
        }

        self::assertSame($expectedValue, $this->exportEvents(...$actualEvents));
    }

    public function providerValidData(): array
    {
        return [
            'Integer data' => [1, [['class' => ScalarEvent::class, 'path' => [], 'data' => 1,]]],
            'String data' => ['a', [['class' => ScalarEvent::class, 'path' => [], 'data' => 'a']]],
            'Float data' => [1.2, [['class' => ScalarEvent::class, 'path' => [], 'data' => 1.2,]]],
            'Boolean data' => [true, [['class' => ScalarEvent::class, 'path' => [], 'data' => true]]],
            'NULL data' => [null, [['class' => ScalarEvent::class, 'path' => [], 'data' => null]]],
            'Empty array' => [
                [],
                [
                    ['class' => BeforeArrayEvent::class, 'path' => [], 'data' => []],
                    ['class' => AfterArrayEvent::class, 'path' => [], 'data' => []],
                ],
            ],
            'Array with scalar element' => [
                [1],
                [
                    ['class' => BeforeArrayEvent::class, 'path' => [], 'data' => [1]],
                    ['class' => ElementEvent::class, 'path' => [], 'index' => 0],
                    ['class' => ScalarEvent::class, 'path' => [0], 'data' => 1],
                    ['class' => AfterArrayEvent::class, 'path' => [], 'data' => [1]],
                ],
            ],
            'Array with array element' => [
                [[1]],
                [
                    ['class' => BeforeArrayEvent::class, 'path' => [], 'data' => [[1]]],
                    ['class' => ElementEvent::class, 'path' => [], 'index' => 0],
                    ['class' => BeforeArrayEvent::class, 'path' => [0], 'data' => [1]],
                    ['class' => ElementEvent::class, 'path' => [0], 'index' => 0],
                    ['class' => ScalarEvent::class, 'path' => [0, 0], 'data' => 1],
                    ['class' => AfterArrayEvent::class, 'path' => [0], 'data' => [1]],
                    ['class' => AfterArrayEvent::class, 'path' => [], 'data' => [[1]]],
                ],
            ],
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
                    ['class' => ScalarEvent::class, 'path' => ['a'], 'data' => 1],
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
                    ['class' => ScalarEvent::class, 'path' => ['a', 'b'], 'data' => 1],
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

    /**
     * @param $data
     * @dataProvider providerInvalidData
     */
    public function testCreate_InvalidData_ThrowsMatchingException($data): void
    {
        $iteratorFactory = new EventIteratorFactory($data, Path::createEmpty());

        $this->expectException(InvalidDataException::class);
        /** @noinspection PhpStatementHasEmptyBodyInspection */
        /** @noinspection PhpUnusedLocalVariableInspection */
        foreach ($iteratorFactory->createIterator() as $event) {
        }
    }

    public function providerInvalidData(): array
    {
        return [
            'Resource' => [STDERR],
            'Invalid object' => [new class {}],
        ];
    }

    /**
     * @param array $data
     * @dataProvider providerArrayWithInvalidIndex
     */
    public function testCreate_ArrayDataWithInvalidIndex_ThrowsException(array $data): void
    {
        $iteratorFactory = new EventIteratorFactory($data, Path::createEmpty());

        $this->expectException(InvalidElementKeyException::class);
        /** @noinspection PhpStatementHasEmptyBodyInspection */
        /** @noinspection PhpUnusedLocalVariableInspection */
        foreach ($iteratorFactory->createIterator() as $event) {
        }
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
            'path' => $event->getPath()->getElements(),
        ];

        if ($event instanceof DataAwareEventInterface) {
            $result += ['data' => $this->exportData($event->getData())];
        }

        if ($event instanceof IteratorAwareEventInterface) {
            $result += ['data' => $this->exportData($this->exportIterator($event->createIterator()))];
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
