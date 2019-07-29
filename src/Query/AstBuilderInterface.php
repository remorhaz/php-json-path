<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

interface AstBuilderInterface
{

    public function getInput(): int;

    public function setOutput(int $id, bool $isDefinite, bool $isPath): void;

    public function fetchFilterContext(int $id): int;

    public function splitFilterContext(int $id): int;

    public function joinFilterResults(int $evaluatedId, int $contextId): int;

    public function evaluate(int $sourceId, int $id): int;

    public function filter(int $contextId, int $evaluatedId): int;

    public function evaluateLogicalOr(int $leftId, int $rightId): int;

    public function evaluateLogicalAnd(int $leftId, int $rightId): int;

    public function evaluateLogicalNot(int $id): int;

    public function calculateIsEqual(int $leftId, int $rightId): int;

    public function calculateIsGreater(int $leftId, int $rightId): int;

    public function calculateIsRegExp(string $pattern, int $id): int;

    public function fetchChildren(int $id, int $matcherId): int;

    public function fetchChildrenDeep(int $id, int $matcherId): int;

    public function matchAnyChild(): int;

    public function matchPropertyStrictly(string ...$names): int;

    public function matchElementStrictly(int ...$indexes): int;

    public function matchElementSlice(?int $start, ?int $end, ?int $step): int;

    public function aggregate(string $name, int $id): int;

    public function populateLiteral(int $sourceId, int $valueId): int;

    public function populateArrayElements(int $sourceId, int $arrayId): int;

    public function createScalar($value): int;

    public function createArray(): int;

    public function appendToArray(int $arrayId, int $valueId): int;

    public function createLiteralArray(int $sourceId, int $elementsId): int;
}
