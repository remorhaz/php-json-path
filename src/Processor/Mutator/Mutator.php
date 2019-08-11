<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Mutator;

use Remorhaz\JSON\Data\Event\ValueWalkerInterface;
use Remorhaz\JSON\Data\Export\EventDecoderInterface;
use Remorhaz\JSON\Data\Path\Path;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;

final class Mutator implements MutatorInterface
{

    private $valueWalker;

    private $eventDecoder;

    public function __construct(ValueWalkerInterface $walker, EventDecoderInterface $eventDecoder)
    {
        $this->valueWalker = $walker;
        $this->eventDecoder = $eventDecoder;
    }

    public function deletePaths(NodeValueInterface $rootNode, PathInterface ...$paths): ?NodeValueInterface
    {
        $modifier = new DeleteMutation(...$paths);
        $events =  $this
            ->valueWalker
            ->createMutableEventIterator($rootNode, new Path, $modifier);

        return $this
            ->eventDecoder
            ->exportEvents($events);
    }

    public function replacePaths(
        NodeValueInterface $rootNode,
        NodeValueInterface $newNode,
        PathInterface ...$paths
    ): NodeValueInterface {
        $modifier = new ReplaceMutation($this->valueWalker, $newNode, ...$paths);
        $events =  $this
            ->valueWalker
            ->createMutableEventIterator($rootNode, new Path, $modifier);

        return $this
            ->eventDecoder
            ->exportExistingEvents($events);
    }
}
