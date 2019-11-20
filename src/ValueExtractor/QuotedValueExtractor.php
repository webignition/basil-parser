<?php

namespace webignition\BasilParser\ValueExtractor;

class QuotedValueExtractor
{
    private const DELIMITER = '"';
    private const ESCAPE_CHARACTER = '\\';

    public function handles(string $string): bool
    {
        if ('' === $string) {
            return false;
        }

        return self::DELIMITER === $string[0];
    }

    public function extract(string $string): string
    {
        if (!$this->handles($string)) {
            return '';
        }

        $previousCharacter = '';
        $characters = preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY);
        if (false === $characters) {
            $characters = [];
        }

        array_shift($characters);

        $stringCharacters = [];

        foreach ($characters as $character) {
            if (self::DELIMITER === $character) {
                if (self::ESCAPE_CHARACTER !== $previousCharacter) {
                    return self::DELIMITER . implode('', $stringCharacters) . self::DELIMITER;
                }
            }

            $stringCharacters[] = $character;
            $previousCharacter = $character;
        }

        return self::DELIMITER . implode('', $stringCharacters) . self::DELIMITER;
    }
}
