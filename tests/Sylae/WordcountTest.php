<?php

/**
 * Copyright (c) 2019 Keira Dueck <sylae@calref.net>
 * Use of this source code is governed by the MIT license, which
 * can be found in the LICENSE file.
 */

namespace Sylae;


use OutOfRangeException;
use PHPUnit\Framework\TestCase;

class WordcountTest extends TestCase
{

    /**
     * These are copy-pasted from Iarna's test suite.
     */
    public function testCountAccuracy()
    {
        $this->assertEquals(Wordcount::count('This is a test'), 4, 'plain text');
        $this->assertEquals(Wordcount::count('now with 23 a number'), 5, 'integer');
        $this->assertEquals(Wordcount::count('now with 23.17'), 3, 'decimal');
        $this->assertEquals(Wordcount::count("emoji 😍😍 do not count"), 4, 'emoji');
        $this->assertEquals(Wordcount::count("possessive's are one word"), 4, 'possessive');
        $this->assertEquals(Wordcount::count('possessive’s are one word'), 4, 'possessive unicode');
        $this->assertEquals(Wordcount::count('some "quoted text" does not impact'), 6, 'quotes');
        $this->assertEquals(Wordcount::count("also 'single quotes' are ok"), 5, 'single quotes');
        $this->assertEquals(Wordcount::count("don't do contractions"), 3, 'contractions count as a single word');
        $this->assertEquals(Wordcount::count('hyphenated words-are considered whole'), 4, 'hyphenated words');
        $this->assertEquals(Wordcount::count('underbars are_too just one'), 4, 'underbars');
        $this->assertEquals(Wordcount::count('n-dash ranges 1–3 are NOT'), 6, 'en-dash');
        $this->assertEquals(Wordcount::count('m-dash connected—bits also are not'), 6, 'em-dash');
    }

    /**
     * Tests some weird shit that might result from parser stuff being wonky.
     */
    public function testCountWeirdShit()
    {
        $this->assertEquals(Wordcount::count(''), 0, 'empty string');
        $this->assertEquals(Wordcount::count('---'), 0, 'just some hyphens');
        $this->assertEquals(Wordcount::count(' '), 0, 'just a space');
        $this->assertEquals(Wordcount::count('hi'), 1, 'just one word');

        $this->expectException(OutOfRangeException::class);
        Wordcount::count("\u{FDD0}");
    }
}
