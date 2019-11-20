<?php

namespace webignition\BasilParser\IdentifierExtractor;

use webignition\BasilParser\VariableParameterExtractor;

class IdentifierExtractor
{
    private $literalParameterIdentifierExtractor;
    private $pageElementIdentifierExtractor;
    private $variableParameterExtractor;

    public function __construct(
        LiteralParameterIdentifierExtractor $literalParameterIdentifierExtractor,
        PageElementIdentifierExtractor $pageElementIdentifierExtractor,
        VariableParameterExtractor $variableParameterIdentifierExtractor
    ) {
        $this->literalParameterIdentifierExtractor = $literalParameterIdentifierExtractor;
        $this->pageElementIdentifierExtractor = $pageElementIdentifierExtractor;
        $this->variableParameterExtractor = $variableParameterIdentifierExtractor;
    }

    public static function create(): IdentifierExtractor
    {
        return new IdentifierExtractor(
            new LiteralParameterIdentifierExtractor(),
            new PageElementIdentifierExtractor(),
            new VariableParameterExtractor()
        );
    }

    public function extract(string $string): string
    {
        if ($this->literalParameterIdentifierExtractor->handles($string)) {
            return $this->literalParameterIdentifierExtractor->extract($string);
        }

        if ($this->pageElementIdentifierExtractor->handles($string)) {
            return $this->pageElementIdentifierExtractor->extract($string);
        }

        if ($this->variableParameterExtractor->handles($string)) {
            return $this->variableParameterExtractor->extract($string);
        }

        return '';
    }
}
