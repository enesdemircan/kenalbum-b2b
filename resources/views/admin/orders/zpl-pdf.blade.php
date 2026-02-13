<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZPL Barcode</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            margin: 0;
            padding: 20px;
            background: #fff;
            font-size: 12px;
            line-height: 1.2;
        }
        .zpl-code {
            white-space: pre-wrap;
            word-wrap: break-word;
            color: #000;
            background: #fff;
            border: none;
            outline: none;
            width: 100%;
            min-height: 100vh;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.2;
        }
    </style>
</head>
<body>
    <pre class="zpl-code">{{ $zplData }}</pre>
</body>
</html> 