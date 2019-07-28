<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use function array_map;
use function array_reverse;
use PhpParser\BuilderFactory;
use PhpParser\Node as PhpAstNode;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;
use PhpParser\PrettyPrinter\Standard;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Runtime\EvaluatorInterface;
use Remorhaz\JSON\Path\Value\ValueListInterface;
use Remorhaz\JSON\Path\Runtime\RuntimeInterface;
use Remorhaz\UniLex\AST\AbstractTranslatorListener;
use Remorhaz\UniLex\AST\Node as QueryAstNode;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\Stack\PushInterface;

final class CallbackBuilder extends AbstractTranslatorListener implements CallbackBuilderInterface
{

    private const ARG_RUNTIME = 'runtime';

    private const ARG_EVALUATOR = 'evaluator';

    private const ARG_INPUT = 'input';

    private $php;

    private $references = [];

    private $runtime;

    private $evaluator;

    private $input;

    private $stmts = [];

    private $queryCallback;

    private $capabilities;

    public function __construct()
    {
        $this->php = new BuilderFactory;
    }

    public function getCallback(): callable
    {
        if (isset($this->queryCallback)) {
            return $this->queryCallback;
        }

        throw new Exception\QueryCallbackNotFoundException;
    }

    public function getCapabilities(): CapabilitiesInterface
    {
        if (isset($this->capabilities)) {
            return $this->capabilities;
        }

        throw new Exception\CapabilitiesNotFoundException;
    }

    public function onStart(QueryAstNode $node): void
    {
        $this->runtime = $this->php->var(self::ARG_RUNTIME);
        $this->evaluator = $this->php->var(self::ARG_EVALUATOR);
        $this->input = $this->php->var(self::ARG_INPUT);
    }

    public function onFinish(): void
    {
        $inputParam = $this
            ->php
            ->param(self::ARG_INPUT)
            ->setType(NodeValueInterface::class)
            ->getNode();
        $runtimeParam = $this
            ->php
            ->param(self::ARG_RUNTIME)
            ->setType(RuntimeInterface::class)
            ->getNode();
        $evaluatorParam = $this
            ->php
            ->param(self::ARG_EVALUATOR)
            ->setType(EvaluatorInterface::class)
            ->getNode();
        $stmts = array_map(
            function (PhpAstNode $stmt): PhpAstNode {
                return $stmt instanceof Expr ? new Expression($stmt): $stmt;
            },
            $this->stmts
        );

        $closure = new Expr\Closure(
            [
                'stmts' => $stmts,
                'returnType' => ValueListInterface::class,
                'params' => [$inputParam, $runtimeParam, $evaluatorParam],
            ]
        );
        $return = new Return_($closure);

        $callbackCode = (new Standard)->prettyPrint([$return]);
        $this->queryCallback = eval($callbackCode);
    }

    public function onBeginProduction(QueryAstNode $node, PushInterface $stack): void
    {
        $stack->push(...array_reverse($node->getChildList()));
    }

    /**
     * @param QueryAstNode $node
     * @throws UniLexException
     */
    public function onFinishProduction(QueryAstNode $node): void
    {
        if ($this->hasReference($node)) {
            return;
        }
        switch ($node->getName()) {
            case AstNodeType::GET_INPUT:
                $this->addRuntimeMethodCall(
                    $node,
                    'getInput',
                    $this->input
                );
                break;

            case AstNodeType::SET_OUTPUT:
                $this->capabilities = new Capabilities(
                    $node->getAttribute('is_definite'),
                    $node->getAttribute('is_path')
                );
                $this->stmts[] = new Return_($this->getReference($node->getChild(0)));
                break;

            case AstNodeType::CREATE_FILTER_CONTEXT:
                $this->addRuntimeMethodCall(
                    $node,
                    'createFilterContext',
                    $this->getReference($node->getChild(0))
                );
                break;

            case AstNodeType::SPLIT:
                $this->addRuntimeMethodCall(
                    $node,
                    'split',
                    $this->getReference($node->getChild(0))
                );
                break;

            case AstNodeType::EVALUATE:
                $this->addEvaluatorMethodCall(
                    $node,
                    'evaluate',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1))
                );
                break;

            case AstNodeType::FILTER:
                $this->addRuntimeMethodCall(
                    $node,
                    'filter',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1))
                );
                break;

            case AstNodeType::EVALUATE_LOGICAL_OR:
                $this->addEvaluatorMethodCall(
                    $node,
                    'logicalOr',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1))
                );
                break;

            case AstNodeType::EVALUATE_LOGICAL_AND:
                $this->addEvaluatorMethodCall(
                    $node,
                    'logicalAnd',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1))
                );
                break;

            case AstNodeType::EVALUATE_LOGICAL_NOT:
                $this->addEvaluatorMethodCall(
                    $node,
                    'logicalNot',
                    $this->getReference($node->getChild(0))
                );
                break;

            case AstNodeType::CALCULATE_IS_EQUAL:
                $this->addEvaluatorMethodCall(
                    $node,
                    'isEqual',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1))
                );
                break;

            case AstNodeType::CALCULATE_IS_GREATER:
                $this->addEvaluatorMethodCall(
                    $node,
                    'isGreater',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1))
                );
                break;

            case AstNodeType::CALCULATE_IS_REGEXP:
                $this->addEvaluatorMethodCall(
                    $node,
                    'isRegExp',
                    $this->php->val($node->getAttribute('pattern')),
                    $this->getReference($node->getChild(0))
                );
                break;

            case AstNodeType::FETCH_CHILDREN:
                $this->addRuntimeMethodCall(
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

            case AstNodeType::FETCH_CHILDREN_DEEP:
                $this->addRuntimeMethodCall(
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

            case AstNodeType::MATCH_ANY_CHILD:
                $this->addRuntimeMethodCall(
                    $node,
                    'matchAnyChild',
                    $this->getReference($node->getChild(0))
                );
                break;

            case AstNodeType::MATCH_PROPERTY_STRICTLY:
                $this->addRuntimeMethodCall(
                    $node,
                    'matchPropertyStrictly',
                    $this->getReference($node->getChild(0)),
                );
                break;

            case AstNodeType::MATCH_ELEMENT_STRICTLY:
                $this->addRuntimeMethodCall(
                    $node,
                    'matchElementStrictly',
                    $this->getReference($node->getChild(0)),
                );
                break;

            case AstNodeType::MATCH_ELEMENT_SLICE:
                $this->addRuntimeMethodCall(
                    $node,
                    'matchElementSlice',
                    $this->getReference($node->getChild(0)),
                    $this
                        ->php
                        ->val($node->getAttribute('hasStart') ? $node->getAttribute('start') : null),
                    $this
                        ->php
                        ->val($node->getAttribute('hasEnd') ? $node->getAttribute('end') : null),
                    $this
                        ->php
                        ->val($node->getAttribute('step')),
                );
                break;

            case AstNodeType::AGGREGATE:
                $this->addEvaluatorMethodCall(
                    $node,
                    'aggregate',
                    $this->php->val($node->getAttribute('name')),
                    $this->getReference($node->getChild(0)),
                );
                break;

            case AstNodeType::POPULATE_LITERAL:
                $this->addRuntimeMethodCall(
                    $node,
                    'populateLiteral',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1)),
                );
                break;

            case AstNodeType::POPULATE_ARRAY_ELEMENTS:
                $this->addRuntimeMethodCall(
                    $node,
                    'populateArrayElements',
                    $this->getReference($node->getChild(0)),
                    new Arg(
                        $this->getReference($node->getChild(1)),
                        false,
                        true
                    ),
                );
                break;

            case AstNodeType::POPULATE_INDEX_LIST:
                $this->addRuntimeMethodCall(
                    $node,
                    'populateIndexList',
                    $this->getReference($node->getChild(0)),
                    ...array_map(
                        [$this->php, 'val'],
                        $node->getAttribute('indexList')
                    ),
                );
                break;

            case AstNodeType::POPULATE_NAME_LIST:
                $this->addRuntimeMethodCall(
                    $node,
                    'populateNameList',
                    $this->getReference($node->getChild(0)),
                    ...array_map(
                        [$this->php, 'val'],
                        $node->getAttribute('nameList'),
                    ),
                );
                break;

            case AstNodeType::CREATE_SCALAR:
                $attributes = $node->getAttributeList();
                // TODO: allow accessing null attributes
                $value = $this->php->val($attributes['value'] ?? null);
                $this->addRuntimeMethodCall(
                    $node,
                    'createScalar',
                    $value,
                );
                break;

            case AstNodeType::CREATE_ARRAY:
                // [ X:APPEND_TO_ARRAY ]
                $items = [];
                foreach ($node->getChildList() as $child) {
                    $items[] = $this->getReference($child);
                }
                $this->stmts[] = new Assign(
                    $this->createReference($node),
                    $this->php->val($items),
                );
                break;

            case AstNodeType::APPEND_TO_ARRAY:
                // [ 0:<value> ]
                $this->setReference(
                    $node,
                    $this->getReference($node->getChild(0))
                );
                break;

            case AstNodeType::CREATE_LITERAL_ARRAY:
                $this->addRuntimeMethodCall(
                    $node,
                    'createArray',
                    $this->getReference($node->getChild(0)),
                    new Arg(
                        $this->getReference($node->getChild(1)),
                        false,
                        true,
                    ),
                );
                break;
        }
    }

    private function getVarName(QueryAstNode $node): string
    {
        return "var{$node->getId()}";
    }

    private function createReference(QueryAstNode $node): Expr
    {
        $reference = $this->php->var($this->getVarName($node));
        $this->setReference($node, $reference);

        return $reference;
    }

    private function setReference(QueryAstNode $node, Expr $expr): void
    {
        if (isset($this->references[$node->getId()])) {
            throw new Exception\ReferenceAlreadyExistsException($node->getId());
        }

        $this->references[$node->getId()] = $expr;
    }

    private function hasReference(QueryAstNode $node): bool
    {
        return isset($this->references[$node->getId()]);
    }

    private function getReference(QueryAstNode $node): Expr
    {
        if (!isset($this->references[$node->getId()])) {
            throw new Exception\ReferenceNotFoundException($node->getId());
        }

        return $this->references[$node->getId()];
    }

    private function addRuntimeMethodCall(QueryAstNode $node, string $method, PhpAstNode ...$args): void
    {
        $methodCall = $this
            ->php
            ->methodCall($this->runtime, $method, $args);
        $this->stmts[] = new Assign($this->createReference($node), $methodCall);
    }

    private function addEvaluatorMethodCall(QueryAstNode $node, string $method, PhpAstNode ...$args): void
    {
        $methodCall = $this
            ->php
            ->methodCall($this->evaluator, $method, $args);
        $this->stmts[] = new Assign($this->createReference($node), $methodCall);
    }
}
