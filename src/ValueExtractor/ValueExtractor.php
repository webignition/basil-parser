<?php

namespace webignition\BasilParser\ValueExtractor;

use webignition\BasilParser\VariableParameterExtractor;

class ValueExtractor
{
    private $quotedValueExtractor;
    private $variableParameterExtractor;

    public function __construct(
        QuotedValueExtractor $quotedStringExtractor,
        VariableParameterExtractor $variableParameterIdentifierExtractor
    ) {
        $this->quotedValueExtractor = $quotedStringExtractor;
        $this->variableParameterExtractor = $variableParameterIdentifierExtractor;
    }

    public static function create(): ValueExtractor
    {
        return new ValueExtractor(
            new QuotedValueExtractor(),
            new VariableParameterExtractor()
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

        if ($this->variableParameterExtractor->handles($string)) {
            return $this->variableParameterExtractor->extract($string);
        }

        return '';
    }
}
