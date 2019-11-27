<?php

declare(strict_types=1);

namespace webignition\BasilParser\Tests\Unit;

use PHPUnit\Framework\TestCase;
use webignition\BasilDataStructure\Action\Action;
use webignition\BasilDataStructure\Action\ActionInterface;
use webignition\BasilDataStructure\Action\InputAction;
use webignition\BasilDataStructure\Action\InteractionAction;
use webignition\BasilDataStructure\Action\WaitAction;
use webignition\BasilParser\ActionParser;

class ActionParserTest extends TestCase
{
    /**
     * @dataProvider parseDataProvider
     */
    public function testParse(string $actionString, ActionInterface $expectedAction)
    {
        $parser = ActionParser::create();

        $this->assertEquals($expectedAction, $parser->parse($actionString));
    }

    public function parseDataProvider(): array
    {
        return [
            'empty' => [
                'actionString' => '',
                'expectedAction' => new Action('', null),
            ],
            'unknown type' => [
                'actionString' => 'foo $".selector"',
                'expectedAction' => new Action('foo $".selector"', 'foo'),
            ],
            'click' => [
                'actionString' => 'click $".selector"',
                'expectedAction' => new InteractionAction(
                    'click $".selector"',
                    'click',
                    '$".selector"',
                    '$".selector"'
                ),
            ],
            'submit' => [
                'actionString' => 'submit $".selector"',
                'expectedAction' => new InteractionAction(
                    'submit $".selector"',
                    'submit',
                    '$".selector"',
                    '$".selector"'
                ),
            ],
            'wait' => [
                'actionString' => 'wait 1',
                'expectedAction' => new WaitAction('wait 1', '1'),
            ],
            'wait-for' => [
                'actionString' => 'wait-for $".selector"',
                'expectedAction' => new InteractionAction(
                    'wait-for $".selector"',
                    'wait-for',
                    '$".selector"',
                    '$".selector"'
                ),
            ],
            'reload' => [
                'actionString' => 'reload',
                'expectedAction' => new Action('reload', 'reload', ''),
            ],
            'back' => [
                'actionString' => 'back',
                'expectedAction' => new Action('back', 'back', ''),
            ],
            'forward' => [
                'actionString' => 'forward',
                'expectedAction' => new Action('forward', 'forward', ''),
            ],
            'set to literal value' => [
                'actionString' => 'set $".selector" to "value"',
                'expectedAction' => new InputAction(
                    'set $".selector" to "value"',
                    '$".selector" to "value"',
                    '$".selector"',
                    '"value"'
                ),
            ],
            'set to variable value, data parameter' => [
                'actionString' => 'set $".selector" to $data.value',
                'expectedAction' => new InputAction(
                    'set $".selector" to $data.value',
                    '$".selector" to $data.value',
                    '$".selector"',
                    '$data.value'
                ),
            ],
            'set to variable value, dom identifier value' => [
                'actionString' => 'set $".selector1" to $".selector2"',
                'expectedAction' => new InputAction(
                    'set $".selector1" to $".selector2"',
                    '$".selector1" to $".selector2"',
                    '$".selector1"',
                    '$".selector2"'
                ),
            ],
            'set with "to" keyword lacking value' => [
                'actionString' => 'set $".selector" to',
                'expectedAction' => new InputAction(
                    'set $".selector" to',
                    '$".selector" to',
                    '$".selector"',
                    null
                ),
            ],
            'set lacking "to" keyword, lacking value' => [
                'actionString' => 'set $".selector"',
                'expectedAction' => new InputAction(
                    'set $".selector"',
                    '$".selector"',
                    '$".selector"',
                    null
                ),
            ],
        ];
    }
}
