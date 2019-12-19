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
            'interaction action arguments with indirect parent' => [
                'string' => '{{ {{ $".inner-parent" }} $".inner-child" }} $".child"',
                'expectedIdentifierString' => '{{ {{ $".inner-parent" }} $".inner-child" }} $".child"',
            ],
            'interaction action arguments with indirectly indirect parent' => [
                'string' => '{{ {{ {{ $".inner-inner-parent" }} $".inner-inner-child" }} $".inner-child" }} $".child"',
                'expectedIdentifierString' =>
                    '{{ {{ {{ $".inner-inner-parent" }} $".inner-inner-child" }} $".inner-child" }} $".child"',
            ],
            'input action arguments' => [
                'string' => '{{ $".parent" }} $".child" to "value"',
                'expectedIdentifierString' => '{{ $".parent" }} $".child"',
            ],
            'input action arguments with indirect parent' => [
                'string' => '{{ {{ $".inner-parent" }} $".inner-child" }} $".child" to "value"',
                'expectedIdentifierString' => '{{ {{ $".inner-parent" }} $".inner-child" }} $".child"',
            ],
        ];
    }
}
