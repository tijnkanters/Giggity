<?php

namespace App\Support;

class AirportData
{
    protected static ?array $airports = null;

    public static function all(): array
    {
        if (static::$airports === null) {
            static::$airports = json_decode(
                file_get_contents(database_path('data/airports.json')),
                true
            );
        }

        return static::$airports;
    }

    /**
     * Get select options: "AMS" => "AMS — Amsterdam Airport Schiphol (Amsterdam)"
     */
    public static function selectOptions(): array
    {
        $options = [];
        foreach (static::all() as $iata => $data) {
            $options[$iata] = "{$iata} — {$data['name']} ({$data['city']})";
        }

        return $options;
    }

    /**
     * Get location string for a given IATA code.
     */
    public static function locationFor(string $iata): ?string
    {
        $airport = static::all()[$iata] ?? null;

        if (!$airport) {
            return null;
        }

        return "{$airport['name']}, {$airport['city']}";
    }
}
