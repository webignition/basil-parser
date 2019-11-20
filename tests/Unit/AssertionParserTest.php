<?php

declare(strict_types=1);

namespace webignition\BasilParser\Tests\Unit;

use PHPUnit\Framework\TestCase;
use webignition\BasilDataStructure\Assertion;
use webignition\BasilDataStructure\AssertionInterface;
use webignition\BasilParser\AssertionParser;

class AssertionParserTest extends TestCase
{
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
            'empty' => [
                'assertionString' => '',
                'expectedAssertion' => new Assertion('', null, null),
            ],
            'css element selector, is, scalar value' => [
                'assertionString' => '".selector" is "value"',
                'expectedAssertion' => new Assertion(
                    '".selector" is "value"',
                    '".selector"',
                    'is',
                    '"value"'
                ),
            ],
            'css element selector, is-not, scalar value' => [
                'assertionString' => '".selector" is-not "value"',
                'expectedAssertion' => new Assertion(
                    '".selector" is-not "value"',
                    '".selector"',
                    'is-not',
                    '"value"'
                ),
            ],
            'css attribute selector, is, scalar value' => [
                'assertionString' => '".selector".attribute_name is "value"',
                'expectedAssertion' => new Assertion(
                    '".selector".attribute_name is "value"',
                    '".selector".attribute_name',
                    'is',
                    '"value"'
                ),
            ],
            'css element selector with element reference, is, scalar value' => [
                'assertionString' => '"{{ reference }} .selector" is "value"',
                'expectedAssertion' => new Assertion(
                    '"{{ reference }} .selector" is "value"',
                    '"{{ reference }} .selector"',
                    'is',
                    '"value"'
                ),
            ],
            'css element selector, exists, no value' => [
                'assertionString' => '".selector" exists',
                'expectedAssertion' => new Assertion(
                    '".selector" exists',
                    '".selector"',
                    'exists'
                ),
            ],
            'css element selector, not-exists, no value' => [
                'assertionString' => '".selector" not-exists',
                'expectedAssertion' => new Assertion(
                    '".selector" not-exists',
                    '".selector"',
                    'not-exists'
                ),
            ],
            'css element selector, exists, scalar value' => [
                'assertionString' => '".selector" exists "value"',
                'expectedAssertion' => new Assertion(
                    '".selector" exists "value"',
                    '".selector"',
                    'exists',
                    '"value"'
                ),
            ],
            'css selector, includes, scalar value' => [
                'assertionString' => '".selector" includes "value"',
                'expectedAssertion' => new Assertion(
                    '".selector" includes "value"',
                    '".selector"',
                    'includes',
                    '"value"'
                ),
            ],
            'css selector, excludes, scalar value' => [
                'assertionString' => '".selector" excludes "value"',
                'expectedAssertion' => new Assertion(
                    '".selector" excludes "value"',
                    '".selector"',
                    'excludes',
                    '"value"'
                ),
            ],
            'css selector, matches, scalar value' => [
                'assertionString' => '".selector" matches "value"',
                'expectedAssertion' => new Assertion(
                    '".selector" matches "value"',
                    '".selector"',
                    'matches',
                    '"value"'
                ),
            ],
        ];
    }
}
