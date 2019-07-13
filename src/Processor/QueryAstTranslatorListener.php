<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Processor;

use function array_map;
use PhpParser\Builder\Namespace_;
use PhpParser\BuilderFactory;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Stmt\Return_;
use PhpParser\PrettyPrinter\Standard;
use Remorhaz\JSON\Path\Parser\QueryAstNodeType;
use Remorhaz\UniLex\AST\AbstractTranslatorListener;
use Remorhaz\UniLex\AST\Node;
use Remorhaz\UniLex\Exception as UnilexException;
use Remorhaz\UniLex\Stack\PushInterface;

final class QueryAstTranslatorListener extends AbstractTranslatorListener
{

    private $php;

    private $references = [];

    /**
     * @var Namespace_
     */
    private $ns;

    private $self;

    public function __construct()
    {
        $this->php = new BuilderFactory;
    }

    public function onStart(Node $node): void
    {
        $this->ns = $this
            ->php
            ->namespace(null);

        $this->self = $this->php->var('this');
    }

    public function onFinish(): void
    {
        /*var_export(
            (new Standard)->prettyPrint([$this->ns->getNode()])
        );*/
    }

    public function onBeginProduction(Node $node, PushInterface $stack): void
    {
        $stack->push(...$node->getChildList());
    }

    /**
     * @param Node $node
     * @throws UnilexException
     */
    public function onFinishProduction(Node $node): void
    {
        if ($this->hasReference($node)) {
            return;
        }
        switch ($node->getName()) {
            case QueryAstNodeType::GET_INPUT:
                $this->setReference($node, $this->php->var('input'));
                break;

            case QueryAstNodeType::SET_OUTPUT:
                $this
                    ->ns
                    ->addStmt(
                        new Return_($this->getReference($node->getChild(0)))
                    );
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
                    $this->getReference($node->getChild(1))
                );
                break;

            case QueryAstNodeType::FETCH_CHILDREN_DEEP:
                $this->addMethodCall(
                    $node,
                    'fetchChildrenDeep',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1))
                );
                break;

            case QueryAstNodeType::MATCH_ANY_CHILD:
                $this->addMethodCall($node, 'matchAnyChild');
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
                $this
                    ->ns
                    ->addStmt(new Assign($this->createReference($node), $value));
                break;

            case QueryAstNodeType::CREATE_ARRAY:
                // [ X:APPEND_TO_ARRAY ]
                $items = [];
                foreach ($node->getChildList() as $child) {
                    $items[] = $this->getReference($child);
                }
                $this
                    ->ns
                    ->addStmt(
                        new Assign(
                            $this->createReference($node),
                            $this->php->val($items)
                        )
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
            throw new Exception\ReferenceAlreadyExistsException;
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
            throw new Exception\ReferenceNotFoundException;
        }

        return $this->references[$node->getId()];
    }

    private function addMethodCall(Node $node, string $method, Expr ...$args): void
    {
        $methodCall = $this
            ->php
            ->methodCall($this->self, $method, $args);
        $this
            ->ns
            ->addStmt(new Assign($this->createReference($node), $methodCall));
    }
}
