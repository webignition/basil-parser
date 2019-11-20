<?php

declare(strict_types=1);

namespace webignition\BasilParser;

use webignition\BasilDataStructure\Action\Action;
use webignition\BasilDataStructure\Action\ActionInterface;
use webignition\BasilDataStructure\Action\InputAction;
use webignition\BasilDataStructure\Action\InteractionAction;
use webignition\BasilDataStructure\Action\WaitAction;
use webignition\BasilParser\ValueExtractor\LiteralValueExtractor;
use webignition\BasilParser\ValueExtractor\PageElementIdentifierExtractor;
use webignition\BasilParser\ValueExtractor\QuotedValueExtractor;

class ActionParser
{
    private const TYPES = [
        'back',
        'click',
        'forward',
        'reload',
        'set',
        'submit',
        'wait-for',
        'wait',
    ];

    private const INPUT_TYPES = [
        'set',
    ];

    private const INTERACTION_TYPES = [
        'click',
        'submit',
        'wait-for',
    ];

    private const WAIT_TYPES = [
        'wait',
    ];

    private $quotedValueExtractor;
    private $literalParameterExtractor;
    private $pageElementIdentifierExtractor;
    private $variableParameterExtractor;

    public function __construct(
        QuotedValueExtractor $quotedValueExtractor,
        LiteralValueExtractor $literalParameterExtractor,
        PageElementIdentifierExtractor $pageElementIdentifierExtractor,
        VariableParameterExtractor $variableParameterExtractor
    ) {
        $this->quotedValueExtractor = $quotedValueExtractor;
        $this->literalParameterExtractor = $literalParameterExtractor;
        $this->pageElementIdentifierExtractor = $pageElementIdentifierExtractor;
        $this->variableParameterExtractor = $variableParameterExtractor;
    }

    public static function create(): ActionParser
    {
        return new ActionParser(
            new QuotedValueExtractor(),
            new LiteralValueExtractor(),
            new PageElementIdentifierExtractor(),
            new VariableParameterExtractor()
        );
    }

    public function parse(string $source): ActionInterface
    {
        $source = trim($source);

        if ('' === $source) {
            return new Action('', null);
        }

        $type = $this->findType($source);
        if (null === $type) {
            return new Action('', null);
        }

        $arguments = trim(mb_substr($source, strlen($type)));

        $isWaitType = in_array($type, self::WAIT_TYPES);
        if ($isWaitType) {
            return new WaitAction($source, $arguments);
        }

        $isInteractionType = in_array($type, self::INTERACTION_TYPES);
        $isInputType = in_array($type, self::INPUT_TYPES);

        if ($isInteractionType || $isInputType) {
            $identifier = $this->findIdentifier($arguments);

            if ($isInteractionType) {
                return new InteractionAction($source, $type, $arguments, $identifier);
            }

            $value = $this->findInputValue($identifier, $arguments);

            return new InputAction($source, $arguments, $identifier, $value);
        }

        return new Action($source, $type, $arguments);
    }

    private function findType(string $source): ?string
    {
        $sourceLength = mb_strlen($source);

        foreach (self::TYPES as $type) {
            $typeLength = strlen($type);

            if ($sourceLength >= $typeLength) {
                $sourcePrefix = mb_substr($source, 0, $typeLength);

                if ($sourcePrefix === $type) {
                    return $type;
                }
            }
        }

        return null;
    }

    private function findIdentifier(string $arguments): string
    {
        if ($this->literalParameterExtractor->handles($arguments)) {
            return $this->literalParameterExtractor->extract($arguments);
        }

        if ($this->pageElementIdentifierExtractor->handles($arguments)) {
            return $this->pageElementIdentifierExtractor->extract($arguments);
        }

        if ($this->variableParameterExtractor->handles($arguments)) {
            return $this->variableParameterExtractor->extract($arguments);
        }

        return '';
    }

    private function findInputValue(string $identifier, string $arguments): string
    {
        $identifierLength = mb_strlen($identifier);
        $toKeywordAndValue = trim(mb_substr($arguments, $identifierLength));

        $toKeyword = 'to';
        $toKeywordLength = strlen($toKeyword);
        $containsToKeyword = mb_substr($toKeywordAndValue, 0, $toKeywordLength) === $toKeyword;

        if ($containsToKeyword) {
            $valueString = trim(mb_substr($toKeywordAndValue, $toKeywordLength));
        } else {
            $valueString = $toKeywordAndValue;
        }

        if ($this->quotedValueExtractor->handles($valueString)) {
            return $this->quotedValueExtractor->extract($valueString);
        }

        if ($this->variableParameterExtractor->handles($valueString)) {
            return $this->variableParameterExtractor->extract($valueString);
        }

        return '';
    }
}
