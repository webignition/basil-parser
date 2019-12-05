<?php

namespace webignition\BasilParser\ValueExtractor;

class ValueExtractor
{
    private $pageElementIdentifierExtractor;
    private $quotedValueExtractor;
    private $variableValueExtractor;

    public function __construct(
        PageElementIdentifierExtractor $pageElementIdentifierExtractor,
        QuotedValueExtractor $quotedValueExtractor,
        VariableValueExtractor $variableValueExtractor
    ) {
        $this->pageElementIdentifierExtractor = $pageElementIdentifierExtractor;
        $this->quotedValueExtractor = $quotedValueExtractor;
        $this->variableValueExtractor = $variableValueExtractor;
    }

    public static function create(): ValueExtractor
    {
        return new ValueExtractor(
            new PageElementIdentifierExtractor(),
            new QuotedValueExtractor(),
            new VariableValueExtractor()
        );
    }

    public function handles(string $string): bool
    {
        if ($this->pageElementIdentifierExtractor->handles($string)) {
            return true;
        }

        if ($this->quotedValueExtractor->handles($string)) {
            return true;
        }

        if ($this->variableValueExtractor->handles($string)) {
            return true;
        }

        return false;
    }

    public function extract(string $string): string
    {
        if ($this->pageElementIdentifierExtractor->handles($string)) {
            return $this->pageElementIdentifierExtractor->extract($string);
        }

        if ($this->quotedValueExtractor->handles($string)) {
            return $this->quotedValueExtractor->extract($string);
        }

        if ($this->variableValueExtractor->handles($string)) {
            return $this->variableValueExtractor->extract($string);
        }

        return '';
    }
}
