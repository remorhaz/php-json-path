<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Data\Test\Value\DecodedJson;

use function array_map;
use function get_class;
use function iterator_to_array;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\DecodedJson\Exception\InvalidNodeDataException;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeArrayValue;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeObjectValue;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeScalarValue;
use Remorhaz\JSON\Data\Value\DecodedJson\NodeValueFactory;
use Remorhaz\JSON\Data\Event\AfterArrayEvent;
use Remorhaz\JSON\Data\Event\AfterObjectEvent;
use Remorhaz\JSON\Data\Event\BeforeArrayEvent;
use Remorhaz\JSON\Data\Event\BeforeObjectEvent;
use Remorhaz\JSON\Data\Event\DataEventInterface;
use Remorhaz\JSON\Data\Event\ElementEvent;
use Remorhaz\JSON\Data\Event\PropertyEvent;
use Remorhaz\JSON\Data\Event\ScalarEvent;
use Remorhaz\JSON\Data\Path\Path;
use Remorhaz\JSON\Data\Value\ScalarValueInterface;
use Remorhaz\JSON\Data\Value\ValueInterface;
use const STDOUT;

/**
 * @covers \Remorhaz\JSON\Data\Value\DecodedJson\NodeValueFactory
 */
class NodeValueFactoryTest extends TestCase
{

    /**
     * @param mixed $data
     * @dataProvider providerScalarDataOrNull
     */
    public function testCreateValue_ScalarDataOrNull_ReturnsNodeScalarValue($data): void
    {
        $actualValue = (new NodeValueFactory)->createValue($data, new Path);
        self::assertInstanceOf(NodeScalarValue::class, $actualValue);
    }

    public function providerScalarDataOrNull(): array
    {
        return [
            'Null' => [null, null],
            'Integer' => [1, 1],
            'Float' => [0.5, 0.5],
            'String' => ['a', 'a'],
            'Bool' => [true, true],
        ];
    }

    /**
     * @param mixed $data
     * @dataProvider providerScalarDataOrNull
     */
    public function testCreateValue_ScalarValueAndGivenPath_ResultHasSamePathInstance($data): void
    {
        $path = new Path;
        $actualValue = (new NodeValueFactory)->createValue($data, $path);
        self::assertSame($path, $actualValue->getPath());
    }

    /**
     * @param mixed $data
     * @dataProvider providerScalarDataOrNull
     */
    public function testCreateValue_ScalarValueAndNoPath_ResultHasEmptyPath($data): void
    {
        $actualValue = (new NodeValueFactory)->createValue($data);
        self::assertEmpty($actualValue->getPath()->getElements());
    }

    /**
     * @param mixed $data
     * @param $expectedValue
     * @dataProvider providerScalarDataOrNull
     */
    public function testCreateValue_ScalarValue_ResultHasMatchingData($data, $expectedValue): void
    {
        $path = new Path;
        /** @var ScalarValueInterface $actualValue */
        $actualValue = (new NodeValueFactory)->createValue($data, $path);
        self::assertSame($expectedValue, $actualValue->getData());
    }

    public function testCreateValue_ArrayValue_ReturnsNodeArrayValue(): void
    {
        $actualValue = (new NodeValueFactory)->createValue([], new Path);
        self::assertInstanceOf(NodeArrayValue::class, $actualValue);
    }

    public function testCreateValue_ArrayValueAndGivenPath_ResultHasSamePathInstance(): void
    {
        $path = new Path;
        $actualValue = (new NodeValueFactory)->createValue([], $path);
        self::assertSame($path, $actualValue->getPath());
    }

    public function testCreateValue_ArrayValueAndNoPath_ResultHasEmptyPath(): void
    {
        $actualValue = (new NodeValueFactory)->createValue([]);
        self::assertEmpty($actualValue->getPath()->getElements());
    }

    public function testCreateValue_ObjectValue_ReturnsNodeObjectValue(): void
    {
        $actualValue = (new NodeValueFactory)->createValue((object) [], new Path);
        self::assertInstanceOf(NodeObjectValue::class, $actualValue);
    }

    public function testCreateValue_ObjectValueAndGivenPath_ResultHasSamePathInstance(): void
    {
        $path = new Path;
        $actualValue = (new NodeValueFactory)->createValue((object) [], $path);
        self::assertSame($path, $actualValue->getPath());
    }

    public function testCreateValue_ObjectValueAndNoPath_ResultHasSamePathInstance(): void
    {
        $actualValue = (new NodeValueFactory)->createValue((object) []);
        self::assertEmpty($actualValue->getPath()->getElements());
    }

    public function testCreateValue_NonScalarValue_ThrowsException(): void
    {
        $factory = new NodeValueFactory;
        $this->expectException(InvalidNodeDataException::class);
        $factory->createValue(STDOUT, new Path);
    }

    public function testCreateValue_NonMatchingObject_ThrowsException(): void
    {
        $factory = new NodeValueFactory;
        $this->expectException(InvalidNodeDataException::class);
        $factory->createValue(new Path, new Path);
    }

    /**
     * @param $data
     * @param array $expectedValue
     * @dataProvider providerValueEvents
     */
    public function testCreateValue_GivenValue_ResultGeneratesMathingEventsOnIteration(
        $data,
        array $expectedValue
    ): void {
        $actualValue = (new NodeValueFactory)->createValue($data, new Path);
        self::assertSame($expectedValue, $this->exportValueEvents($actualValue));
    }

    public function providerValueEvents(): array
    {
        return [
            'Scalar' => [1, [ScalarEvent::class]],
            'Array' => [
                [1],
                [
                    BeforeArrayEvent::class,
                    ElementEvent::class,
                    ScalarEvent::class,
                    AfterArrayEvent::class,
                ]
            ],
            'Object' => [
                (object) ['a' => 1],
                [
                    BeforeObjectEvent::class,
                    PropertyEvent::class,
                    ScalarEvent::class,
                    AfterObjectEvent::class,
                ]
            ],
        ];
    }

    private function exportValueEvents(ValueInterface $value): array
    {
        return array_map([$this, 'exportValueEvent'], iterator_to_array($value->createIterator(), false));
    }

    private function exportValueEvent(DataEventInterface $event): string
    {
        return get_class($event);
    }
}
