<?php

namespace App\Helpers;

class TimeFormatter
{
    // Converts minutes to a human-readable format (e.g., "1h 30m" or "45m")
    public static function minutesToHuman(?int $minutes): string {
        if (!$minutes) {
            return '-';
        }

        if ($minutes < 60) {
            return "{$minutes}m";
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes === 0) {
            return "{$hours}h";
        }

        return "{$hours}h {$remainingMinutes}m";
    }

    // Converts a human-readable format (e.g., "1h 30m" or "45m") to minutes
    public static function humanToMinutes(?string $input): ?int {
        if (blank($input)) {
            return null;
        }

        $input = strtolower(trim($input));

        // Just a number => minutes
        if (preg_match('/^\d+$/', $input)) {
            return (int) $input;
        }

        $minutes = 0;

        if (preg_match('/(\d+)\s*h/', $input, $hoursMatch)) {
            $minutes += ((int) $hoursMatch[1]) * 60;
        }

        if (preg_match('/(\d+)\s*m/', $input, $minutesMatch)) {
            $minutes += (int) $minutesMatch[1];
        }

        return $minutes;
    }
}
