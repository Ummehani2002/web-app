<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Pools</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; background: #f3f2f1; color: #323130; }
        .card {
            max-width: 640px;
            margin: 40px auto;
            padding: 24px 28px;
            background: #fff;
            border: 1px solid #edebe9;
            border-radius: 4px;
        }
        h1 { margin: 0 0 10px; font-size: 1.25rem; }
        p { margin: 0 0 12px; line-height: 1.5; color: #605e5c; font-size: 14px; }
        a { color: #0078d4; text-decoration: none; }
        a:hover { text-decoration: underline; }
        code { font-size: 13px; background: #f3f2f1; padding: 2px 6px; border-radius: 2px; }
    </style>
</head>
<body>
    @include('partials.global-company-selector')
    <div class="card">
        <h1>Pools</h1>
        <p>
            Pool maintenance is not available in the web UI. Use the API (for example <code>GET /api/pools</code> and <code>POST /api/pools</code>) with your configured bearer token, or manage data directly in the database.
        </p>
        <p><a href="{{ route('dashboard') }}">Back to Dashboard</a></p>
    </div>
</body>
</html>
