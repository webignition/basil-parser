<?php

declare(strict_types=1);

namespace webignition\BasilParser;

use webignition\BasilDomIdentifierFactory\Extractor\DescendantIdentifierExtractor;
use webignition\BasilDomIdentifierFactory\Extractor\ElementIdentifierExtractor;
use webignition\BasilParser\ValueExtractor\VariableValueExtractor;

class IdentifierExtractor
{
    private $elementIdentifierExtractor;
    private $variableValueExtractor;
    private $descendantIdentifierExtractor;

    public function __construct(
        ElementIdentifierExtractor $elementIdentifierExtractor,
        VariableValueExtractor $variableValueExtractor,
        DescendantIdentifierExtractor $descendantIdentifierExtractor
    ) {
        $this->elementIdentifierExtractor = $elementIdentifierExtractor;
        $this->variableValueExtractor = $variableValueExtractor;
        $this->descendantIdentifierExtractor = $descendantIdentifierExtractor;
    }

    public static function create(): IdentifierExtractor
    {
        return new IdentifierExtractor(
            ElementIdentifierExtractor::createExtractor(),
            new VariableValueExtractor(),
            DescendantIdentifierExtractor::createExtractor()
        );
    }

    public function extract(string $string): ?string
    {
        $identifier = $this->descendantIdentifierExtractor->extractIdentifier($string);
        if (null !== $identifier) {
            return $identifier;
        }

        $identifier = $this->elementIdentifierExtractor->extractIdentifier($string);
        if (null !== $identifier) {
            return $identifier;
        }

        $identifier = $this->variableValueExtractor->extract($string);
        if (null !== $identifier) {
            return $identifier;
        }

        return null;
    }
}
