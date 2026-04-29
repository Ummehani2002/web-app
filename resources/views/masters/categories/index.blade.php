<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Item Categories Master</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: "Segoe UI", Arial, sans-serif; background: #f3f2f1; color: #323130; display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: #fff; border-right: 1px solid #edebe9; padding: 12px 0; flex-shrink: 0; }
        .logo { padding: 10px 16px 18px; border-bottom: 1px solid #edebe9; margin-bottom: 8px; font-weight: 700; }
        .label { padding: 10px 16px 4px; color: #8a8886; font-size: 11px; text-transform: uppercase; }
        .menu-link { display: block; padding: 10px 16px; color: #323130; text-decoration: none; border-radius: 8px; margin: 2px 8px; font-size: 14px; }
        .menu-link:hover { background: #f3f2f1; }
        .menu-link.active { background: #deecf9; color: #005a9e; }
        .sub { margin-left: 16px; padding-left: 8px; border-left: 2px solid #edebe9; }
        .main { flex: 1; padding: 16px; overflow: auto; }
        .header, .card { background: #fff; border: 1px solid #edebe9; border-radius: 2px; padding: 14px; margin-bottom: 12px; }
        .header h1 { margin: 0; font-size: 38px; color: #0f2b56; font-weight: 700; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr auto; gap: 10px; margin-bottom: 8px; max-width: 800px; }
        .form-row label { display: block; font-size: 12px; color: #605e5c; font-weight: 600; margin-bottom: 4px; }
        .form-row input, .form-row select { width: 100%; padding: 8px; border: 1px solid #8a8886; border-radius: 2px; }
        .btn { background: #106ebe; color: #fff; border: 1px solid #106ebe; padding: 8px 12px; border-radius: 2px; cursor: pointer; align-self: end; }
        .status { background: #e8f6ee; color: #1f7a48; padding: 10px; border-radius: 2px; margin-bottom: 10px; }
        .error { background: #fde7e9; color: #a4262c; padding: 10px; border-radius: 2px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; border-bottom: 1px solid #edebe9; padding: 10px 8px; }
        th { color: #605e5c; background: #faf9f8; font-weight: 600; }
        .empty { text-align: center; color: #8a8886; padding: 24px 8px; }
    </style>
</head>
<body>
    @include('partials.global-company-selector')
    @php
        $companyQuery = !empty($currentCompanyCode) ? ['company' => strtoupper((string) $currentCompanyCode)] : [];
    @endphp
    <aside class="sidebar">
        <div class="logo">Logo</div>
        <div class="label">Menu</div>
        <a class="menu-link" href="{{ route('dashboard', $companyQuery) }}">Dashboard</a>
        <a class="menu-link active" href="{{ route('masters.company.index', $companyQuery) }}">Masters</a>
        <a class="menu-link" href="#">Modules</a>
        <div class="sub">
            <a class="menu-link" href="#">Project Management</a>
            <a class="menu-link" href="{{ route('modules.project-management.item-issue', $companyQuery) }}">Item Issue</a>
            <a class="menu-link" href="#">Procurement &amp; Sourcing</a>
            <div class="sub">
                <a class="menu-link" href="{{ route('modules.procurement.purch-req', $companyQuery) }}">Purchase Requisition</a>
                <a class="menu-link" href="{{ route('grns.index', $companyQuery) }}">GRN</a>
            </div>
        </div>
        <a class="menu-link" href="{{ route('settings.index', $companyQuery) }}">Settings</a>
    </aside>
    <main class="main">
        <div class="header">
            <h1>Item Categories</h1>
        </div>
        <div class="card">
            <h2 style="margin-top:0;">Create Category</h2>
            @if(session('status'))
                <div class="status">{{ session('status') }}</div>
            @endif
            @if($errors->any())
                <div class="error">{{ $errors->first() }}</div>
            @endif
            <form method="post" action="{{ route('masters.categories.store', $companyQuery) }}">
                @csrf
                <div class="form-row">
                    <div>
                        <label for="item_category_id">Item Category ID</label>
                        <input id="item_category_id" name="item_category_id" value="{{ old('item_category_id') }}" required maxlength="100" placeholder="e.g. CAT001">
                    </div>
                    <div>
                        <label for="name">Category Name</label>
                        <input id="name" name="name" value="{{ old('name') }}" required maxlength="255" placeholder="e.g. Building Materials">
                    </div>
                    <button class="btn" type="submit">Save Category</button>
                </div>
            </form>
        </div>
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item Category ID</th>
                        <th>Category Name</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $idx => $category)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>{{ $category->item_category_id ?: $category->d365_id ?: '—' }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ optional($category->created_at)->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty">No categories yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
