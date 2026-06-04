<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ brand('name') }} Public API — Dokümantasyon</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <style>
        body { margin: 0; padding: 0; font-family: 'Inter', system-ui, sans-serif; }
    </style>
</head>
<body>
    <script
        id="api-reference"
        data-url="{{ asset('api/openapi.yaml') }}"
        data-configuration='{"theme":"deepSpace","layout":"modern","showSidebar":true,"hideDownloadButton":false}'
    ></script>
    <script src="https://cdn.jsdelivr.net/npm/@scalar/api-reference@latest/dist/browser/standalone.min.js"></script>
</body>
</html>
