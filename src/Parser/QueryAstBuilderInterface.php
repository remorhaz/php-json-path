<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Parser;

interface QueryAstBuilderInterface
{

    public function getInput(): int;

    public function setOutput(int $id): void;

    public function createFilterContext(int $id): int;

    public function split(int $id): int;

    public function evaluate(int $sourceId, int $id): int;

    public function filter(int $contextId, int $evaluatedId): int;

    public function calculateLogicalOr(int $leftId, int $rightId): int;

    public function calculateLogicalAnd(int $leftId, int $rightId): int;

    public function calculateLogicalNot(int $id): int;

    public function calculateIsEqual(int $leftId, int $rightId): int;

    public function calculateIsGreater(int $leftId, int $rightId): int;

    public function calculateIsRegExp(string $pattern, int $id): int;

    public function fetchChildren(int $id, int $matcherId): int;

    public function fetchChildrenDeep(int $id, int $matcherId): int;

    public function matchAnyChild(): int;

    public function matchPropertyStrictly(int $id): int;

    public function matchElementStrictly(int $id): int;

    public function calculateAggregate(string $name, int $id): int;

    public function populateLiteralScalar(int $sourceId, $value): int;

    public function populateLiteralArray(int $sourceId, int ...$valueIdList): int;

    public function populateIndexList(int $sourceId, int ...$indexList): int;

    public function populateIndexSlice(int $sourceId, ?int $start, ?int $end, ?int $step): int;

    public function populateNameList(int $sourceId, string ...$nameList): int;
}
