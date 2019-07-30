<?php
declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use Remorhaz\JSON\Path\Value\NodeValueListInterface;
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
            ->setType(NodeValueListInterface::class)
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
                $this->setReference($node, $this->input);
                break;

            case AstNodeType::SET_OUTPUT:
                $this->capabilities = new Capabilities(
                    $node->getAttribute('is_definite'),
                    $node->getAttribute('is_path'),
                );
                $this->stmts[] = new Return_($this->getReference($node->getChild(0)));
                break;

            case AstNodeType::FETCH_FILTER_CONTEXT:
                /** @see RuntimeInterface::fetchFilterContext() */
                $this->addRuntimeMethodCall(
                    $node,
                    'fetchFilterContext',
                    $this->getReference($node->getChild(0)),
                );
                break;

            case AstNodeType::SPLIT_FILTER_CONTEXT:
                /** @see RuntimeInterface::splitFilterContext() */
                $this->addRuntimeMethodCall(
                    $node,
                    'splitFilterContext',
                    $this->getReference($node->getChild(0)),
                );
                break;

            case AstNodeType::JOIN_FILTER_RESULTS:
                /** @see RuntimeInterface::joinFilterResults() */
                $this->addRuntimeMethodCall(
                    $node,
                    'joinFilterResults',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1)),
                );
                break;

            case AstNodeType::EVALUATE:
                /** @see EvaluatorInterface::evaluate() */
                $this->addEvaluatorMethodCall(
                    $node,
                    'evaluate',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1)),
                );
                break;

            case AstNodeType::FILTER:
                /** @see RuntimeInterface::fetchFilteredValues() */
                $this->addRuntimeMethodCall(
                    $node,
                    'fetchFilteredValues',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1)),
                );
                break;

            case AstNodeType::EVALUATE_LOGICAL_OR:
                /** @see EvaluatorInterface::logicalOr() */
                $this->addEvaluatorMethodCall(
                    $node,
                    'logicalOr',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1)),
                );
                break;

            case AstNodeType::EVALUATE_LOGICAL_AND:
                /** @see EvaluatorInterface::logicalAnd() */
                $this->addEvaluatorMethodCall(
                    $node,
                    'logicalAnd',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1)),
                );
                break;

            case AstNodeType::EVALUATE_LOGICAL_NOT:
                /** @see EvaluatorInterface::logicalNot() */
                $this->addEvaluatorMethodCall(
                    $node,
                    'logicalNot',
                    $this->getReference($node->getChild(0)),
                );
                break;

            case AstNodeType::CALCULATE_IS_EQUAL:
                /** @see EvaluatorInterface::isEqual() */
                $this->addEvaluatorMethodCall(
                    $node,
                    'isEqual',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1)),
                );
                break;

            case AstNodeType::CALCULATE_IS_GREATER:
                /** @see EvaluatorInterface::isGreater() */
                $this->addEvaluatorMethodCall(
                    $node,
                    'isGreater',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1)),
                );
                break;

            case AstNodeType::CALCULATE_IS_REGEXP:
                /** @see EvaluatorInterface::isRegExp() */
                $this->addEvaluatorMethodCall(
                    $node,
                    'isRegExp',
                    $this->php->val($node->getAttribute('pattern')),
                    $this->getReference($node->getChild(0)),
                );
                break;

            case AstNodeType::FETCH_CHILDREN:
                /** @see RuntimeInterface::fetchChildren() */
                $this->addRuntimeMethodCall(
                    $node,
                    'fetchChildren',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1)),
                );
                break;

            case AstNodeType::FETCH_CHILDREN_DEEP:
                /** @see RuntimeInterface::fetchChildrenDeep() */
                $this->addRuntimeMethodCall(
                    $node,
                    'fetchChildrenDeep',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1)),
                );
                break;

            case AstNodeType::MATCH_ANY_CHILD:
                /** @see RuntimeInterface::matchAnyChild() */
                $this->addRuntimeMethodCall(
                    $node,
                    'matchAnyChild',
                );
                break;

            case AstNodeType::MATCH_PROPERTY_STRICTLY:
                /** @see RuntimeInterface::matchPropertyStrictly() */
                $this->addRuntimeMethodCall(
                    $node,
                    'matchPropertyStrictly',
                    ...array_map(
                        [$this->php, 'val'],
                        $node->getAttribute('names')
                    ),
                );
                break;

            case AstNodeType::MATCH_ELEMENT_STRICTLY:
                /** @see RuntimeInterface::matchElementStrictly() */
                $this->addRuntimeMethodCall(
                    $node,
                    'matchElementStrictly',
                    ...array_map(
                        [$this->php, 'val'],
                        $node->getAttribute('indexes')
                    ),
                );
                break;

            case AstNodeType::MATCH_ELEMENT_SLICE:
                /** @see RuntimeInterface::matchElementSlice() */
                $this->addRuntimeMethodCall(
                    $node,
                    'matchElementSlice',
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
                /** @see EvaluatorInterface::aggregate() */
                $this->addEvaluatorMethodCall(
                    $node,
                    'aggregate',
                    $this->php->val($node->getAttribute('name')),
                    $this->getReference($node->getChild(0)),
                );
                break;

            case AstNodeType::CREATE_SCALAR:
                $attributes = $node->getAttributeList();
                $value = $attributes['value'] ?? null; // TODO: allow pass null in attribute
                /** @see RuntimeInterface::createScalar() */
                $this->addRuntimeMethodCall(
                    $node,
                    'createScalar',
                    $this->getReference($node->getChild(0)),
                    $this->php->val($value),
                );
                break;

            case AstNodeType::POPULATE_ARRAY_ELEMENTS:
                /** @see RuntimeInterface::populateArrayElements() */
                $this->addRuntimeMethodCall(
                    $node,
                    'populateArrayElements',
                    $this->getReference($node->getChild(0)),
                    new Arg(
                        $this->getReference($node->getChild(1)),
                        false,
                        true,
                    ),
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
                    $this->getReference($node->getChild(0)),
                );
                break;

            case AstNodeType::CREATE_LITERAL_ARRAY:
                /** @see RuntimeInterface::createArray() */
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
