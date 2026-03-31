<?php

namespace App\Controllers;

use App\Helpers\CsvReader;
use App\Repositories\VatRepository;
use App\Services\VatValidationService;
use RuntimeException;

class VatController
{
    public function __construct(
        private VatValidationService $vatService,
        private VatRepository $vatRepository,
        private CsvReader $csvReader,
        private array $appConfig
    ) {
    }

    /**
     * Shows the main page.
     * Loads last processed results, flash messages and renders the view.
     */
    public function showHome(): void
    {
        // Get latest CSV results grouped by status
        $groupedResults = $this->vatRepository->getLatestGroupedResults();

        // Get temporary messages/results from session
        $flash = $_SESSION['flash'] ?? null;
        $singleResult = $_SESSION['single_result'] ?? null;
        $reset = $_GET['reset'] ?? null;

        if ($reset) {
            $groupedResults = [
                'batch_id' => null,
                'filename' => null,
                'valid' => [],
                'corrected' => [],
                'invalid' => [],
            ];

            $singleResult = null;

            unset($_SESSION['flash']);
        }

        // Remove them so they are shown only once
        unset($_SESSION['flash'], $_SESSION['single_result']);

        // Load main view
        $this->render('home', [
            'flash' => $flash,
            'singleResult' => $singleResult,
            'groupedResults' => $groupedResults,
            'baseUrl' => $this->appConfig['base_url'],
        ]);
    }

    /**
     * Handles CSV upload.
     * Validates file, saves it, reads values, processes VATs and stores results.
     */
    public function uploadCsv(): void
    {
        try {
            if (!isset($_FILES['vat_csv'])) {
                throw new RuntimeException('No file was uploaded.');
            }

            $file = $_FILES['vat_csv'];

            // Basic upload validations
            $this->validateUploadedFile($file);

            // Save uploaded file
            $destination = $this->storeUploadedFile($file);

            // Read CSV values
            $values = $this->csvReader->readColumn($destination);

            if (count($values) === 0) {
                throw new RuntimeException('The CSV file does not contain any values to process.');
            }

            // Create batch for this import
            $batchId = $this->vatRepository->createBatch((string) $file['name']);

            // Analyze VATs
            $results = $this->vatService->analyzeMany($values);

            // Save results in database
            foreach ($results as $result) {
                $this->vatRepository->insertResult($result->withBatchId($batchId));
            }

            $_SESSION['flash'] = [
                'type' => 'success',
                'message' => sprintf('CSV processed successfully. %d VAT values analyzed.', count($results)),
            ];
        } catch (\Throwable $e) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => $e->getMessage(),
            ];
        }

        $this->redirect('/index.php');
    }

    /**
     * Handles single VAT validation from form input.
     * Uses the service to analyze one value and stores result in session.
     */
    public function validateSingleVat(): void
    {
        // Get VAT from form
        $value = trim($_POST['vat_number'] ?? '');

        // Analyze VAT
        $result = $this->vatService->analyze($value)->toArray();

        // Store result to show after redirect
        $_SESSION['single_result'] = $result;

        $_SESSION['flash'] = [
            'type' => $result['status'] === VatValidationService::STATUS_INVALID ? 'error' : 'success',
            'message' => 'Single VAT validation completed.',
        ];

        $this->redirect('/index.php');
    }

    /**
     * Validates uploaded file.
     * Checks for upload errors, size and file extension.
     */
    private function validateUploadedFile(array $file): void
    {
        if ((int) $file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('The uploaded file contains an upload error.');
        }

        if ((int) $file['size'] > $this->appConfig['max_upload_size']) {
            throw new RuntimeException('The uploaded file exceeds the maximum allowed size.');
        }

        $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));

        if ($extension !== 'csv') {
            throw new RuntimeException('Only CSV files are allowed.');
        }
    }

    /**
     * Stores uploaded file in the uploads directory.
     * Creates folder if needed and moves file from temp location.
     */
    private function storeUploadedFile(array $file): string
    {
        $uploadDir = $this->appConfig['upload_dir'];

        // Create directory if not exists
        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                throw new RuntimeException('Failed to create upload directory.');
            }
        }

        // Generate unique filename
        $safeName = uniqid('vat_', true) . '.csv';
        $destination = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $safeName;

        // Move file
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new RuntimeException('Failed to move uploaded file.');
        }

        return $destination;
    }

    /**
     * Renders a view file and passes data to it.
     */
    private function render(string $view, array $data = []): void
    {
        // Make data available inside the view
        extract($data, EXTR_SKIP);

        require __DIR__ . '/../../views/' . $view . '.php';
    }

    /**
     * Redirects to another URL.
     */
    private function redirect(string $path): void
    {
        header('Location: ' . $this->appConfig['base_url'] . $path);
        exit;
    }
}