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

    public function handles(string $string): bool
    {
        $identifier = $this->identifierExtractor->extract($string);
        if ('' !== $identifier) {
            return true;
        }

        if ($this->quotedValueExtractor->handles($string)) {
            return true;
        }

        return false;
    }

    public function extract(string $string): string
    {
        $identifier = $this->identifierExtractor->extract($string);
        if ('' !== $identifier) {
            return $identifier;
        }

        if ($this->quotedValueExtractor->handles($string)) {
            return $this->quotedValueExtractor->extract($string);
        }

        return '';
    }
}
