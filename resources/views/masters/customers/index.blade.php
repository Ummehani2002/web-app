<!DOCTYPE html>
<html>
<head>
    <title>Customer Master</title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            margin: 0;
            background: #f3f2f1;
            color: #323130;
            min-height: 100vh;
        }
        .main { padding: 12px 16px; overflow: auto; }
        .page-shell { border: 1px solid #edebe9; background: #fff; border-radius: 2px; overflow: hidden; }
        .command-bar { height: 44px; border-bottom: 1px solid #edebe9; background: #fff; display: flex; align-items: center; justify-content: space-between; padding: 0 12px; }
        .crumb { font-size: 12px; color: #605e5c; }
        .title { margin: 0 0 12px; font-size: 24px; font-weight: 600; }
        .card { background: white; border-radius: 2px; border: 1px solid #edebe9; overflow: hidden; }
        .card-head { padding: 12px 14px; border-bottom: 1px solid #edebe9; font-size: 20px; font-weight: 600; }
        .section-head {
            font-size: 14px;
            font-weight: 600;
            color: #605e5c;
            margin: 16px 0 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #edebe9;
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            margin-bottom: 12px;
        }
        label { display: block; font-size: 14px; margin-bottom: 4px; font-weight: 600; }
        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #8a8886;
            border-radius: 2px;
            box-sizing: border-box;
            font-size: 14px;
        }
        .btn-primary {
            background: #005a9e;
            color: white;
            border: 1px solid #005a9e;
            padding: 8px 12px;
            border-radius: 2px;
            cursor: pointer;
            font-size: 13px;
        }
        .btn-delete {
            background: #a4262c;
            color: white;
            border: 1px solid #a4262c;
            padding: 6px 10px;
            border-radius: 2px;
            cursor: pointer;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        th, td {
            text-align: left;
            border-bottom: 1px solid #edebe9;
            padding: 10px 8px;
            vertical-align: top;
        }
        th {
            color: #605e5c;
            background: #faf9f8;
            font-weight: 600;
        }
        .status {
            background: #e8f6ee;
            color: #1f7a48;
            padding: 10px;
            border-radius: 2px;
            margin-bottom: 12px;
        }
        .errors {
            background: #fde7e9;
            color: #a4262c;
            padding: 10px;
            border-radius: 2px;
            margin-bottom: 12px;
        }
        .back-link { text-decoration: none; display: inline-block; margin-top: 12px; font-size: 13px; }
        .address-cell { font-size: 12px; color: #605e5c; line-height: 1.4; }
        .table-wrap { overflow-x: auto; }
    </style>
    @include('settings.rbac.partials.styles')
</head>
<body>
    @include('partials.global-company-selector')
    @php
        $companyCode = strtoupper((string) request()->query('company', ''));
        $companyQuery = $companyCode !== '' ? ['company' => $companyCode] : [];
    @endphp
    @include('settings.rbac.partials.sidebar')
    <main class="main">
        <div class="page-shell">
            <div class="command-bar">
                <div class="crumb">Masters / Customers</div>
            </div>
            <div style="padding:12px;">
                <h1 class="title">Customer Master</h1>

                @if (session('status'))
                    <div class="status">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="errors">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="card" style="margin-bottom: 12px;">
                    <div class="card-head">Create Customer</div>
                    <div style="padding: 12px;">
                        <form method="post" action="{{ route('masters.customers.store', $companyQuery) }}">
                            @csrf

                            <div class="section-head">General</div>
                            <div class="form-row">
                                <div>
                                    <label for="company_id">Company ID (D365)</label>
                                    <input id="company_id" name="company_id" type="text" value="{{ old('company_id', $companyCode !== '' ? $companyCode : '') }}" required maxlength="100" placeholder="e.g. PS">
                                </div>
                                <div>
                                    <label for="type">Type</label>
                                    <select id="type" name="type" required>
                                        <option value="">Select type</option>
                                        @foreach ($types as $typeOption)
                                            <option value="{{ $typeOption }}" @selected(old('type') === $typeOption)>{{ $typeOption }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="customer_id">ID</label>
                                    <input id="customer_id" name="customer_id" type="text" value="{{ old('customer_id') }}" required maxlength="100">
                                </div>
                                <div>
                                    <label for="name">Name</label>
                                    <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="255">
                                </div>
                            </div>

                            <div class="section-head">Address</div>
                            <div class="form-row">
                                <div>
                                    <label for="address_flat">Flat #</label>
                                    <input id="address_flat" name="address_flat" type="text" value="{{ old('address_flat') }}" maxlength="100">
                                </div>
                                <div>
                                    <label for="address_building">Building #</label>
                                    <input id="address_building" name="address_building" type="text" value="{{ old('address_building') }}" maxlength="100">
                                </div>
                                <div>
                                    <label for="address_area">Area/Street</label>
                                    <input id="address_area" name="address_area" type="text" value="{{ old('address_area') }}" maxlength="255">
                                </div>
                                <div>
                                    <label for="address_city">City</label>
                                    <input id="address_city" name="address_city" type="text" value="{{ old('address_city') }}" maxlength="100">
                                </div>
                                <div>
                                    <label for="address_emirates">Emirates</label>
                                    <select id="address_emirates" name="address_emirates">
                                        <option value="">Select emirate</option>
                                        @foreach ($emirates as $emirateOption)
                                            <option value="{{ $emirateOption }}" @selected(old('address_emirates') === $emirateOption)>{{ $emirateOption }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="address_pincode">Pincode</label>
                                    <input id="address_pincode" name="address_pincode" type="text" value="{{ old('address_pincode') }}" maxlength="20">
                                </div>
                            </div>

                            <div class="section-head">Contact</div>
                            <div class="form-row">
                                <div>
                                    <label for="phone">Phone</label>
                                    <input id="phone" name="phone" type="text" value="{{ old('phone') }}" maxlength="50">
                                </div>
                                <div>
                                    <label for="email">Email</label>
                                    <input id="email" name="email" type="email" value="{{ old('email') }}" maxlength="255">
                                </div>
                            </div>

                            <button class="btn-primary" type="submit">Save Customer</button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-head">Customers</div>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Company</th>
                                    <th>Type</th>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Address</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($customers as $index => $customer)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $customer->company_id }}</td>
                                        <td>{{ $customer->type }}</td>
                                        <td>{{ $customer->customer_id }}</td>
                                        <td>{{ $customer->name }}</td>
                                        <td class="address-cell">
                                            @if ($customer->address_flat)
                                                <div>Flat: {{ $customer->address_flat }}</div>
                                            @endif
                                            @if ($customer->address_building)
                                                <div>Bldg: {{ $customer->address_building }}</div>
                                            @endif
                                            @if ($customer->address_area)
                                                <div>{{ $customer->address_area }}</div>
                                            @endif
                                            @if ($customer->address_city || $customer->address_emirates)
                                                <div>{{ trim($customer->address_city . ($customer->address_city && $customer->address_emirates ? ', ' : '') . $customer->address_emirates) }}</div>
                                            @endif
                                            @if ($customer->address_pincode)
                                                <div>Pin: {{ $customer->address_pincode }}</div>
                                            @endif
                                            @if (!$customer->address_flat && !$customer->address_building && !$customer->address_area && !$customer->address_city && !$customer->address_emirates && !$customer->address_pincode)
                                                —
                                            @endif
                                        </td>
                                        <td>{{ $customer->phone ?: '—' }}</td>
                                        <td>{{ $customer->email ?: '—' }}</td>
                                        <td>{{ optional($customer->created_at)->format('d M Y H:i') }}</td>
                                        <td>
                                            <form method="post" action="{{ route('masters.customers.destroy', array_merge($companyQuery, ['customer' => $customer->id])) }}" onsubmit="return confirm('Delete this customer?');" style="margin:0;">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn-delete" type="submit">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10">No customers found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div style="padding: 0 14px 12px;">
                        <a class="back-link" href="{{ route('dashboard', $companyQuery) }}">Back to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
