<?php

namespace App\Support;

class BrandSlogan
{
    public const DEFAULT = "Action pour le Développement\net la Promotion des Droits Humains";

    public static function lines(?string $text): array
    {
        $text = trim($text ?? '') ?: self::DEFAULT;
        $lines = array_values(array_filter(array_map('trim', preg_split('/\R/u', $text))));
        if (count($lines) > 1) return [$lines[0], implode(' ', array_slice($lines, 1))];
        if ($text === str_replace("\n", ' ', self::DEFAULT)) return explode("\n", self::DEFAULT);
        $words = preg_split('/\s+/u', $text);
        if (count($words) < 2) return [$text];
        $split = 1;
        $distance = PHP_INT_MAX;
        for ($i = 1; $i < count($words); $i++) {
            $difference = abs(mb_strlen(implode(' ', array_slice($words, 0, $i))) - mb_strlen(implode(' ', array_slice($words, $i))));
            if ($difference < $distance) { $distance = $difference; $split = $i; }
        }
        return [implode(' ', array_slice($words, 0, $split)), implode(' ', array_slice($words, $split))];
    }
}
