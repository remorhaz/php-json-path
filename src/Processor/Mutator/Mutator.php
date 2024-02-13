<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor\Mutator;

use Remorhaz\JSON\Data\Export\EventDecoderInterface;
use Remorhaz\JSON\Data\Path\Path;
use Remorhaz\JSON\Data\Path\PathInterface;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Data\Walker\ValueWalkerInterface;

final class Mutator implements MutatorInterface
{
    public function __construct(
        private readonly ValueWalkerInterface $valueWalker,
        private readonly EventDecoderInterface $eventDecoder,
    ) {
    }

    public function deletePaths(NodeValueInterface $rootNode, PathInterface ...$paths): ?NodeValueInterface
    {
        $modifier = new DeleteMutation(...$paths);
        $events =  $this
            ->valueWalker
            ->createMutableEventIterator($rootNode, new Path(), $modifier);

        return $this
            ->eventDecoder
            ->exportEvents($events);
    }

    public function replacePaths(
        NodeValueInterface $rootNode,
        NodeValueInterface $newNode,
        PathInterface ...$paths,
    ): NodeValueInterface {
        $modifier = new ReplaceMutation($newNode, ...$paths);
        $events =  $this
            ->valueWalker
            ->createMutableEventIterator($rootNode, new Path(), $modifier);

        return $this
            ->eventDecoder
            ->exportExistingEvents($events);
    }
}
