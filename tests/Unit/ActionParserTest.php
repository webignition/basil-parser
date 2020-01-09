<?php

declare(strict_types=1);

namespace webignition\BasilParser\Tests\Unit;

use PHPUnit\Framework\TestCase;
use webignition\BasilModels\Action\Action;
use webignition\BasilModels\Action\ActionInterface;
use webignition\BasilModels\Action\InputAction;
use webignition\BasilModels\Action\InteractionAction;
use webignition\BasilModels\Action\WaitAction;
use webignition\BasilParser\ActionParser;
use webignition\BasilParser\Exception\UnparseableActionException;

class ActionParserTest extends TestCase
{
    /**
     * @var ActionParser
     */
    private $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = ActionParser::create();
    }

    /**
     * @dataProvider parseDataProvider
     */
    public function testParse(string $actionString, ActionInterface $expectedAction)
    {
        $this->assertEquals($expectedAction, $this->parser->parse($actionString));
    }

    public function parseDataProvider(): array
    {
        return [
            'unknown type' => [
                'actionString' => 'foo $".selector"',
                'expectedAction' => new Action('foo $".selector"', 'foo', '$".selector"'),
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
            'click: parent > child' => [
                'actionString' => 'click $"{{ $".parent" }} .child"',
                'expectedAction' => new InteractionAction(
                    'click $"{{ $".parent" }} .child"',
                    'click',
                    '$"{{ $".parent" }} .child"',
                    '$"{{ $".parent" }} .child"'
                ),
            ],
            'click: grandparent > parent > child' => [
                'actionString' => 'click $"{{ $"{{ $".grandparent" }} .parent" }} .child"',
                'expectedAction' => new InteractionAction(
                    'click $"{{ $"{{ $".grandparent" }} .parent" }} .child"',
                    'click',
                    '$"{{ $"{{ $".grandparent" }} .parent" }} .child"',
                    '$"{{ $"{{ $".grandparent" }} .parent" }} .child"'
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
            'set to literal value, non-empty' => [
                'actionString' => 'set $".selector" to "value"',
                'expectedAction' => new InputAction(
                    'set $".selector" to "value"',
                    '$".selector" to "value"',
                    '$".selector"',
                    '"value"'
                ),
            ],
            'set to literal value, empty' => [
                'actionString' => 'set $".selector" to ""',
                'expectedAction' => new InputAction(
                    'set $".selector" to ""',
                    '$".selector" to ""',
                    '$".selector"',
                    '""'
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
            'set to variable value, dom identifier value (1)' => [
                'actionString' => 'set $".selector1" to $".selector2"',
                'expectedAction' => new InputAction(
                    'set $".selector1" to $".selector2"',
                    '$".selector1" to $".selector2"',
                    '$".selector1"',
                    '$".selector2"'
                ),
            ],
            'set to variable value, dom identifier value (2)' => [
                'actionString' => 'set $".selector1":1 to $".selector2":1',
                'expectedAction' => new InputAction(
                    'set $".selector1":1 to $".selector2":1',
                    '$".selector1":1 to $".selector2":1',
                    '$".selector1":1',
                    '$".selector2":1'
                ),
            ],
            'set to variable value, dom identifier value (3)' => [
                'actionString' => 'set $".parent1 .child1" to $".parent2 .child2"',
                'expectedAction' => new InputAction(
                    'set $".parent1 .child1" to $".parent2 .child2"',
                    '$".parent1 .child1" to $".parent2 .child2"',
                    '$".parent1 .child1"',
                    '$".parent2 .child2"'
                ),
            ],
            'set to variable value, dom identifier value (4)' => [
                'actionString' => 'set $"{{ $".parent1" }} .child1" to $"{{ $".parent2" }} .child2"',
                'expectedAction' => new InputAction(
                    'set $"{{ $".parent1" }} .child1" to $"{{ $".parent2" }} .child2"',
                    '$"{{ $".parent1" }} .child1" to $"{{ $".parent2" }} .child2"',
                    '$"{{ $".parent1" }} .child1"',
                    '$"{{ $".parent2" }} .child2"'
                ),
            ],
        ];
    }

    public function testParseEmptyAction()
    {
        $this->expectExceptionObject(UnparseableActionException::createEmptyActionException());

        $this->parser->parse('');
    }

    /**
     * @dataProvider parseInputActionEmptyValueDataProvider
     */
    public function testParseInputActionEmptyValue(string $action, UnparseableActionException $expectedException)
    {
        $this->expectExceptionObject($expectedException);

        $this->parser->parse($action);
    }

    public function parseInputActionEmptyValueDataProvider(): array
    {
        return [
            'set with "to" keyword lacking value' => [
                'actionString' => 'set $".selector" to',
                'expectedException' => UnparseableActionException::createEmptyInputActionValueException(
                    'set $".selector" to'
                ),
            ],
            'set lacking "to" keyword, lacking value' => [
                'actionString' => 'set $".selector"',
                'expectedException' => UnparseableActionException::createEmptyInputActionValueException(
                    'set $".selector"'
                ),
            ],
        ];
    }

    /**
     * @dataProvider parseActionWithInvalidIdentifierDataProvider
     */
    public function testParseActionWithInvalidIdentifier(string $action, \Exception $expectedException)
    {
        $this->expectExceptionObject($expectedException);

        $this->parser->parse($action);
    }

    public function parseActionWithInvalidIdentifierDataProvider(): array
    {
        return [
            'click action with non-dollar-prefixed selector' => [
                'action' => 'click "selector"',
                'expectedException' => UnparseableActionException::createInvalidIdentifierException('click "selector"'),
            ],
            'set action with non-dollar-prefixed selector' => [
                'action' => 'set "selector" to "value"',
                'expectedException' => UnparseableActionException::createInvalidIdentifierException(
                    'set "selector" to "value"'
                ),
            ],
        ];
    }
}
