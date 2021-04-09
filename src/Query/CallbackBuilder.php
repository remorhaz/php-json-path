<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Query;

use PhpParser\BuilderFactory;
use PhpParser\Node as PhpAstNode;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;
use PhpParser\PrettyPrinter\Standard;
use Remorhaz\JSON\Path\Runtime\EvaluatorInterface;
use Remorhaz\JSON\Path\Runtime\LiteralFactoryInterface;
use Remorhaz\JSON\Path\Runtime\Matcher\MatcherFactoryInterface;
use Remorhaz\JSON\Path\Runtime\ValueListFetcherInterface;
use Remorhaz\JSON\Path\Value\NodeValueListInterface;
use Remorhaz\JSON\Path\Value\ValueListInterface;
use Remorhaz\UniLex\AST\AbstractTranslatorListener;
use Remorhaz\UniLex\AST\Node as QueryAstNode;
use Remorhaz\UniLex\Exception as UniLexException;
use Remorhaz\UniLex\Stack\PushInterface;

use function array_map;
use function array_reverse;

final class CallbackBuilder extends AbstractTranslatorListener implements CallbackBuilderInterface
{

    private const ARG_INPUT = 'input';

    private const ARG_VALUE_LIST_FETCHER = 'valueListFetcher';

    private const ARG_EVALUATOR = 'evaluator';

    private const ARG_LITERAL_FACTORY = 'literalFactory';

    private const ARG_MATCHER_FACTORY = 'matcherFactory';

    private $php;

    private $references = [];

    private $input;

    private $valueListFetcher;

    private $evaluator;

    private $literalFactory;

    private $matcherFactory;

    private $stmts = [];

    private $callback;

    private $callbackCode;

    private $capabilities;

    public function __construct()
    {
        $this->php = new BuilderFactory();
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     */
    public function getCallback(): callable
    {
        if (!isset($this->callback)) {
            $this->callback = function (
                NodeValueListInterface $input,
                ValueListFetcherInterface $valueListFetcher,
                EvaluatorInterface $evaluator,
                LiteralFactoryInterface $literalFactory,
                MatcherFactoryInterface $matcherFactory
            ): ValueListInterface {
                return eval($this->getCallbackCode());
            };
        }

        return $this->callback;
    }

    public function getCallbackCode(): string
    {
        if (isset($this->callbackCode)) {
            return $this->callbackCode;
        }

        throw new Exception\QueryCallbackCodeNotFoundException();
    }

    public function getCapabilities(): CapabilitiesInterface
    {
        if (isset($this->capabilities)) {
            return $this->capabilities;
        }

        throw new Exception\CapabilitiesNotFoundException();
    }

    public function onStart(QueryAstNode $node): void
    {
        $this->input = $this->php->var(self::ARG_INPUT);
        $this->valueListFetcher = $this->php->var(self::ARG_VALUE_LIST_FETCHER);
        $this->evaluator = $this->php->var(self::ARG_EVALUATOR);
        $this->literalFactory = $this->php->var(self::ARG_LITERAL_FACTORY);
        $this->matcherFactory = $this->php->var(self::ARG_MATCHER_FACTORY);

        $this->stmts = [];
        $this->references = [];
        unset($this->callback, $this->callbackCode, $this->capabilities);
    }

    public function onFinish(): void
    {
        $stmts = array_map(
            function (PhpAstNode $stmt): PhpAstNode {
                return $stmt instanceof Expr ? new Expression($stmt) : $stmt;
            },
            $this->stmts
        );

        $this->callbackCode = (new Standard())->prettyPrint($stmts);
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
                    $node->getAttribute('is_addressable'),
                );
                $this->stmts[] = new Return_($this->getReference($node->getChild(0)));
                break;

            case AstNodeType::FETCH_CHILDREN:
                /** @see ValueListFetcherInterface::fetchChildren() */
                $this->addMethodCall(
                    $node,
                    $this->valueListFetcher,
                    'fetchChildren',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1)),
                );
                break;

            case AstNodeType::FETCH_CHILDREN_DEEP:
                /** @see ValueListFetcherInterface::fetchChildrenDeep() */
                $this->addMethodCall(
                    $node,
                    $this->valueListFetcher,
                    'fetchChildrenDeep',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1)),
                );
                break;

            case AstNodeType::MERGE:
                /** @see ValueListFetcherInterface::merge() */
                $this->addMethodCall(
                    $node,
                    $this->valueListFetcher,
                    'merge',
                    ...$this->getReferences(...$node->getChildList()),
                );
                break;

            case AstNodeType::FETCH_FILTER_CONTEXT:
                /** @see ValueListFetcherInterface::fetchFilterContext() */
                $this->addMethodCall(
                    $node,
                    $this->valueListFetcher,
                    'fetchFilterContext',
                    $this->getReference($node->getChild(0)),
                );
                break;

            case AstNodeType::SPLIT_FILTER_CONTEXT:
                /** @see ValueListFetcherInterface::splitFilterContext() */
                $this->addMethodCall(
                    $node,
                    $this->valueListFetcher,
                    'splitFilterContext',
                    $this->getReference($node->getChild(0)),
                );
                break;

            case AstNodeType::JOIN_FILTER_RESULTS:
                /** @see ValueListFetcherInterface::joinFilterResults() */
                $this->addMethodCall(
                    $node,
                    $this->valueListFetcher,
                    'joinFilterResults',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1)),
                );
                break;

            case AstNodeType::FILTER:
                /** @see ValueListFetcherInterface::fetchFilteredValues() */
                $this->addMethodCall(
                    $node,
                    $this->valueListFetcher,
                    'fetchFilteredValues',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1)),
                );
                break;

            case AstNodeType::EVALUATE:
                /** @see EvaluatorInterface::evaluate() */
                $this->addMethodCall(
                    $node,
                    $this->evaluator,
                    'evaluate',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1)),
                );
                break;

            case AstNodeType::EVALUATE_LOGICAL_OR:
                /** @see EvaluatorInterface::logicalOr() */
                $this->addMethodCall(
                    $node,
                    $this->evaluator,
                    'logicalOr',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1)),
                );
                break;

            case AstNodeType::EVALUATE_LOGICAL_AND:
                /** @see EvaluatorInterface::logicalAnd() */
                $this->addMethodCall(
                    $node,
                    $this->evaluator,
                    'logicalAnd',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1)),
                );
                break;

            case AstNodeType::EVALUATE_LOGICAL_NOT:
                /** @see EvaluatorInterface::logicalNot() */
                $this->addMethodCall(
                    $node,
                    $this->evaluator,
                    'logicalNot',
                    $this->getReference($node->getChild(0)),
                );
                break;

            case AstNodeType::CALCULATE_IS_EQUAL:
                /** @see EvaluatorInterface::isEqual() */
                $this->addMethodCall(
                    $node,
                    $this->evaluator,
                    'isEqual',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1)),
                );
                break;

            case AstNodeType::CALCULATE_IS_GREATER:
                /** @see EvaluatorInterface::isGreater() */
                $this->addMethodCall(
                    $node,
                    $this->evaluator,
                    'isGreater',
                    $this->getReference($node->getChild(0)),
                    $this->getReference($node->getChild(1)),
                );
                break;

            case AstNodeType::CALCULATE_IS_REGEXP:
                /** @see EvaluatorInterface::isRegExp() */
                $this->addMethodCall(
                    $node,
                    $this->evaluator,
                    'isRegExp',
                    $this->php->val($node->getAttribute('pattern')),
                    $this->getReference($node->getChild(0)),
                );
                break;

            case AstNodeType::MATCH_ANY_CHILD:
                /** @see MatcherFactoryInterface::matchAnyChild() */
                $this->addMethodCall(
                    $node,
                    $this->matcherFactory,
                    'matchAnyChild',
                );
                break;

            case AstNodeType::MATCH_PROPERTY_STRICTLY:
                /** @see MatcherFactoryInterface::matchPropertyStrictly() */
                $this->addMethodCall(
                    $node,
                    $this->matcherFactory,
                    'matchPropertyStrictly',
                    ...array_map(
                        [$this->php, 'val'],
                        $node->getAttribute('names')
                    ),
                );
                break;

            case AstNodeType::MATCH_ELEMENT_STRICTLY:
                /** @see MatcherFactoryInterface::matchElementStrictly() */
                $this->addMethodCall(
                    $node,
                    $this->matcherFactory,
                    'matchElementStrictly',
                    ...array_map(
                        [$this->php, 'val'],
                        $node->getAttribute('indexes')
                    ),
                );
                break;

            case AstNodeType::MATCH_ELEMENT_SLICE:
                /** @see MatcherFactoryInterface::matchElementSlice() */
                $this->addMethodCall(
                    $node,
                    $this->matcherFactory,
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
                $this->addMethodCall(
                    $node,
                    $this->evaluator,
                    'aggregate',
                    $this->php->val($node->getAttribute('name')),
                    $this->getReference($node->getChild(0)),
                );
                break;

            case AstNodeType::CREATE_LITERAL_SCALAR:
                $attributes = $node->getAttributeList();
                $value = $attributes['value'] ?? null; // TODO: allow pass null in attribute
                /** @see LiteralFactoryInterface::createScalar() */
                $this->addMethodCall(
                    $node,
                    $this->literalFactory,
                    'createScalar',
                    $this->getReference($node->getChild(0)),
                    $this->php->val($value),
                );
                break;

            case AstNodeType::CREATE_LITERAL_ARRAY:
                /** @see LiteralFactoryInterface::createArray() */
                $this->addMethodCall(
                    $node,
                    $this->literalFactory,
                    'createArray',
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

    /**
     * @param QueryAstNode ...$nodes
     * @return Expr[]
     */
    private function getReferences(QueryAstNode ...$nodes): array
    {
        return array_map([$this, 'getReference'], $nodes);
    }

    private function addMethodCall(QueryAstNode $node, Expr $object, string $method, PhpAstNode ...$args): void
    {
        $methodCall = $this
            ->php
            ->methodCall($object, $method, $args);
        $this->stmts[] = new Assign($this->createReference($node), $methodCall);
    }
}
