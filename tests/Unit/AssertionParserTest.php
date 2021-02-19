<?php

declare(strict_types=1);

namespace webignition\BasilParser\Tests\Unit;

use PHPUnit\Framework\TestCase;
use webignition\BasilModels\Assertion\Assertion;
use webignition\BasilModels\Assertion\AssertionInterface;
use webignition\BasilParser\AssertionParser;
use webignition\BasilParser\Exception\UnparseableAssertionException;

class AssertionParserTest extends TestCase
{
    private AssertionParser $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = AssertionParser::create();
    }

    /**
     * @dataProvider parseDataProvider
     */
    public function testParse(string $assertionString, AssertionInterface $expectedAssertion): void
    {
        $parser = AssertionParser::create();

        $this->assertEquals($expectedAssertion, $parser->parse($assertionString));
    }

    /**
     * @return array[]
     */
    public function parseDataProvider(): array
    {
        return [
            'css element selector, is, scalar value' => [
                'assertionString' => '$".selector" is "value"',
                'expectedAssertion' => new Assertion(
                    '$".selector" is "value"',
                    '$".selector"',
                    'is',
                    '"value"'
                ),
            ],
            'css parent > child element selector, is, scalar value' => [
                'assertionString' => '$".parent" >> $".child" is "value"',
                'expectedAssertion' => new Assertion(
                    '$".parent" >> $".child" is "value"',
                    '$".parent" >> $".child"',
                    'is',
                    '"value"'
                ),
            ],
            'css grandparent > parent > child element selector, is, scalar value' => [
                'assertionString' => '$".grandparent" >> $".parent" >> $".child" is "value"',
                'expectedAssertion' => new Assertion(
                    '$".grandparent" >> $".parent" >> $".child" is "value"',
                    '$".grandparent" >> $".parent" >> $".child"',
                    'is',
                    '"value"'
                ),
            ],
            'css element selector containing whitespace, is, scalar value' => [
                'assertionString' => '$".parent .child" is "value"',
                'expectedAssertion' => new Assertion(
                    '$".parent .child" is "value"',
                    '$".parent .child"',
                    'is',
                    '"value"'
                ),
            ],
            'css element selector, is-not, scalar value' => [
                'assertionString' => '$".selector" is-not "value"',
                'expectedAssertion' => new Assertion(
                    '$".selector" is-not "value"',
                    '$".selector"',
                    'is-not',
                    '"value"'
                ),
            ],
            'css attribute selector, is, scalar value' => [
                'assertionString' => '$".selector".attribute_name is "value"',
                'expectedAssertion' => new Assertion(
                    '$".selector".attribute_name is "value"',
                    '$".selector".attribute_name',
                    'is',
                    '"value"'
                ),
            ],
            'scalar value, is, scalar value' => [
                'assertionString' => '"value" is "value"',
                'expectedAssertion' => new Assertion(
                    '"value" is "value"',
                    '"value"',
                    'is',
                    '"value"'
                ),
            ],
            'css element selector, is, dom identifier value' => [
                'assertionString' => '$".selector1" is $".selector2"',
                'expectedAssertion' => new Assertion(
                    '$".selector1" is $".selector2"',
                    '$".selector1"',
                    'is',
                    '$".selector2"'
                ),
            ],
            'css element selector, is, descendant dom identifier value' => [
                'assertionString' => '$".selector1" is $".parent" >> $".child"',
                'expectedAssertion' => new Assertion(
                    '$".selector1" is $".parent" >> $".child"',
                    '$".selector1"',
                    'is',
                    '$".parent" >> $".child"'
                ),
            ],
            'css element selector, is, nested descendant dom identifier value' => [
                'assertionString' => '$".selector1" is $".grandparent" >> $".parent" >> $".child"',
                'expectedAssertion' => new Assertion(
                    '$".selector1" is $".grandparent" >> $".parent" >> $".child"',
                    '$".selector1"',
                    'is',
                    '$".grandparent" >> $".parent" >> $".child"'
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
                'expectedAssertion' => new Assertion(
                    '$".selector" includes "value"',
                    '$".selector"',
                    'includes',
                    '"value"'
                ),
            ],
            'css selector, excludes, scalar value' => [
                'assertionString' => '$".selector" excludes "value"',
                'expectedAssertion' => new Assertion(
                    '$".selector" excludes "value"',
                    '$".selector"',
                    'excludes',
                    '"value"'
                ),
            ],
            'css selector, matches, scalar value' => [
                'assertionString' => '$".selector" matches "value"',
                'expectedAssertion' => new Assertion(
                    '$".selector" matches "value"',
                    '$".selector"',
                    'matches',
                    '"value"'
                ),
            ],
        ];
    }

    public function testParseEmptyAssertion(): void
    {
        $this->expectExceptionObject(UnparseableAssertionException::createEmptyAssertionException());

        $this->parser->parse('');
    }

    public function testParseEmptyIdentifier(): void
    {
        $source = 'foo';

        $this->expectExceptionObject(UnparseableAssertionException::createEmptyIdentifierException($source));

        $this->parser->parse($source);
    }

    public function testParseEmptyComparison(): void
    {
        $source = '$page.title';

        $this->expectExceptionObject(UnparseableAssertionException::createEmptyComparisonException($source));

        $this->parser->parse($source);
    }

    public function testParseEmptyValue(): void
    {
        $source = '$page.title is';

        $this->expectExceptionObject(UnparseableAssertionException::createEmptyValueException($source));

        $this->parser->parse($source);
    }

    public function testParseInvalidValueFormat(): void
    {
        $source = '$page.title is value';

        $this->expectExceptionObject(UnparseableAssertionException::createInvalidValueFormatException($source));

        $this->parser->parse($source);
    }
}
