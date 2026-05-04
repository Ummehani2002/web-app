<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Settings - Roles & Permissions</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f3f2f1;
            color: #323130;
            display: flex;
            min-height: 100vh;
        }

        .sidebar { width: 260px; min-height: 100vh; background: #fff; border-right: 1px solid #edebe9; padding: 16px 0; flex-shrink: 0; }
        .sidebar-brand { padding: 10px 16px 18px; border-bottom: 1px solid #edebe9; margin-bottom: 8px; font-weight: 700; font-size: 15px; }
        .nav-section-label { padding: 10px 16px 4px; color: #8a8886; font-size: 11px; text-transform: uppercase; letter-spacing: .5px; }
        .nav-link { display: block; padding: 9px 16px; color: #323130; text-decoration: none; font-size: 14px; border-radius: 2px; margin: 1px 8px; }
        .nav-link:hover { background: #f3f2f1; }
        .nav-link.active { background: #deecf9; color: #005a9e; font-weight: 500; }
        .nav-sub { margin-left: 16px; border-left: 2px solid #edebe9; padding-left: 4px; }
        .nav-sub .nav-link { font-size: 13px; padding: 7px 12px; }
        .sidebar-footer { padding: 16px; border-top: 1px solid #edebe9; margin-top: 8px; }
        .btn-logout { background: transparent; color: #605e5c; border: 1px solid #8a8886; padding: 7px 14px; border-radius: 2px; cursor: pointer; font-size: 13px; font-family: inherit; width: 100%; }
        .btn-logout:hover { background: #f3f2f1; }

        .main { flex: 1; padding: 32px 40px; overflow: auto; }
        .page-title { margin: 0 0 6px; font-size: 22px; font-weight: 600; color: #201f1e; }
        .page-subtitle { margin: 0 0 24px; font-size: 13px; color: #8a8886; }

        .card {
            background: #fff;
            border: 1px solid #edebe9;
            border-radius: 4px;
            max-width: 900px;
            margin-bottom: 20px;
        }
        .card h3 { margin: 0 0 8px; font-size: 16px; color: #201f1e; }
        .card p, .help { margin: 0; color: #605e5c; font-size: 14px; line-height: 1.5; }
        .card-body { padding: 20px 24px; }
        .card-header { padding: 18px 24px; border-bottom: 1px solid #edebe9; }
        .card-header h3 { margin: 0; font-size: 15px; }

        .alert { padding: 10px 14px; border-radius: 2px; font-size: 13px; margin-bottom: 16px; }
        .alert-success { background: #dff6dd; color: #107c10; border: 1px solid #c0e0c0; }
        .alert-warn { background: #fff4ce; color: #8a6d3b; border: 1px solid #e0d0a0; }
        .alert-info { background: #deecf9; color: #005a9e; border: 1px solid #a6c7e8; }

        .data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .data-table th { text-align: left; padding: 10px 12px; border-bottom: 1px solid #edebe9; color: #605e5c; font-weight: 600; }
        .data-table td { padding: 10px 12px; border-bottom: 1px solid #f3f2f1; vertical-align: middle; }
        .data-table tr:last-child td { border-bottom: none; }

        .field { margin-bottom: 14px; }
        .field label { display: block; font-size: 12px; font-weight: 600; color: #323130; margin-bottom: 5px; }
        .field input, .field select {
            width: 100%; max-width: 360px;
            border: 1px solid #8a8886; border-radius: 2px; padding: 8px 10px; font-size: 13px;
        }
        .field .hint { font-size: 12px; color: #8a8886; margin-top: 4px; }

        .form-row { display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end; }
        .form-row .field { margin-bottom: 0; flex: 1; min-width: 180px; }

        .btn {
            padding: 8px 18px; border-radius: 2px; font-size: 13px;
            font-family: inherit; cursor: pointer; border: 1px solid transparent; font-weight: 500;
        }
        .btn-primary { background: #0078d4; color: #fff; border-color: #0078d4; }
        .btn-primary:hover { background: #106ebe; }
        .btn-primary:disabled { background: #c8c6c4; border-color: #c8c6c4; cursor: not-allowed; }

        .role-perm-list { margin: 6px 0 0; padding-left: 18px; color: #605e5c; font-size: 13px; }
        .muted { color: #8a8886; font-size: 12px; }
    </style>
</head>
<body>
    @include('partials.global-company-selector')

<aside class="sidebar">
    <div class="sidebar-brand">TI Web App</div>
    <nav>
        <div class="nav-section-label">Menu</div>
        <a class="nav-link" href="{{ route('dashboard', $companyQuery ?? []) }}">Dashboard</a>
        <a class="nav-link" href="{{ route('masters.company.index', $companyQuery ?? []) }}">Masters</a>
        <a class="nav-link" href="{{ route('modules.project-management.item-issue', $companyQuery ?? []) }}">Modules</a>

        <div class="nav-section-label" style="margin-top:8px;">Settings</div>
        <div class="nav-sub">
            <a class="nav-link" href="{{ route('settings.token', $companyQuery ?? []) }}">API Token Timer</a>
            <a class="nav-link" href="{{ route('settings.credentials', $companyQuery ?? []) }}">D365 Credentials</a>
            <a class="nav-link active" href="{{ route('settings.roles-permissions', $companyQuery ?? []) }}">Roles & Permissions</a>
        </div>
    </nav>
    <div class="sidebar-footer">
        <form method="post" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">Log out</button>
        </form>
    </div>
</aside>

<main class="main">
    <h1 class="page-title">Users &amp; roles</h1>
    <p class="page-subtitle">Use the company selector (top right) first. Then create logins if needed, and assign <strong>Admin</strong>, <strong>User</strong>, or <strong>Store keeper</strong> for this company.</p>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if (!($rbacReady ?? false))
        <div class="card">
            <div class="card-body">
                <h3>Setup required</h3>
                <p>Run <code>php artisan migrate</code> and <code>php artisan db:seed --class=PermissionSeeder</code> to enable roles and permissions.</p>
            </div>
        </div>
    @elseif (!($hasCompany ?? false))
        <div class="card">
            <div class="card-body">
                <h3>No company</h3>
                <p>Create a company under Masters first, then return here to assign users.</p>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            Working in company <strong>{{ strtoupper((string) $selectedCompany->d365_id) }}</strong> — {{ $selectedCompany->name }}
        </div>

        @if (!($canManage ?? false))
            <div class="alert alert-warn">
                You do not have permission to change users for this company.
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3>Step 1 — Create or update a login</h3>
            </div>
            <div class="card-body">
                <p class="help" style="margin-bottom:14px;">Use this when the person is not in the system yet, or you want to set their password. If the email already exists, only name (and optional password) are updated.</p>
                <form method="post" action="{{ route('settings.roles-permissions.user-account.store') }}">
                    @csrf
                    <input type="hidden" name="company" value="{{ strtoupper((string) $selectedCompany->d365_id) }}">
                    <div class="form-row">
                        <div class="field">
                            <label for="su-name">Full name</label>
                            <input id="su-name" name="name" type="text" required value="{{ old('name') }}" autocomplete="name">
                        </div>
                        <div class="field">
                            <label for="su-email">Email (login)</label>
                            <input id="su-email" name="email" type="email" required value="{{ old('email') }}" autocomplete="email">
                        </div>
                        <div class="field">
                            <label for="su-pass">Password <span class="muted">(optional)</span></label>
                            <input id="su-pass" name="password" type="password" minlength="8" autocomplete="new-password" placeholder="Leave blank to auto-generate">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save user</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>Step 2 — Assign a role in this company</h3>
            </div>
            <div class="card-body">
                @if ($canManage)
                    <p class="help" style="margin-bottom:14px;">Pick someone who already has an account, then choose <strong>Admin</strong>, <strong>User</strong>, or <strong>Store keeper</strong>. One role per user per company (assigning again replaces the previous role).</p>
                    <form method="post" action="{{ route('settings.roles-permissions.assign') }}">
                        @csrf
                        <input type="hidden" name="company_id" value="{{ $selectedCompany->id }}">
                        <div class="form-row">
                            <div class="field">
                                <label for="as-user">User</label>
                                <select id="as-user" name="user_id" required>
                                    <option value="" disabled selected>Select user…</option>
                                    @foreach ($allUsers as $u)
                                        <option value="{{ $u->id }}" @selected((string) old('user_id') === (string) $u->id)>{{ $u->name }} — {{ $u->email }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label for="as-role">Role</label>
                                <select id="as-role" name="role_id" required>
                                    <option value="" disabled selected>Select role…</option>
                                    @foreach ($presetRoles as $r)
                                        <option value="{{ $r->id }}" @selected((string) old('role_id') === (string) $r->id)>{{ $r->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if ($errors->any())
                            <div class="alert alert-warn" style="margin-bottom:12px;">{{ $errors->first() }}</div>
                        @endif
                        <button type="submit" class="btn btn-primary">Assign role</button>
                    </form>
                @else
                    <p class="muted">You cannot assign roles for this company.</p>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>People in this company</h3>
            </div>
            <div class="card-body">
                @if ($memberships->isEmpty())
                    <p class="help">Nobody linked to this company yet. Use Step 2 after users exist.</p>
                @else
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($memberships as $m)
                                <tr>
                                    <td>{{ $m->user->name }}</td>
                                    <td>{{ $m->user->email }}</td>
                                    <td>
                                        @if ($canManage)
                                            <form method="post" action="{{ route('settings.roles-permissions.members.update', $m) }}" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                                                @csrf
                                                @method('PATCH')
                                                <select name="role_id" onchange="this.form.submit()" style="min-width:220px;">
                                                    @foreach ($roleTableOptions ?? $presetRoles as $r)
                                                        <option value="{{ $r->id }}" @selected($m->roles->contains('id', $r->id))>{{ $r->name }}</option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        @else
                                            {{ $m->roles->pluck('name')->first() ?? '—' }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>What each role can do</h3>
            </div>
            <div class="card-body">
                <p class="help"><strong>Admin</strong> — full access for this company (including inviting users and all modules).</p>
                <p class="help"><strong>User</strong> — Dashboard and typical modules (e.g. Item Issue and general module screens).</p>
                <p class="help"><strong>Store keeper</strong> — Purchase Requisition and Goods Receive Note (GRN) only, plus choosing the company.</p>
            </div>
        </div>
    @endif
</main>
</body>
</html>
