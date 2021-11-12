<?php

declare(strict_types=1);

namespace webignition\BasilParser;

use webignition\BasilModels\Action\Action;
use webignition\BasilModels\Action\ActionInterface;
use webignition\BasilParser\Exception\UnparseableActionException;
use webignition\BasilValueExtractor\IdentifierExtractor;
use webignition\BasilValueExtractor\ValueExtractor;

class ActionParser
{
    private IdentifierExtractor $identifierExtractor;
    private ValueExtractor $valueExtractor;

    public function __construct(IdentifierExtractor $identifierExtractor, ValueExtractor $valueExtractor)
    {
        $this->identifierExtractor = $identifierExtractor;
        $this->valueExtractor = $valueExtractor;
    }

    public static function create(): ActionParser
    {
        return new ActionParser(
            IdentifierExtractor::createExtractor(),
            ValueExtractor::createExtractor()
        );
    }

    /**
     * @throws UnparseableActionException
     */
    public function parse(string $source): ActionInterface
    {
        $source = trim($source);

        if ('' === $source) {
            throw UnparseableActionException::createEmptyActionException();
        }

        $type = $this->findType($source);
        $arguments = trim(mb_substr($source, strlen($type)));

        if (Action::isWaitType($type)) {
            return new Action($source, $type, $arguments, null, $arguments);
        }

        $isInteractionType = Action::isInteractionType($type);
        $isInputType = Action::isInputType($type);

        if ($isInteractionType || $isInputType) {
            $identifier = $this->identifierExtractor->extract($arguments);

            if (null === $identifier) {
                throw UnparseableActionException::createInvalidIdentifierException($source);
            }

            if ($isInteractionType) {
                return new Action($source, $type, $arguments, $identifier);
            }

            $value = $this->findInputValue($identifier, $arguments);

            if (null === $value) {
                throw UnparseableActionException::createEmptyInputActionValueException($source);
            }

            return new Action($source, $type, $arguments, $identifier, $value);
        }

        return new Action($source, $type, $arguments);
    }

    private function findType(string $source): string
    {
        $parts = explode(' ', $source, 2);

        return (string) $parts[0];
    }

    private function findInputValue(string $identifier, string $arguments): ?string
    {
        $identifierLength = mb_strlen($identifier);
        $toKeywordAndValue = trim(mb_substr($arguments, $identifierLength));
        $toKeyword = 'to';

        if ($toKeyword === $toKeywordAndValue) {
            return null;
        }

        $toKeywordLength = strlen($toKeyword);
        $containsToKeyword = mb_substr($toKeywordAndValue, 0, $toKeywordLength) === $toKeyword;

        if ($containsToKeyword) {
            $valueString = trim(mb_substr($toKeywordAndValue, $toKeywordLength));
        } else {
            $valueString = $toKeywordAndValue;
        }

        $value = $this->valueExtractor->extract($valueString);
        if ('' !== $value) {
            return $value;
        }

        return null;
    }
}
