<?php

/**
 * Copyright (c) 2021 Keira Dueck <sylae@calref.net>
 * Use of this source code is governed by the MIT license, which
 * can be found in the LICENSE file.
 */

namespace Sylae;

use IntlChar;
use OutOfRangeException;

class Wordcount
{

    protected const CATEGORIES = [
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

    protected const BREAKS = [
        IntlChar::WB_MIDNUM,
        IntlChar::WB_MIDNUMLET,
        IntlChar::WB_SINGLE_QUOTE,
    ];

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
        foreach (preg_split('//u', $text, null, PREG_SPLIT_NO_EMPTY) as $char) {
            $isLetter = self::isLetter(IntlChar::ord($char));
            if ($last !== $isLetter && $isLetter) {
                $wc++;
            }
            $last = $isLetter;
        }
        return $wc;
    }

    protected static function isLetter(int $codepoint): bool
    {
        static $cache = [];
        if (array_key_exists($codepoint, $cache)) {
            return $cache[$codepoint];
        }

        if (!IntlChar::isdefined($codepoint)) {
            throw new OutOfRangeException("Unknown codepoint $codepoint");
        }

        $hyphenMatch = ($codepoint == 0x00ad || $codepoint == 0x002d);
        $catMatch = in_array(IntlChar::charType($codepoint), self::CATEGORIES, true);
        $wbMatch = in_array(
            IntlChar::getIntPropertyValue($codepoint, IntlChar::PROPERTY_WORD_BREAK),
            self::BREAKS,
            true
        );

        $cache[$codepoint] = ($hyphenMatch || $catMatch || $wbMatch);
        return $cache[$codepoint];
    }

}
