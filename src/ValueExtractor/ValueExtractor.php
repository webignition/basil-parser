<?php

namespace webignition\BasilParser\ValueExtractor;

use webignition\BasilParser\IdentifierExtractor\VariableParameterIdentifierExtractor;

class ValueExtractor
{
    private $quotedValueExtractor;
    private $variableParameterIdentifierExtractor;

    public function __construct(
        QuotedValueExtractor $quotedStringExtractor,
        VariableParameterIdentifierExtractor $variableParameterIdentifierExtractor
    ) {
        $this->quotedValueExtractor = $quotedStringExtractor;
        $this->variableParameterIdentifierExtractor = $variableParameterIdentifierExtractor;
    }

    public static function create(): ValueExtractor
    {
        return new ValueExtractor(
            new QuotedValueExtractor(),
            new VariableParameterIdentifierExtractor()
        );
    }

    public function extract(string $string): string
    {
        $string = trim($string);

        if ('' === $string) {
            return '';
        }

        if ($this->quotedValueExtractor->handles($string)) {
            return $this->quotedValueExtractor->extract($string);
        }

        if ($this->variableParameterIdentifierExtractor->handles($string)) {
            return $this->variableParameterIdentifierExtractor->extract($string);
        }

        return '';
    }
}
