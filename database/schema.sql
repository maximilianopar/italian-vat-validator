CREATE DATABASE IF NOT EXISTS italian_vat_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE italian_vat_app;

CREATE TABLE IF NOT EXISTS import_batches (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    original_filename VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS vat_results (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    batch_id INT UNSIGNED NULL,
    original_value VARCHAR(255) NOT NULL,
    final_value VARCHAR(255) NULL,
    status ENUM('valid', 'corrected', 'invalid') NOT NULL,
    message VARCHAR(255) NOT NULL,
    modifications VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_vat_results_batch_id
        FOREIGN KEY (batch_id) REFERENCES import_batches(id)
        ON DELETE SET NULL
);
