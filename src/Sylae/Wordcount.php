<?php

/**
 * Copyright (c) 2019 Keira Dueck <sylae@calref.net>
 * Use of this source code is governed by the MIT license, which
 * can be found in the LICENSE file.
 */

namespace Sylae;


use IntlChar;

class Wordcount
{
    /**
     * Given a chunk of text, tell us how many words are in it!
     *
     * @param string $text The text you want a wordcount of, probably in UTF-8 encoding
     *
     * @return int The wordcount
     */
    public static function count(string $text): int
    {
        // multiple hyphens are to be interpreted as a word break, this is hacky but w/e
        $text = preg_replace("/-{2,}/", " ", $text);

        $last = null;
        $wc = 0;
        $letters = self::getLetters();
        foreach (preg_split('//u', $text, null, PREG_SPLIT_NO_EMPTY) as $char) {
            $isLetter = in_array(IntlChar::ord($char), $letters);
            if ($isLetter && $last !== $isLetter) {
                $wc++;
            }
            $last = $isLetter;
        }
        return $wc;
    }

    /**
     * Get the code points that "count" as letters.
     *
     * @return array
     */
    private static function getLetters(): array
    {
        static $letters = [];
        if (count($letters) > 0) {
            return $letters;
        }

        $categories = [
            IntlChar::CHAR_CATEGORY_UPPERCASE_LETTER,
            IntlChar::CHAR_CATEGORY_LOWERCASE_LETTER,
            IntlChar::CHAR_CATEGORY_TITLECASE_LETTER,
            IntlChar::CHAR_CATEGORY_MODIFIER_LETTER,
            IntlChar::CHAR_CATEGORY_OTHER_LETTER,
            IntlChar::CHAR_CATEGORY_LETTER_NUMBER,
            IntlChar::CHAR_CATEGORY_CONNECTOR_PUNCTUATION,
            IntlChar::CHAR_CATEGORY_DECIMAL_DIGIT_NUMBER,
            IntlChar::CHAR_CATEGORY_OTHER_NUMBER,
        ];
        $breaks = [
            IntlChar::WB_MIDNUM,
            IntlChar::WB_MIDNUMLET,
            IntlChar::WB_SINGLE_QUOTE,
        ];

        $x = 0x0;
        while ($x < 0x10ffff) {
            $cp = $x;
            $x++;

            if (!IntlChar::isdefined($cp)) {
                continue;
            }

            $hyphenMatch = ($cp == 0x00ad || $cp == 0x002d);
            $catMatch = in_array(IntlChar::charType($cp), $categories, true);
            $wbMatch = in_array(IntlChar::getIntPropertyValue($cp, IntlChar::PROPERTY_WORD_BREAK), $breaks, true);

            if ($hyphenMatch || $catMatch || $wbMatch) {
                $letters[] = $cp;
            }
        }
        return $letters;
    }

    /**
     * the list of valid letters is normally generated when wordcount() is called for the first time. If you'd like it
     * to load earlier (since it takes a hot moment), use this handy method!
     */
    public static function loadLetters(): void
    {
        self::getLetters();
    }
}
