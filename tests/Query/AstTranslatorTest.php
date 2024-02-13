<?php

declare(strict_types=1);

namespace Remorhaz\JSON\Path\Test\Query;

use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Remorhaz\JSON\Data\Value\NodeValueInterface;
use Remorhaz\JSON\Path\Query\Exception\ExceptionInterface;
use Remorhaz\JSON\Path\Query\Exception\QueryAstNotTranslatedException;
use Remorhaz\JSON\Path\Query\Query;
use Remorhaz\JSON\Path\Query\AstTranslator;
use Remorhaz\JSON\Path\Query\CallbackBuilderInterface;
use Remorhaz\JSON\Path\Query\CapabilitiesInterface;
use Remorhaz\JSON\Path\Runtime\EvaluatorInterface;
use Remorhaz\JSON\Path\Runtime\LiteralFactoryInterface;
use Remorhaz\JSON\Path\Runtime\Matcher\MatcherFactoryInterface;
use Remorhaz\JSON\Path\Runtime\RuntimeInterface;
use Remorhaz\JSON\Path\Runtime\ValueListFetcherInterface;
use Remorhaz\JSON\Path\Value\NodeValueListInterface;
use Remorhaz\JSON\Path\Value\ValueListInterface;
use Remorhaz\UniLex\AST\Tree;
use Remorhaz\UniLex\Exception as UniLexException;

#[CoversClass(AstTranslator::class)]
class AstTranslatorTest extends TestCase
{
    /**
     * @throws UniLexException
     */
    public function testBuildQuery_Constructed_ReturnsQueryInstance(): void
    {
        $translator = new AstTranslator();
        $tree = new Tree();
        $tree->setRootNode($tree->createNode('a'));
        $callbackBuilder = $this->createStub(CallbackBuilderInterface::class);
        self::assertInstanceOf(
            Query::class,
            $translator->buildQuery('return;', $tree, $callbackBuilder),
        );
    }

    /**
     * @throws UniLexException
     */
    public function testBuildQuery_ThrowsExceptionOnTranslation_ThrowsException(): void
    {
        $translator = new AstTranslator();
        $tree = new Tree();
        $tree->setRootNode($tree->createNode('a'));

        $callbackBuilder = $this->createStub(CallbackBuilderInterface::class);
        $callbackBuilder
            ->method('onStart')
            ->willThrowException(new Exception());
        $this->expectException(QueryAstNotTranslatedException::class);
        $translator->buildQuery('b', $tree, $callbackBuilder);
    }

    /**
     * @throws UniLexException
     */
    public function testBuildQuery_Constructed_StartsTreeTranslation(): void
    {
        $translator = new AstTranslator();
        $tree = new Tree();
        $rootNode = $tree->createNode('a');
        $tree->setRootNode($rootNode);

        $callbackBuilder = $this->createMock(CallbackBuilderInterface::class);
        $callbackBuilder
            ->expects(self::once())
            ->method('onStart')
            ->with($rootNode);
        $translator->buildQuery('b', $tree, $callbackBuilder);
    }

    /**
     * @throws UniLexException
     */
    public function testBuildQuery_Constructed_FinishesTreeTranslation(): void
    {
        $translator = new AstTranslator();
        $tree = new Tree();
        $tree->setRootNode($tree->createNode('a'));

        $callbackBuilder = $this->createMock(CallbackBuilderInterface::class);
        $callbackBuilder
            ->expects(self::once())
            ->method('onFinish')
            ->with();
        $translator->buildQuery('b', $tree, $callbackBuilder);
    }

    /**
     * @throws UniLexException
     * @throws ExceptionInterface
     */
    public function testBuildQuery_CallbackBuilderProvidesCallback_OnInvocationResultInvokesSameCallback(): void
    {
        $translator = new AstTranslator();
        $tree = new Tree();
        $tree->setRootNode($tree->createNode('a'));

        $rootValue = $this->createStub(NodeValueInterface::class);
        $runtime = $this->createStub(RuntimeInterface::class);
        $valueListFetcher = $this->createStub(ValueListFetcherInterface::class);
        $evaluator = $this->createStub(EvaluatorInterface::class);
        $literalFactory = $this->createStub(LiteralFactoryInterface::class);
        $matcherFactory = $this->createStub(MatcherFactoryInterface::class);
        $runtime
            ->method('getValueListFetcher')
            ->willReturn($valueListFetcher);
        $runtime
            ->method('getEvaluator')
            ->willReturn($evaluator);
        $runtime
            ->method('getLiteralFactory')
            ->willReturn($literalFactory);
        $runtime
            ->method('getMatcherFactory')
            ->willReturn($matcherFactory);

        $isCallbackCalledWithMatchingArgs = null;
        $callback = function () use (
            $rootValue,
            $valueListFetcher,
            $evaluator,
            $literalFactory,
            $matcherFactory,
            &$isCallbackCalledWithMatchingArgs,
        ): ValueListInterface {
            $args = func_get_args();
            /** @var NodeValueListInterface $input */
            $input = array_shift($args);
            $isCallbackCalledWithMatchingArgs =
                $input->getValues() === [$rootValue] &&
                [$valueListFetcher, $evaluator, $literalFactory, $matcherFactory] === $args;

            return $this->createStub(ValueListInterface::class);
        };
        $callbackBuilder = $this->createStub(CallbackBuilderInterface::class);
        $callbackBuilder
            ->method('getCallback')
            ->willReturn($callback);
        $query = $translator->buildQuery('b', $tree, $callbackBuilder);

        $query($rootValue, $runtime);
        self::assertTrue($isCallbackCalledWithMatchingArgs);
    }

    /**
     * @throws UniLexException
     */
    public function testBuildQuery_CallbackBuilderProvidesGivenQueryProperties_ResultHasSamePropertiesInstance(): void
    {
        $properties = $this->createStub(CapabilitiesInterface::class);
        $translator = new AstTranslator();
        $tree = new Tree();
        $tree->setRootNode($tree->createNode('a'));

        $callbackBuilder = $this->createStub(CallbackBuilderInterface::class);
        $callbackBuilder
            ->method('getCapabilities')
            ->willReturn($properties);

        $query = $translator->buildQuery('b', $tree, $callbackBuilder);
        self::assertSame($properties, $query->getCapabilities());
    }
}
