<?php

declare(strict_types=1);

namespace webignition\BasilParser\Tests\Unit;

use PHPUnit\Framework\TestCase;
use webignition\BasilModels\Assertion\Assertion;
use webignition\BasilModels\Assertion\AssertionInterface;
use webignition\BasilModels\Assertion\ComparisonAssertion;
use webignition\BasilParser\AssertionParser;
use webignition\BasilParser\Exception\UnparseableAssertionException;

class AssertionParserTest extends TestCase
{
    /**
     * @var AssertionParser
     */
    private $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = AssertionParser::create();
    }

    /**
     * @dataProvider parseDataProvider
     */
    public function testParse(string $assertionString, AssertionInterface $expectedAssertion)
    {
        $parser = AssertionParser::create();

        $this->assertEquals($expectedAssertion, $parser->parse($assertionString));
    }

    public function parseDataProvider(): array
    {
        return [
            'css element selector, is, scalar value' => [
                'assertionString' => '$".selector" is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '$".selector" is "value"',
                    '$".selector"',
                    'is',
                    '"value"'
                ),
            ],
            'css parent > child element selector, is, scalar value' => [
                'assertionString' => '$"{{ $".parent" }} .child" is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '$"{{ $".parent" }} .child" is "value"',
                    '$"{{ $".parent" }} .child"',
                    'is',
                    '"value"'
                ),
            ],
            'css grandparent > parent > child element selector, is, scalar value' => [
                'assertionString' => '$"{{ $"{{ $".grandparent" }} .parent" }} .child" is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '$"{{ $"{{ $".grandparent" }} .parent" }} .child" is "value"',
                    '$"{{ $"{{ $".grandparent" }} .parent" }} .child"',
                    'is',
                    '"value"'
                ),
            ],
            'css element selector containing whitespace, is, scalar value' => [
                'assertionString' => '$".parent .child" is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '$".parent .child" is "value"',
                    '$".parent .child"',
                    'is',
                    '"value"'
                ),
            ],
            'css element selector, is-not, scalar value' => [
                'assertionString' => '$".selector" is-not "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '$".selector" is-not "value"',
                    '$".selector"',
                    'is-not',
                    '"value"'
                ),
            ],
            'css attribute selector, is, scalar value' => [
                'assertionString' => '$".selector".attribute_name is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '$".selector".attribute_name is "value"',
                    '$".selector".attribute_name',
                    'is',
                    '"value"'
                ),
            ],
            'css element selector with element reference, is, scalar value' => [
                'assertionString' => '$"{{ reference }} .selector" is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '$"{{ reference }} .selector" is "value"',
                    '$"{{ reference }} .selector"',
                    'is',
                    '"value"'
                ),
            ],
            'scalar value, is, scalar value' => [
                'assertionString' => '"value" is "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '"value" is "value"',
                    '"value"',
                    'is',
                    '"value"'
                ),
            ],
            'css element selector, is, dom identifier value' => [
                'assertionString' => '$".selector1" is $".selector2"',
                'expectedAssertion' => new ComparisonAssertion(
                    '$".selector1" is $".selector2"',
                    '$".selector1"',
                    'is',
                    '$".selector2"'
                ),
            ],
            'css element selector, is, descendant dom identifier value' => [
                'assertionString' => '$".selector1" is $"{{ $".parent" }} .child"',
                'expectedAssertion' => new ComparisonAssertion(
                    '$".selector1" is $"{{ $".parent" }} .child"',
                    '$".selector1"',
                    'is',
                    '$"{{ $".parent" }} .child"'
                ),
            ],
            'css element selector, is, nested descendant dom identifier value' => [
                'assertionString' => '$".selector1" is $"{{ $"{{ $".grandparent" }} .parent" }} .child"',
                'expectedAssertion' => new ComparisonAssertion(
                    '$".selector1" is $"{{ $"{{ $".grandparent" }} .parent" }} .child"',
                    '$".selector1"',
                    'is',
                    '$"{{ $"{{ $".grandparent" }} .parent" }} .child"'
                ),
            ],
            'css element selector, exists, no value' => [
                'assertionString' => '$".selector" exists',
                'expectedAssertion' => new Assertion(
                    '$".selector" exists',
                    '$".selector"',
                    'exists'
                ),
            ],
            'css element selector, not-exists, no value' => [
                'assertionString' => '$".selector" not-exists',
                'expectedAssertion' => new Assertion(
                    '$".selector" not-exists',
                    '$".selector"',
                    'not-exists'
                ),
            ],
            'css element selector, exists, scalar value' => [
                'assertionString' => '$".selector" exists "value"',
                'expectedAssertion' => new Assertion(
                    '$".selector" exists "value"',
                    '$".selector"',
                    'exists'
                ),
            ],
            'css selector, includes, scalar value' => [
                'assertionString' => '$".selector" includes "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '$".selector" includes "value"',
                    '$".selector"',
                    'includes',
                    '"value"'
                ),
            ],
            'css selector, excludes, scalar value' => [
                'assertionString' => '$".selector" excludes "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '$".selector" excludes "value"',
                    '$".selector"',
                    'excludes',
                    '"value"'
                ),
            ],
            'css selector, matches, scalar value' => [
                'assertionString' => '$".selector" matches "value"',
                'expectedAssertion' => new ComparisonAssertion(
                    '$".selector" matches "value"',
                    '$".selector"',
                    'matches',
                    '"value"'
                ),
            ],
        ];
    }

    public function testParseEmptyAssertion()
    {
        $this->expectExceptionObject(UnparseableAssertionException::createEmptyAssertionException());

        $this->parser->parse('');
    }

    public function testParseEmptyIdentifier()
    {
        $source = 'foo';

        $this->expectExceptionObject(UnparseableAssertionException::createEmptyIdentifierException($source));

        $this->parser->parse($source);
    }

    public function testParseEmptyComparison()
    {
        $source = '$page.title';

        $this->expectExceptionObject(UnparseableAssertionException::createEmptyComparisonException($source));

        $this->parser->parse($source);
    }

    public function testParseEmptyValue()
    {
        $source = '$page.title is';

        $this->expectExceptionObject(UnparseableAssertionException::createEmptyValueException($source));

        $this->parser->parse($source);
    }
}
