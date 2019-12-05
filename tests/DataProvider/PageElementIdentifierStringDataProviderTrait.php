<?php

namespace webignition\BasilParser\Tests\DataProvider;

trait PageElementIdentifierStringDataProviderTrait
{
    public function pageElementIdentifierStringDataProvider(): array
    {
        return [
            'page element identifier: whole-word quoted identifier' => [
                'string' => '$".selector" is "value"',
                'expectedIdentifierString' => '$".selector"',
            ],
            'page element identifier: whole-word quoted identifier with positive integer position' => [
                'string' => '$".selector":1 is "value"',
                'expectedIdentifierString' => '$".selector":1',
            ],
            'page element identifier: whole-word quoted identifier with negative integer position' => [
                'string' => '$".selector":-1 is "value"',
                'expectedIdentifierString' => '$".selector":-1',
            ],
            'page element identifier: whole-word quoted identifier with first position' => [
                'string' => '$".selector":first is "value"',
                'expectedIdentifierString' => '$".selector":first',
            ],
            'page element identifier: whole-word quoted identifier with last position' => [
                'string' => '$".selector":last is "value"',
                'expectedIdentifierString' => '$".selector":last',
            ],
            'page element identifier: whole-word quoted identifier with attribute name' => [
                'string' => '$".selector".attribute_name is "value"',
                'expectedIdentifierString' => '$".selector".attribute_name',
            ],
            'page element identifier: whole-word quoted identifier with positive integer position, attribute name' => [
                'string' => '$".selector":1.attribute_name is "value"',
                'expectedIdentifierString' => '$".selector":1.attribute_name',
            ],
            'page element identifier: whole-word quoted identifier with negative integer position, attribute name' => [
                'string' => '$".selector":-1.attribute_name is "value"',
                'expectedIdentifierString' => '$".selector":-1.attribute_name',
            ],
            'page element identifier: whole-word quoted identifier with first position, attribute name' => [
                'string' => '$".selector":first.attribute_name is "value"',
                'expectedIdentifierString' => '$".selector":first.attribute_name',
            ],
            'page element identifier: whole-word quoted identifier with last position, attribute name' => [
                'string' => '$".selector":last.attribute_name is "value"',
                'expectedIdentifierString' => '$".selector":last.attribute_name',
            ],
            'page element identifier: quoted identifier ending with comparison' => [
                'string' => '$".selector is" is "value"',
                'expectedIdentifierString' => '$".selector is"',
            ],
            'page element identifier: quoted identifier containing comparison and value' => [
                'string' => '$".selector is value" is "value"',
                'expectedIdentifierString' => '$".selector is value"',
            ],
            'page element identifier: whole-word quoted identifier with encapsulating escaped quotes' => [
                'string' => '$"\".selector\"" is "value"',
                'expectedIdentifierString' => '$"\".selector\""',
            ],
            'page element identifier: quoted quoted identifier containing escaped quotes' => [
                'string' => '$".selector \".is\"" is "value"',
                'expectedIdentifierString' => '$".selector \".is\""',
            ],
            'page element identifier: set action arguments: whole-word selector' => [
                'string' => '$".selector" to "value"',
                'expectedIdentifierString' => '$".selector"',
            ],
            'page element identifier: set action arguments: whole-word selector ending with stop word' => [
                'string' => '$".selector to " to "value"',
                'expectedIdentifierString' => '$".selector to "',
            ],
            'page element identifier: set action arguments: whole-word containing with stop word' => [
                'string' => '$".selector to value" to "value"',
                'expectedIdentifierString' => '$".selector to value"',
            ],
            'page element identifier: set action arguments: no value following stop word' => [
                'string' => '$".selector" to',
                'expectedIdentifierString' => '$".selector"',
            ],
            'page element identifier: no value following "is" keyword' => [
                'string' => '$".selector" is',
                'expectedIdentifierString' => '$".selector"',
            ],
            'page element identifier: no value following "is-not" keyword' => [
                'string' => '$".selector" is-not',
                'expectedIdentifierString' => '$".selector"',
            ],
            'page element identifier: no value following "includes" keyword' => [
                'string' => '$".selector" includes',
                'expectedIdentifierString' => '$".selector"',
            ],
            'page element identifier: no value following "excludes" keyword' => [
                'string' => '$".selector" excludes',
                'expectedIdentifierString' => '$".selector"',
            ],
            'page element identifier: no value following "matches" keyword' => [
                'string' => '$".selector" matches',
                'expectedIdentifierString' => '$".selector"',
            ],
            'page element identifier: whole-word quoted identifier only' => [
                'string' => '$".selector"',
                'expectedIdentifierString' => '$".selector"',
            ],
            'page element identifier: whole-word quoted identifier with position' => [
                'string' => '$".selector":3',
                'expectedIdentifierString' => '$".selector":3',
            ],
        ];
    }
}
