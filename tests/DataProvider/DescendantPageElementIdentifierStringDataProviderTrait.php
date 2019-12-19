<?php

namespace webignition\BasilParser\Tests\DataProvider;

trait DescendantPageElementIdentifierStringDataProviderTrait
{
    public function descendantPageElementIdentifierStringDataProvider(): array
    {
        return [
            'interaction action arguments' => [
                'string' => '{{ $".parent" }} $".child"',
                'expectedIdentifierString' => '{{ $".parent" }} $".child"',
            ],
            'input action arguments' => [
                'string' => '{{ $".parent" }} $".child" to "value"',
                'expectedIdentifierString' => '{{ $".parent" }} $".child"',
            ],
        ];
    }
}
