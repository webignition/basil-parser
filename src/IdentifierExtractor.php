<?php

declare(strict_types=1);

namespace webignition\BasilParser;

use webignition\BasilParser\ValueExtractor\DescendantPageElementIdentifierExtractor;
use webignition\BasilParser\ValueExtractor\PageElementIdentifierExtractor;
use webignition\BasilParser\ValueExtractor\VariableValueExtractor;

class IdentifierExtractor
{
    private $pageElementIdentifierExtractor;
    private $variableValueExtractor;
    private $descendantPageElementIdentifierExtractor;

    public function __construct(
        PageElementIdentifierExtractor $pageElementIdentifierExtractor,
        VariableValueExtractor $variableValueExtractor,
        DescendantPageElementIdentifierExtractor $descendantPageElementIdentifierExtractor
    ) {
        $this->pageElementIdentifierExtractor = $pageElementIdentifierExtractor;
        $this->variableValueExtractor = $variableValueExtractor;
        $this->descendantPageElementIdentifierExtractor = $descendantPageElementIdentifierExtractor;
    }

    public static function create(): IdentifierExtractor
    {
        return new IdentifierExtractor(
            new PageElementIdentifierExtractor(),
            new VariableValueExtractor(),
            DescendantPageElementIdentifierExtractor::createExtractor()
        );
    }

    public function extract(string $string): string
    {
        $identifier = $this->descendantPageElementIdentifierExtractor->extract($string);
        if ('' !== $identifier) {
            return $identifier;
        }

        $identifier = $this->pageElementIdentifierExtractor->extract($string);
        if ('' !== $identifier) {
            return $identifier;
        }

        $identifier = $this->variableValueExtractor->extract($string);
        if ('' !== $identifier) {
            return $identifier;
        }

        return '';
    }
}
