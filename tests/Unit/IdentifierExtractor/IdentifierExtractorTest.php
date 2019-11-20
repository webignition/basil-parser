<?php

declare(strict_types=1);

namespace webignition\BasilParser\Tests\Unit\IdentifierStringExtractor;

use webignition\BasilParser\IdentifierExtractor\IdentifierExtractor;
use webignition\BasilParser\Tests\DataProvider\LiteralParameterStringDataProviderTrait;
use webignition\BasilParser\Tests\DataProvider\PageElementIdentifierStringDataProviderTrait;
use webignition\BasilParser\Tests\DataProvider\VariableParameterIdentifierStringDataProviderTrait;

class IdentifierExtractorTest extends \PHPUnit\Framework\TestCase
{
    use LiteralParameterStringDataProviderTrait;
    use PageElementIdentifierStringDataProviderTrait;
    use VariableParameterIdentifierStringDataProviderTrait;

    /**
     * @var IdentifierExtractor
     */
    private $identifierStringExtractor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->identifierStringExtractor = new IdentifierExtractor();
    }

    /**
     * @dataProvider extractDataProvider
     * @dataProvider literalParameterStringDataProvider
     * @dataProvider pageElementIdentifierStringDataProvider
     * @dataProvider variableParameterIdentifierStringDataProvider
     */
    public function testExtractFromStart(string $string, ?string $expectedIdentifierString)
    {
        $identifierString = $this->identifierStringExtractor->extract($string);

        $this->assertSame($expectedIdentifierString, $identifierString);
    }

    public function extractDataProvider(): array
    {
        return [
            'empty' => [
                'string' => '',
                'expectedIdentifierString' => null,
            ],
        ];
    }
}
