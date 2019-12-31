<?php

namespace webignition\BasilParser\Tests\DataProvider;

trait DescendantPageElementIdentifierStringDataProviderTrait
{
    public function descendantPageElementIdentifierStringDataProvider(): array
    {
        return [
            'interaction action arguments: parent > child' => [
                'string' => '$"{{ $".parent" }} .child"',
                'expectedIdentifierString' => '$"{{ $".parent" }} .child"',
            ],
            'interaction action arguments: grandparent > parent > child' => [
                'string' => '$"{{ $"{{ $".grandparent" }} .parent" }} .child"',
                'expectedIdentifierString' => '$"{{ $"{{ $".grandparent" }} .parent" }} .child"',
            ],
            'interaction action arguments: great-grandparent > grandparent > parent > child' => [
                'string' => '$"{{ $"{{ $"{{ $".great-grandparent" }} .grandparent }} .parent" }} .child"',
                'expectedIdentifierString' =>
                    '$"{{ $"{{ $"{{ $".great-grandparent" }} .grandparent }} .parent" }} .child"',
            ],
            'input action arguments: parent > child' => [
                'string' => '$"{{ $".parent" }} .child" to "value"',
                'expectedIdentifierString' => '$"{{ $".parent" }} .child"',
            ],
            'input action arguments: grandparent > parent > child' => [
                'string' => '$"{{ $"{{ $".grandparent" }} .parent" }} .child" to "value"',
                'expectedIdentifierString' => '$"{{ $"{{ $".grandparent" }} .parent" }} .child"',
            ],
            'input action arguments: great-grandparent > grandparent > parent > child' => [
                'string' => '$"{{ $"{{ $"{{ $".great-grandparent" }} .grandparent }} .parent" }} .child" to "value"',
                'expectedIdentifierString' =>
                    '$"{{ $"{{ $"{{ $".great-grandparent" }} .grandparent }} .parent" }} .child"',
            ],
        ];
    }
}
