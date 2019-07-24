<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use function array_map;
use PhpParser\BuilderFactory;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;
use PhpParser\PrettyPrinter\Standard;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Value\ValueListInterface;
use Remorhaz\JSON\Path\Runtime\RuntimeInterface;
use Remorhaz\UniLex\AST\AbstractTranslatorListener;
use Remorhaz\UniLex\AST\Node;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\Stack\PushInterface;

final class QueryCallbackBuilder extends AbstractTranslatorListener implements QueryCallbackBuilderInterface
{

    private $php;

    private $references = [];

    private $runtime;

    private $input;

    private $stmts = [];

    private $queryCallback;

    private $isDefinite;

    public function __construct()
    {
        $this->php = new BuilderFactory;
    }

    public function getQueryCallback(): callable
    {
        if (isset($this->queryCallback)) {
            return $this->queryCallback;
        }

        throw new Exception\QueryCallbackNotFoundException;
    }

    public function isDefinite(): bool
    {
        if (isset($this->isDefinite)) {
            return $this->isDefinite;
        }

        throw new Exception\IsDefiniteFlagNotFoundException;
    }

    public function onStart(Node $node): void
    {
        $this->runtime = $this->php->var('runtime');
        $this->input = $this->php->var('input');
    }

    public function onFinish(): void
    {
        $runtimeParam = $this
            ->php
            ->param('runtime')
            ->setType(RuntimeInterface::class)
            ->getNode();
        $inputParam = $this
            ->php
            ->param('input')
            ->setType(NodeValueInterface::class)
            ->getNode();
        $stmts = array_map(
            function (\PhpParser\Node $stmt): \PhpParser\Node {
                return $stmt instanceof Expr ? new Expression($stmt): $stmt;
            },
            $this->stmts
        );

        $closure = new Expr\Closure(
            [
                'stmts' => $stmts,
                'returnType' => ValueListInterface::class,
                'params' => [$runtimeParam, $inputParam],
            ]
        );
        $return = new Return_($closure);

        $callbackCode = (new Standard)->prettyPrint([$return]);
        $this->queryCallback = eval($callbackCode);
    }

    public function onBeginProduction(Node $node, PushInterface $stack): void
    {
        $stack->push(...$node->getChildList());
    }

    /**
     * @param Node $node
     * @throws UniLexException
     */
    public function onFinishProduction(Node $node): void
    {
        if ($this->hasReference($node)) {
            return;
        }
        switch ($node->getName()) {
            case QueryAstNodeType::GET_INPUT:
                $this->addMethodCall(
                    $node,
                    'getInput',
                    $this->input
                );
                break;

            case QueryAstNodeType::SET_OUTPUT:
                $this->isDefinite = $node->getAttribute('is_definite');
                $this->stmts[] = new Return_($this->getReference($node->getChild(0)));
                break;

            case QueryAstNodeType::CREATE_FILTER_CONTEXT:
                $this->addMethodCall(
                    $node,
                    'createFilterContext',
                    $this->getReference($node->getChild(0))
                );
                break;

            case QueryAstNodeType::SPLIT:
                $this->addMethodCall(
                    $node,
                    'split',
                    $this->getReference($node->getChild(0))
                );
                break;

            case QueryAstNodeType::EVALUATE:
                $this->addMethodCall(
                    $node,
                    'evaluate',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1))
                );
                break;

            case QueryAstNodeType::FILTER:
                $this->addMethodCall(
                    $node,
                    'filter',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1))
                );
                break;

            case QueryAstNodeType::EVALUATE_LOGICAL_OR:
                $this->addMethodCall(
                    $node,
                    'evaluateLogicalOr',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1))
                );
                break;

            case QueryAstNodeType::EVALUATE_LOGICAL_AND:
                $this->addMethodCall(
                    $node,
                    'evaluateLogicalAnd',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1))
                );
                break;

            case QueryAstNodeType::EVALUATE_LOGICAL_NOT:
                $this->addMethodCall(
                    $node,
                    'evaluateLogicalNot',
                    $this->getReference($node->getChild(0))
                );
                break;

            case QueryAstNodeType::CALCULATE_IS_EQUAL:
                $this->addMethodCall(
                    $node,
                    'calculateIsEqual',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1))
                );
                break;

            case QueryAstNodeType::CALCULATE_IS_GREATER:
                $this->addMethodCall(
                    $node,
                    'calculateIsGreater',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1))
                );
                break;

            case QueryAstNodeType::CALCULATE_IS_REGEXP:
                $this->addMethodCall(
                    $node,
                    'calculateIsRegExp',
                    $this->php->val($node->getAttribute('pattern')),
                    $this->getReference($node->getChild(0))
                );
                break;

            case QueryAstNodeType::FETCH_CHILDREN:
                $this->addMethodCall(
                    $node,
                    'fetchChildren',
                    $this->getReference($node->getChild(0)),
                    new Arg(
                        $this->getReference($node->getChild(1)),
                        false,
                        true
                    )
                );
                break;

            case QueryAstNodeType::FETCH_CHILDREN_DEEP:
                $this->addMethodCall(
                    $node,
                    'fetchChildrenDeep',
                    $this->getReference($node->getChild(0)),
                    new Arg(
                        $this->getReference($node->getChild(1)),
                        false,
                        true
                    )
                );
                break;

            case QueryAstNodeType::MATCH_ANY_CHILD:
                $this->addMethodCall(
                    $node,
                    'matchAnyChild',
                    $this->getReference($node->getChild(0))
                );
                break;

            case QueryAstNodeType::MATCH_PROPERTY_STRICTLY:
                $this->addMethodCall(
                    $node,
                    'matchPropertyStrictly',
                    $this->getReference($node->getChild(0)),
                );
                break;

            case QueryAstNodeType::MATCH_ELEMENT_STRICTLY:
                $this->addMethodCall(
                    $node,
                    'matchElementStrictly',
                    $this->getReference($node->getChild(0)),
                );
                break;

            case QueryAstNodeType::AGGREGATE:
                $this->addMethodCall(
                    $node,
                    'aggregate',
                    $this->php->val($node->getAttribute('name')),
                    $this->getReference($node->getChild(0)),
                );
                break;

            case QueryAstNodeType::POPULATE_LITERAL:
                $this->addMethodCall(
                    $node,
                    'populateLiteral',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1))
                );
                break;

            case QueryAstNodeType::POPULATE_LITERAL_ARRAY:
                $this->addMethodCall(
                    $node,
                    'populateLiteralArray',
                    $this->getReference($node->getChild(0)),
                    new Arg(
                        $this->getReference($node->getChild(1)),
                        false,
                        true
                    )
                );
                break;

            case QueryAstNodeType::POPULATE_INDEX_LIST:
                $this->addMethodCall(
                    $node,
                    'populateIndexList',
                    $this->getReference($node->getChild(0)),
                    ...array_map(
                        [$this->php, 'val'],
                        $node->getAttribute('indexList')
                    )
                );
                break;

            case QueryAstNodeType::POPULATE_INDEX_SLICE:
                $attributes = $node->getAttributeList();
                $this->addMethodCall(
                    $node,
                    'populateIndexSlice',
                    $this->getReference($node->getChild(0)),
                    $this->php->val($attributes['start'] ?? null),
                    $this->php->val($attributes['end'] ?? null),
                    $this->php->val($attributes['step'] ?? null)
                );

                break;

            case QueryAstNodeType::POPULATE_NAME_LIST:
                $this->addMethodCall(
                    $node,
                    'populateNameList',
                    $this->getReference($node->getChild(0)),
                    ...array_map(
                        [$this->php, 'val'],
                        $node->getAttribute('nameList')
                    )
                );
                break;

            case QueryAstNodeType::CREATE_SCALAR:
                $attributes = $node->getAttributeList();
                // TODO: allow accessing null attributes
                $value = $this->php->val($attributes['value'] ?? null);
                $this->addMethodCall(
                    $node,
                    'createScalar',
                    $value
                );
                break;

            case QueryAstNodeType::CREATE_ARRAY:
                // [ X:APPEND_TO_ARRAY ]
                $items = [];
                foreach ($node->getChildList() as $child) {
                    $items[] = $this->getReference($child);
                }
                $this->stmts[] = new Assign(
                    $this->createReference($node),
                    $this->php->val($items)
                );
                break;

            case QueryAstNodeType::APPEND_TO_ARRAY:
                // [ 0:<value> ]
                $this->setReference(
                    $node,
                    $this->getReference($node->getChild(0))
                );
                break;
        }
    }

    private function getVarName(Node $node): string
    {
        return "var{$node->getId()}";
    }

    private function createReference(Node $node): Expr
    {
        $reference = $this->php->var($this->getVarName($node));
        $this->setReference($node, $reference);

        return $reference;
    }

    private function setReference(Node $node, Expr $expr): void
    {
        if (isset($this->references[$node->getId()])) {
            throw new Exception\ReferenceAlreadyExistsException($node->getId());
        }

        $this->references[$node->getId()] = $expr;
    }

    private function hasReference(Node $node): bool
    {
        return isset($this->references[$node->getId()]);
    }

    private function getReference(Node $node): Expr
    {
        if (!isset($this->references[$node->getId()])) {
            throw new Exception\ReferenceNotFoundException($node->getId());
        }

        return $this->references[$node->getId()];
    }

    private function addMethodCall(Node $node, string $method, \PhpParser\Node ...$args): void
    {
        $methodCall = $this
            ->php
            ->methodCall($this->runtime, $method, $args);
        $this->stmts[] = new Assign($this->createReference($node), $methodCall);
    }
}
