<?php

namespace webignition\BasilParser\IdentifierExtractor;

class IdentifierExtractor
{
    private $literalParameterIdentifierExtractor;
    private $pageElementIdentifierExtractor;
    private $variableParameterIdentifierExtractor;

    public function __construct(
        LiteralParameterIdentifierExtractor $literalParameterIdentifierExtractor,
        PageElementIdentifierExtractor $pageElementIdentifierExtractor,
        VariableParameterIdentifierExtractor $variableParameterIdentifierExtractor
    ) {
        $this->literalParameterIdentifierExtractor = $literalParameterIdentifierExtractor;
        $this->pageElementIdentifierExtractor = $pageElementIdentifierExtractor;
        $this->variableParameterIdentifierExtractor = $variableParameterIdentifierExtractor;
    }

    public static function create(): IdentifierExtractor
    {
        return new IdentifierExtractor(
            new LiteralParameterIdentifierExtractor(),
            new PageElementIdentifierExtractor(),
            new VariableParameterIdentifierExtractor()
        );
    }

    public function extract(string $string): ?string
    {
        if ($this->literalParameterIdentifierExtractor->handles($string)) {
            return $this->literalParameterIdentifierExtractor->extract($string);
        }

        if ($this->pageElementIdentifierExtractor->handles($string)) {
            return $this->pageElementIdentifierExtractor->extract($string);
        }

        if ($this->variableParameterIdentifierExtractor->handles($string)) {
            return $this->variableParameterIdentifierExtractor->extract($string);
        }

        return null;
    }
}
