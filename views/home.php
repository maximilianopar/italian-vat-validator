<?php require __DIR__ . '/partials/header.php'; ?>

<!-- Main page title -->
<div class="title-row">
    <h1>Italian VAT Numbers Validator</h1>

    <a href="index.php?reset=1" class="reset-btn" title="Clear results">
        ⟳
    </a>
</div>

<p class="small">
   Upload a CSV file to process multiple VAT numbers, or validate a single VAT manually.
</p>

<!-- Flash message -->
<?php if (!empty($flash)): ?>
    <div class="flash <?= htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8'); ?>">
        <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php endif; ?>

<div class="grid">
    <div class="card">
        <h2>Upload CSV</h2>

        <form action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8'); ?>/index.php?action=upload" method="POST" enctype="multipart/form-data">
            <label for="vat_csv">CSV file</label>
            <input type="file" name="vat_csv" id="vat_csv" accept=".csv" required>
            <button type="submit">Process file</button>
        </form>

        <p class="small">
            A VAT number is considered valid if it starts with <strong>IT</strong> followed by 11 digits.
            Numeric values without the IT prefix are automatically corrected.
        </p>
    </div>

    <div class="card">
        <h2>Validate Single VAT</h2>

        <form action="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8'); ?>/index.php?action=single-check" method="POST">
            <label for="vat_number">VAT number</label>
            <input
                type="text"
                name="vat_number"
                id="vat_number"
                placeholder="Example: IT12345678901 or 12345678901"
                required
            >
            <button type="submit">Validate VAT</button>
        </form>

        <?php if (!empty($singleResult)): ?>
            <hr>
            <h3>Single Result</h3>

            <p>
                <strong>Status:</strong>
                <span class="badge <?= htmlspecialchars($singleResult['status'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?= htmlspecialchars(strtoupper($singleResult['status']), ENT_QUOTES, 'UTF-8'); ?>
                </span>
            </p>

            <p><strong>Original:</strong> <?= htmlspecialchars($singleResult['original_value'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Final:</strong> <?= htmlspecialchars($singleResult['final_value'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>Message:</strong> <?= htmlspecialchars($singleResult['message'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p><strong>What was modified / why:</strong> <?= htmlspecialchars($singleResult['modifications'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
    </div>
</div>

<hr class="section-divider">

<h2 class="section-title">Latest Imported Batch Results</h2>

<?php if (empty($groupedResults['batch_id'])): ?>
    <p>No CSV batch has been imported yet.</p>
<?php else: ?>
    <?php
        $validCount = count($groupedResults['valid'] ?? []);
        $correctedCount = count($groupedResults['corrected'] ?? []);
        $invalidCount = count($groupedResults['invalid'] ?? []);
    ?>

    <p class="small">
        Showing the latest imported batch:

        <?php if (!empty($groupedResults['filename'])): ?>
            <strong> Name file:</strong>
            <?= htmlspecialchars($groupedResults['filename'], ENT_QUOTES, 'UTF-8'); ?>
            —
        <?php endif; ?>

        <strong>Batch ID:</strong> <?= (int) $groupedResults['batch_id']; ?>
    </p>

    <h3 style="margin-top: 30px;">Batch Summary:</h3>

    <!-- Summary cards -->
    <div class="stats-row">
        <div class="card stat-card valid">
            <h3>Valid</h3>
            <p class="stat-number"><?= $validCount; ?></p>
        </div>

        <div class="card stat-card corrected">
            <h3>Corrected</h3>
            <p class="stat-number"><?= $correctedCount; ?></p>
        </div>

        <div class="card stat-card invalid">
            <h3>Invalid</h3>
            <p class="stat-number"><?= $invalidCount; ?></p>
        </div>
    </div>

    <h3 style="margin-top: 30px;">Detailed Results:</h3>

    <!-- Valid + Corrected side by side -->
    <div class="results-grid">
        <div class="card">
            <h3>Valid VAT numbers</h3>

            <?php if (empty($groupedResults['valid'])): ?>
                <p>No valid records in the latest batch.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Original</th>
                            <th>Final</th>
                            <th>Status</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($groupedResults['valid'] as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['original_value'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($row['final_value'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><span class="badge valid">VALID</span></td>
                                <td><?= htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="card" style="margin-top: 30px;">
            <h3>Corrected VAT numbers</h3>

            <?php if (empty($groupedResults['corrected'])): ?>
                <p>No corrected records in the latest batch.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Original</th>
                            <th>Final</th>
                            <th>Status</th>
                            <th>Message</th>
                            <th>What was modified</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($groupedResults['corrected'] as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['original_value'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($row['final_value'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><span class="badge corrected">CORRECTED</span></td>
                                <td><?= htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($row['modifications'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Invalid full width -->
    <div class="card" style="margin-top: 30px;">
        <h3>Invalid VAT numbers</h3>

        <?php if (empty($groupedResults['invalid'])): ?>
            <p>No invalid records in the latest batch.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Original</th>
                        <th>Status</th>
                        <th>Message</th>
                        <th>Why invalid</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($groupedResults['invalid'] as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['original_value'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><span class="badge invalid">INVALID</span></td>
                            <td><?= htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars($row['modifications'], ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>