<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quotations</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Segoe UI', Arial, sans-serif; background: #f3f2f1; color: #323130; display: flex; min-height: 100vh; }
        .main { flex: 1; padding: 12px 16px; overflow: auto; }
        .page-shell { border: 1px solid #edebe9; background: #fff; border-radius: 2px; overflow: hidden; }
        .command-bar { height: 44px; border-bottom: 1px solid #edebe9; background: #fff; display: flex; align-items: center; padding: 0 12px; }
        .crumb { font-size: 12px; color: #605e5c; }
        .title { margin: 0 0 8px; font-size: 24px; font-weight: 600; }
        .card { background: #fff; border: 1px solid #edebe9; border-radius: 2px; padding: 24px; }
        .empty-note { color: #8a8886; font-size: 14px; }
    </style>
    @include('settings.rbac.partials.styles')
</head>
<body>
    @include('partials.global-company-selector')
    @php
        $companyCode = strtoupper((string) ($currentCompanyCode ?? $globalSelectedCompany ?? request()->query('company', '')));
        $companyQuery = $companyCode !== '' ? ['company' => $companyCode] : [];
    @endphp
    @include('settings.rbac.partials.sidebar')

    <main class="main">
        <div class="page-shell">
            <div class="command-bar">
                <div class="crumb">Modules / Project Management / Quotations</div>
            </div>
            <div style="padding:12px;">
                <h1 class="title">Quotations</h1>
                <div class="card">
                    <p class="empty-note">Quotation module is coming soon.</p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
