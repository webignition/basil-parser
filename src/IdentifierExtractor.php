<?php

declare(strict_types=1);

namespace webignition\BasilParser;

use webignition\BasilParser\ValueExtractor\LiteralValueExtractor;
use webignition\BasilParser\ValueExtractor\PageElementIdentifierExtractor;
use webignition\BasilParser\ValueExtractor\VariableValueExtractor;

class IdentifierExtractor
{
    private $literalValueExtractor;
    private $pageElementIdentifierExtractor;
    private $variableValueExtractor;

    public function __construct(
        LiteralValueExtractor $literalValueExtractor,
        PageElementIdentifierExtractor $pageElementIdentifierExtractor,
        VariableValueExtractor $variableValueExtractor
    ) {
        $this->literalValueExtractor = $literalValueExtractor;
        $this->pageElementIdentifierExtractor = $pageElementIdentifierExtractor;
        $this->variableValueExtractor = $variableValueExtractor;
    }

    public static function create(): IdentifierExtractor
    {
        return new IdentifierExtractor(
            new LiteralValueExtractor(),
            new PageElementIdentifierExtractor(),
            new VariableValueExtractor()
        );
    }

    public function extract(string $string): string
    {
        if ($this->literalValueExtractor->handles($string)) {
            return $this->literalValueExtractor->extract($string);
        }

        if ($this->pageElementIdentifierExtractor->handles($string)) {
            return $this->pageElementIdentifierExtractor->extract($string);
        }

        if ($this->variableValueExtractor->handles($string)) {
            return $this->variableValueExtractor->extract($string);
        }

        return '';
    }
}
