<?php

session_start();

require_once __DIR__ . '/../src/Config/Database.php';
require_once __DIR__ . '/../src/Controllers/VatController.php';
require_once __DIR__ . '/../src/Helpers/CsvReader.php';
require_once __DIR__ . '/../src/Repositories/VatRepository.php';
require_once __DIR__ . '/../src/Services/VatValidationService.php';
require_once __DIR__ . '/../src/Models/VatAnalysisResult.php';

use App\Config\Database;
use App\Controllers\VatController;
use App\Helpers\CsvReader;
use App\Repositories\VatRepository;
use App\Services\VatValidationService;

// Load application configuration
$config = require __DIR__ . '/../config/config.php';

try {
    // Create main objects used by the application
    $database = new Database($config['db']);
    $vatRepository = new VatRepository($database->getConnection());
    $vatService = new VatValidationService();
    $csvReader = new CsvReader();

    // Create controller and inject dependencies
    $controller = new VatController($vatService, $vatRepository, $csvReader, $config['app']);

    // Detect requested action and HTTP method
    $action = $_GET['action'] ?? 'home';
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

    // Route POST request for CSV upload
    if ($action === 'upload' && $method === 'POST') {
        $controller->uploadCsv();
        return;
    }

    // Route POST request for single VAT validation
    if ($action === 'single-check' && $method === 'POST') {
        $controller->validateSingleVat();
        return;
    }

    // Default action: show main page
    $controller->showHome();

} catch (Throwable $e) {
    http_response_code(500);

    echo '<h1>Internal error</h1>';
    echo '<p><strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . '</p>';
    echo '<p><strong>Line:</strong> ' . htmlspecialchars((string)$e->getLine()) . '</p>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
}