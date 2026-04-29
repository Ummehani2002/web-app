<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Items Master</title>
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
        .header { background: #fff; border: 1px solid #edebe9; border-radius: 2px; padding: 14px 16px; margin-bottom: 12px; }
        .header h1 { margin: 0; font-size: 38px; color: #0f2b56; font-weight: 700; }
        .card { background: #fff; border: 1px solid #edebe9; border-radius: 2px; padding: 14px; margin-bottom: 12px; }
        .form-row { display: grid; grid-template-columns: repeat(4, minmax(160px, 1fr)); gap: 10px; margin-bottom: 10px; }
        .form-row label { display: block; font-size: 12px; color: #605e5c; font-weight: 600; margin-bottom: 4px; }
        .form-row input, .form-row select { width: 100%; padding: 8px; border: 1px solid #8a8886; border-radius: 2px; }
        .btn { background: #106ebe; color: #fff; border: 1px solid #106ebe; padding: 8px 12px; border-radius: 2px; cursor: pointer; }
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
                <a class="menu-link" href="{{ route('modules.procurement.grn', $companyQuery) }}">GRN</a>
            </div>
        </div>
        <a class="menu-link" href="{{ route('settings.index', $companyQuery) }}">Settings</a>
    </aside>
    <main class="main">
        <div class="header">
            <h1>Items</h1>
        </div>
        <div class="card">
            <h2 style="margin-top:0;">Create / Update Item</h2>
            @if(session('status'))
                <div class="status">{{ session('status') }}</div>
            @endif
            @if($errors->any())
                <div class="error">{{ $errors->first() }}</div>
            @endif
            <form method="post" action="{{ route('masters.items.store', $companyQuery) }}">
                @csrf
                <div class="form-row">
                    <div>
                        <label for="item_id">Item ID</label>
                        <input id="item_id" name="item_id" value="{{ old('item_id') }}" required maxlength="100">
                    </div>
                    <div>
                        <label for="item_name">Item Name</label>
                        <input id="item_name" name="item_name" value="{{ old('item_name') }}" required maxlength="255">
                    </div>
                    <div>
                        <label for="type">Type</label>
                        <input id="type" name="type" value="{{ old('type') }}" maxlength="50">
                    </div>
                    <div>
                        <label for="item_category_id">Item Category</label>
                        <select id="item_category_id" name="item_category_id">
                            <option value="">— Select Category —</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->name }}" {{ old('item_category_id') === $category->name ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button class="btn" type="submit">Save Item</button>
            </form>
        </div>
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item ID</th>
                        <th>Item Name</th>
                        <th>Type</th>
                        <th>Item Category</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $idx => $item)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>{{ $item->item_id ?: $item->d365_id ?: $item->d365_item_id }}</td>
                            <td>{{ $item->item_name }}</td>
                            <td>{{ $item->type ?: '—' }}</td>
                            <td>{{ $item->item_category_id ?: '—' }}</td>
                            <td>{{ optional($item->created_at)->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty">No items synced yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
