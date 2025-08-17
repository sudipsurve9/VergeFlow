<?php

namespace App\Helpers;

class NumberToWords
{
    private static $ones = [
        '', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine',
        'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen',
        'seventeen', 'eighteen', 'nineteen'
    ];

    private static $tens = [
        '', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'
    ];

    private static $thousands = [
        '', 'thousand', 'million', 'billion', 'trillion'
    ];

    public static function convert($number)
    {
        if ($number == 0) {
            return 'zero';
        }

        $number = (int) $number;
        $result = '';

        if ($number < 0) {
            $result = 'minus ';
            $number = abs($number);
        }

        $groups = [];
        while ($number > 0) {
            $groups[] = $number % 1000;
            $number = intval($number / 1000);
        }

        $groupCount = count($groups);
        for ($i = $groupCount - 1; $i >= 0; $i--) {
            $group = $groups[$i];
            if ($group != 0) {
                $groupText = self::convertGroup($group);
                if ($i > 0) {
                    $groupText .= ' ' . self::$thousands[$i];
                }
                if ($result != '' && $result != 'minus ') {
                    $result .= ' ';
                }
                $result .= $groupText;
            }
        }

        return trim($result);
    }

    private static function convertGroup($number)
    {
        $result = '';

        $hundreds = intval($number / 100);
        if ($hundreds > 0) {
            $result = self::$ones[$hundreds] . ' hundred';
        }

        $remainder = $number % 100;
        if ($remainder > 0) {
            if ($result != '') {
                $result .= ' ';
            }

            if ($remainder < 20) {
                $result .= self::$ones[$remainder];
            } else {
                $tens = intval($remainder / 10);
                $ones = $remainder % 10;
                $result .= self::$tens[$tens];
                if ($ones > 0) {
                    $result .= ' ' . self::$ones[$ones];
                }
            }
        }

        return $result;
    }
}
