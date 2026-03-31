<?php

namespace App\Repositories;

use App\Models\VatAnalysisResult;
use PDO;

class VatRepository
{
    public function __construct(private PDO $connection)
    {
    }

    /**
     * Creates a new import batch.
     * Used to identify all VAT results that belong to the same CSV upload.
     */
    public function createBatch(?string $originalFilename = null): int
    {
        $statement = $this->connection->prepare(
            'INSERT INTO import_batches (original_filename, created_at) 
             VALUES (:original_filename, NOW())'
        );

        $statement->execute([
            'original_filename' => $originalFilename,
        ]);

        return (int) $this->connection->lastInsertId();
    }

    /**
     * Inserts one VAT analysis result into database.
     */
    public function insertResult(VatAnalysisResult $result): void
    {
        $statement = $this->connection->prepare(
            'INSERT INTO vat_results (
                batch_id,
                original_value,
                final_value,
                status,
                message,
                modifications,
                created_at
            ) VALUES (
                :batch_id,
                :original_value,
                :final_value,
                :status,
                :message,
                :modifications,
                NOW()
            )'
        );

        $statement->execute($result->toArray());
    }

    /**
     * Gets all results for one batch and groups them by status.
     */
    public function getGroupedResultsByBatch(int $batchId): array
    {
        $statement = $this->connection->prepare(
            'SELECT * 
             FROM vat_results 
             WHERE batch_id = :batch_id 
             ORDER BY id DESC'
        );

        $statement->execute([
            'batch_id' => $batchId,
        ]);

        $rows = $statement->fetchAll();

        return $this->groupRowsByStatus($rows);
    }

    /**
     * Gets the latest created batch data.
     * Returns null if no batch exists yet.
     */
    public function getLatestBatch(): ?array
    {
        $statement = $this->connection->query(
            'SELECT id, original_filename
            FROM import_batches
            ORDER BY id DESC
            LIMIT 1'
        );

        $row = $statement->fetch();

        return $row ?: null;
    }

    /**
     * Gets latest batch results grouped by status.
     */
    public function getLatestGroupedResults(): array
    {
        $batch = $this->getLatestBatch();

        if (!$batch) {
            return [
                'batch_id' => null,
                'filename' => null,
                'valid' => [],
                'corrected' => [],
                'invalid' => [],
            ];
        }

        $grouped = $this->getGroupedResultsByBatch((int) $batch['id']);

        return [
            'batch_id' => (int) $batch['id'],
            'filename' => $batch['original_filename'],
            'valid' => $grouped['valid'] ?? [],
            'corrected' => $grouped['corrected'] ?? [],
            'invalid' => $grouped['invalid'] ?? [],
        ];
    }

    /**
     * Groups database rows by VAT status.
     */
    private function groupRowsByStatus(array $rows): array
    {
        $groupedResults = [
            'valid' => [],
            'corrected' => [],
            'invalid' => [],
        ];

        foreach ($rows as $row) {
            $status = $row['status'] ?? null;

            // Ignore unknown statuses
            if (!isset($groupedResults[$status])) {
                continue;
            }

            $groupedResults[$status][] = $row;
        }

        return $groupedResults;
    }
}