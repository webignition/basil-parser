<?php

namespace webignition\BasilParser\ValueExtractor;

use webignition\BasilParser\IdentifierExtractor;

class ValueExtractor
{
    private $quotedValueExtractor;
    private $identifierExtractor;

    public function __construct(QuotedValueExtractor $quotedValueExtractor, IdentifierExtractor $identifierExtractor)
    {
        $this->quotedValueExtractor = $quotedValueExtractor;
        $this->identifierExtractor = $identifierExtractor;
    }

    public static function create(): ValueExtractor
    {
        return new ValueExtractor(
            new QuotedValueExtractor(),
            IdentifierExtractor::create()
        );
    }

    public function extract(string $string): string
    {
        $value = $this->identifierExtractor->extract($string);
        if ('' !== $value) {
            return $value;
        }

        $value = $this->quotedValueExtractor->extract($string);
        if ('' !== $value) {
            return $value;
        }

        return '';
    }
}
