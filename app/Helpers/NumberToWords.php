<?php

namespace App\Helpers;

class NumberToWords
{
    protected static $ones = [
        "",
        "One",
        "Two",
        "Three",
        "Four",
        "Five",
        "Six",
        "Seven",
        "Eight",
        "Nine",
        "Ten",
        "Eleven",
        "Twelve",
        "Thirteen",
        "Fourteen",
        "Fifteen",
        "Sixteen",
        "Seventeen",
        "Eighteen",
        "Nineteen"
    ];

    protected static $tens = [
        "",
        "",
        "Twenty",
        "Thirty",
        "Forty",
        "Fifty",
        "Sixty",
        "Seventy",
        "Eighty",
        "Ninety"
    ];

    protected static $scales = [
        "",
        "Thousand",
        "Million",
        "Billion",
        "Trillion"
    ];

    /**
     * Convert a number to its word representation
     *
     * @param float $number
     * @return string
     */
    public static function convert($number)
    {
        if ($number < 0) {
            return "Negative " . self::convert(abs($number));
        }
        
        $string = "";
        
        // Split the number into whole and decimal parts
        $parts = explode('.', (string) $number);
        $wholePart = $parts[0];
        $decimalPart = isset($parts[1]) ? str_pad(substr($parts[1], 0, 2), 2, '0', STR_PAD_RIGHT) : "00";
        
        // Convert whole part
        if ($wholePart == "0") {
            $string = "Zero";
        } else {
            $string = self::convertTriplets($wholePart);
        }
        
        // Add decimal part
        $string .= " and " . $decimalPart . "/100";
        
        return $string;
    }

    /**
     * Convert groups of 3 digits to words
     *
     * @param string $num
     * @return string
     */
    private static function convertTriplets($num)
    {
        // Add leading zeros if needed
        $num = str_pad($num, ceil(strlen($num) / 3) * 3, "0", STR_PAD_LEFT);
        
        // Split the number into triplets
        $triplets = str_split($num, 3);
        
        // Process each triplet
        $string = "";
        foreach ($triplets as $i => $triplet) {
            $tripletIndex = count($triplets) - $i - 1;
            
            // Skip if the triplet is 000
            if ($triplet == "000") continue;
            
            // Convert the triplet to words
            $tripletString = self::convertTriplet($triplet);
            
            // Add the scale
            if ($tripletIndex > 0) {
                $tripletString .= " " . self::$scales[$tripletIndex];
            }
            
            // Add to result
            if ($string) {
                $string .= ", " . $tripletString;
            } else {
                $string = $tripletString;
            }
        }
        
        return $string;
    }

    /**
     * Convert a triplet (3 digits) to words
     *
     * @param string $triplet
     * @return string
     */
    private static function convertTriplet($triplet)
    {
        // Extract hundreds, tens, and ones
        $hundreds = (int) $triplet[0];
        $tensAndOnes = (int) substr($triplet, 1);
        
        $string = "";
        
        // Process hundreds
        if ($hundreds > 0) {
            $string = self::$ones[$hundreds] . " Hundred";
            if ($tensAndOnes > 0) {
                $string .= " and ";
            }
        }
        
        // Process tens and ones
        if ($tensAndOnes > 0) {
            if ($tensAndOnes < 20) {
                $string .= self::$ones[$tensAndOnes];
            } else {
                $tens = (int) ($tensAndOnes / 10);
                $ones = $tensAndOnes % 10;
                $string .= self::$tens[$tens];
                if ($ones > 0) {
                    $string .= "-" . self::$ones[$ones];
                }
            }
        }
        
        return $string;
    }
} 