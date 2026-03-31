<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Italian VAT Numbers Validator</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f5f5f5;
            color: #222;
        }

        .container {
            max-width: 1100px;
            margin: 30px auto;
            background: #fff;
            padding: 24px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .08);
        }

        h1, h2, h3 {
            margin-top: 0;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 16px;
            background: #fafafa;
        }

        .flash {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
        }

        .flash.success {
            background: #e9f8ef;
            color: #166534;
            border: 1px solid #b7ebc6;
        }

        .flash.error {
            background: #fdecec;
            color: #991b1b;
            border: 1px solid #f5c2c2;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f0f0f0;
        }

        input[type="text"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 12px;
            box-sizing: border-box;
        }

        button {
            background: #1f4b99;
            color: #fff;
            border: 0;
            padding: 10px 16px;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background: #163971;
        }

        .title-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .reset-btn {
            text-decoration: none;
            font-size: 20px;
            padding: 6px 10px;
            border-radius: 6px;
            color: #444;
            border: 1px solid #ddd;
            background: #fafafa;
            transition: all 0.2s ease;
        }

        .reset-btn:hover {
            background: #f0f0f0;
            color: #000;
        }
     
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge.valid {
            background: #dcfce7;
            color: #166534;
        }

        .badge.corrected {
            background: #fef9c3;
            color: #854d0e;
        }

        .badge.invalid {
            background: #fee2e2;
            color: #991b1b;
        }

        .small {
            font-size: 13px;
            color: #555;
        }
        .stats-row {
            display: flex;
            gap: 15px;
            margin: 20px 0;
        }

        .stat-card {
            flex: 1;
            text-align: center;
        }
        .card.stat-card.valid {
            background: #e9f8ef;
            border-color: #b7ebc6;
            color: #166534;
        }

        .card.stat-card.corrected {
            background: #fef9c3;
            border-color: #fde047;
            color: #854d0e;
        }

        .card.stat-card.invalid {
            background: #fdecec;
            border-color: #f5c2c2;
            color: #991b1b;
        }
        .section-divider {
            margin: 30px 0;
        }

        .section-title {
            margin-bottom: 15px;
        }
   
        @media (max-width: 800px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">