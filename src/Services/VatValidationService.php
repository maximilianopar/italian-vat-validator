<?php

namespace App\Services;

use App\Models\VatAnalysisResult;

class VatValidationService
{
    public const STATUS_VALID = 'valid';
    public const STATUS_CORRECTED = 'corrected';
    public const STATUS_INVALID = 'invalid';

    /**
     * Analyzes one VAT value.
     * Returns whether it is valid, corrected or invalid.
     */
    public function analyze(string $rawInput): VatAnalysisResult
    {
        // Keep original value trimmed
        $original = trim($rawInput);

        // Empty values are invalid
        if ($original === '') {
            return new VatAnalysisResult(
                originalValue: $rawInput,
                finalValue: null,
                status: self::STATUS_INVALID,
                message: 'Empty value.',
                modifications: 'No processing possible.'
            );
        }

        // Normalize value to uppercase
        $normalized = strtoupper($original);

        // Case 1: already valid -> IT + 11 digits
        if (preg_match('/^IT\d{11}$/', $normalized) === 1) {
            return new VatAnalysisResult(
                originalValue: $original,
                finalValue: $normalized,
                status: self::STATUS_VALID,
                message: 'VAT number is valid.',
                modifications: 'No changes needed.'
            );
        }

        // Case 2: only 11 digits -> add missing IT prefix
        if (preg_match('/^\d{11}$/', $normalized) === 1) {
            $corrected = 'IT' . $normalized;

            return new VatAnalysisResult(
                originalValue: $original,
                finalValue: $corrected,
                status: self::STATUS_CORRECTED,
                message: 'VAT number corrected successfully.',
                modifications: 'Added missing IT prefix.'
            );
        }

        // Case 3: starts with IT but format is still wrong
        if (str_starts_with($normalized, 'IT')) {
            $suffix = substr($normalized, 2);

            // Characters after IT must all be digits
            if (!preg_match('/^\d+$/', $suffix)) {
                return new VatAnalysisResult(
                    originalValue: $original,
                    finalValue: null,
                    status: self::STATUS_INVALID,
                    message: 'Invalid VAT number.',
                    modifications: 'Prefix IT exists, but the remaining characters are not all digits.'
                );
            }

            // Digits exist, but count is not exactly 11
            return new VatAnalysisResult(
                originalValue: $original,
                finalValue: null,
                status: self::STATUS_INVALID,
                message: 'Invalid VAT number.',
                modifications: 'Prefix IT exists, but it must be followed by exactly 11 digits.'
            );
        }

        // Case 4: completely invalid format
        return new VatAnalysisResult(
            originalValue: $original,
            finalValue: null,
            status: self::STATUS_INVALID,
            message: 'Invalid VAT number.',
            modifications: 'It must start with IT and contain exactly 11 digits after the prefix, or be exactly 11 digits so it can be corrected.'
        );
    }

    /**
     * Analyzes multiple VAT values.
     * Returns an array of VatAnalysisResult objects.
     */
    public function analyzeMany(array $values): array
    {
        $results = [];

        foreach ($values as $value) {
            $results[] = $this->analyze((string) $value);
        }

        return $results;
    }
}