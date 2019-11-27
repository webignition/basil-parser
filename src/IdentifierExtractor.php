<?php

declare(strict_types=1);

namespace webignition\BasilParser;

use webignition\BasilParser\ValueExtractor\PageElementIdentifierExtractor;
use webignition\BasilParser\ValueExtractor\VariableValueExtractor;

class IdentifierExtractor
{
    private $pageElementIdentifierExtractor;
    private $variableValueExtractor;

    public function __construct(
        PageElementIdentifierExtractor $pageElementIdentifierExtractor,
        VariableValueExtractor $variableValueExtractor
    ) {
        $this->pageElementIdentifierExtractor = $pageElementIdentifierExtractor;
        $this->variableValueExtractor = $variableValueExtractor;
    }

    public static function create(): IdentifierExtractor
    {
        return new IdentifierExtractor(
            new PageElementIdentifierExtractor(),
            new VariableValueExtractor()
        );
    }

    public function extract(string $string): string
    {
        if ($this->pageElementIdentifierExtractor->handles($string)) {
            return $this->pageElementIdentifierExtractor->extract($string);
        }

        if ($this->variableValueExtractor->handles($string)) {
            return $this->variableValueExtractor->extract($string);
        }

        return '';
    }
}
