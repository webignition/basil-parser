<?php

namespace webignition\BasilParser\ValueExtractor;

class LiteralValueExtractor
{
    public function handles(string $string): bool
    {
        if ('' === $string) {
            return false;
        }

        $firstCharacter = $string[0];

        return $firstCharacter !== '"' && $firstCharacter !== '$';
    }

    public function extract(string $string): string
    {
        if (!$this->handles($string)) {
            return '';
        }

        $spacePosition = mb_strpos($string, ' ');

        if (false === $spacePosition) {
            return $string;
        }

        return mb_substr($string, 0, $spacePosition);
    }
}
