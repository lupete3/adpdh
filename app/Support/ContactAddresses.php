<?php

namespace App\Support;

class ContactAddresses
{
    public const TOPBAR = 'Av P.E Lumumba • Bukavu, Av Galilee • Kinshasa, Q. Namyanda • Uvira • République démocratique du Congo';

    public const DEFAULT = "22, avenue Galilee, Commune Ngaliema, Kinshasa, RD Congo\n305, avenue Patrice Emery Lumumba, Commune d’Ibanda, Bukavu, Sud-Kivu, RD Congo\n48, quartier Namyanda, Uvira, Sud-Kivu, RD Congo";

    public static function text(?string $value): string
    {
        if (! trim($value ?? '') || $value === '305, avenue Patrice Emery Lumumba, commune d’Ibanda, Bukavu, Sud-Kivu, RDC') {
            return self::DEFAULT;
        }

        // Split the old separator only when it introduces another street number.
        $value = preg_replace('/\h+-\h+(?=\d+\s*,)/u', "\n", $value);

        return implode("\n", array_filter(array_map('trim', preg_split('/\R/u', $value)), fn ($line) => $line !== ''));
    }
}
