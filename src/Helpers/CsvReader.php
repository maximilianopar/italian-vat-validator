<?php

namespace App\Helpers;

use RuntimeException;

/**
 * Helper responsible for reading and parsing CSV files.
 * 
 */
class CsvReader
{
    /**
     * Reads a CSV file and returns all non-empty values found.
     */
    public function readColumn(string $filePath): array
    {
        // Check if the file exists before attempting to read it
        if (!file_exists($filePath)) {
            throw new RuntimeException('CSV file not found.');
        }

        // Try to open the file in read-only binary mode
        $handle = fopen($filePath, 'rb');
        if ($handle === false) {
            throw new RuntimeException('Unable to open uploaded CSV file.');
        }

        $values = [];

        // Read the CSV file line by line
        while (($row = fgetcsv($handle)) !== false) {

            // Skip empty rows
            if ($row === [null] || $row === false) {
                continue;
            }

            foreach ($row as $cell) {

                $cell = trim((string) $cell);

                if ($cell !== '') {
                    $values[] = $cell;
                }
            }
        }

        fclose($handle);

        return $values;
    }
}