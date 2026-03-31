<?php

namespace App\Models;

class VatAnalysisResult
{
    public function __construct(
        private string $originalValue,
        private ?string $finalValue,
        private string $status,
        private string $message,
        private ?string $modifications = null,
        private ?int $batchId = null
    ) {
    }

    public function toArray(): array
    {
        return [
            'original_value' => $this->originalValue,
            'final_value' => $this->finalValue,
            'status' => $this->status,
            'message' => $this->message,
            'modifications' => $this->modifications,
            'batch_id' => $this->batchId,
        ];
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getOriginalValue(): string
    {
        return $this->originalValue;
    }

    public function getFinalValue(): ?string
    {
        return $this->finalValue;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getModifications(): ?string
    {
        return $this->modifications;
    }

    public function getBatchId(): ?int
    {
        return $this->batchId;
    }

    public function withBatchId(int $batchId): self
    {
        return new self(
            $this->originalValue,
            $this->finalValue,
            $this->status,
            $this->message,
            $this->modifications,
            $batchId
        );
    }
}
