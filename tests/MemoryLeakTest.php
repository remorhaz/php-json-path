<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Path\Query\QueryFactory;

use function array_fill;
use function array_unique;
use function memory_get_usage;

#[CoversNothing]
class MemoryLeakTest extends TestCase
{
    public function testQueryCompilation(): void
    {
        $queryFactory = QueryFactory::create();
        $attemptCount = 100;
        $memory = array_fill(0, $attemptCount, 0);
        for ($i = 0; $i < $attemptCount; $i++) {
            $queryFactory
                ->createQuery('$.a.b[?(@.c==1)].d')
                ->getCapabilities();
            $memory[$i] = memory_get_usage();
        }
        self::assertCount(1, array_unique($memory));
    }
}
