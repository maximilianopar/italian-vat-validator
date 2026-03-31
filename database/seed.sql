USE italian_vat_app;

INSERT INTO import_batches (original_filename, created_at)
VALUES ('seed_example.csv', NOW());

SET @batch_id = LAST_INSERT_ID();

INSERT INTO vat_results (batch_id, original_value, final_value, status, message, modifications, created_at)
VALUES
(@batch_id, 'IT12345678901', 'IT12345678901', 'valid', 'VAT number is valid.', 'No changes needed.', NOW()),
(@batch_id, '98765432158', 'IT98765432158', 'corrected', 'VAT number corrected successfully.', 'Added missing IT prefix.', NOW()),
(@batch_id, 'IT12345', NULL, 'invalid', 'Invalid VAT number.', 'Prefix IT exists, but it must be followed by exactly 11 digits.', NOW()),
(@batch_id, '123-hello', NULL, 'invalid', 'Invalid VAT number.', 'It must start with IT and contain exactly 11 digits after the prefix, or be exactly 11 digits so it can be corrected.', NOW());
