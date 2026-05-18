<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('settings.rbac.partials.styles')
    <style>
        body.dashboard-page {
            background: #eef1f6;
        }
        .dashboard-page .main {
            flex: 1;
            padding: 24px 28px 32px;
            overflow: auto;
            min-height: 100vh;
            background: #eef1f6;
            position: relative;
        }
        .dashboard-page .main::before {
            display: none;
        }
        .dashboard-shell {
            max-width: 1280px;
            margin: 0 auto;
        }
        .main-header {
            margin-bottom: 20px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
        }
        .main-header h2 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #0f172a !important;
        }
        .main-subtitle {
            margin-top: 4px;
            color: #64748b !important;
            font-size: 14px;
        }
        .dashboard-panels {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(300px, 320px);
            gap: 20px;
            align-items: stretch;
        }
        .company-hero {
            margin-top: 0;
            min-height: 0;
            border-radius: 12px;
            border: 1px solid #d8dee9;
            overflow: hidden;
            position: relative;
            background: #fff;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06), 0 8px 24px rgba(15, 23, 42, 0.06);
        }
        .company-hero.company-hero--custom-bg {
            background: #fff;
        }
        .company-hero-bg {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            z-index: 0;
            user-select: none;
            opacity: 0.14;
        }
        .company-hero-bg-scrim {
            position: absolute;
            inset: 0;
            z-index: 1;
            pointer-events: none;
            background: linear-gradient(105deg, rgba(255, 255, 255, 0.97) 0%, rgba(248, 250, 252, 0.94) 55%, rgba(241, 245, 249, 0.9) 100%);
        }
        .company-hero::after {
            display: none;
        }
        .company-hero-content {
            position: relative;
            z-index: 2;
            padding: 28px 32px;
            display: grid;
            grid-template-columns: 1fr 300px;
            align-items: center;
            gap: 28px;
            min-height: 380px;
        }
        .company-hero-top {
            max-width: 560px;
            border-left: 4px solid #0f6cbd;
            padding-left: 20px;
        }
        .company-hero-title {
            margin: 0;
            color: #0f172a !important;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: -0.02em;
            text-transform: none;
            text-shadow: none;
            line-height: 1.15;
        }
        .company-hero-meta {
            margin-top: 10px;
            color: #475569 !important;
            font-size: 15px;
        }
        .company-hero-meta strong {
            color: #0f172a;
            font-weight: 600;
        }
        .company-hero-chip {
            margin-top: 16px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            color: #0d5c2e;
            background: #e8f6ee;
            border: 1px solid #b7e4c7;
        }
        .company-hero-chip::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #107c10;
        }
        .company-hero-brand {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .company-hero-brand img {
            width: 100%;
            height: 150px;
            object-fit: contain;
            display: block;
            border-radius: 8px;
            background: #fff;
            padding: 10px;
            border: 1px solid #e2e8f0;
        }
        .company-hero-brand-placeholder {
            min-height: 120px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 14px 10px;
            text-align: center;
            font-size: 12px;
            line-height: 1.45;
            color: #64748b;
            border-radius: 8px;
            background: #fff;
            border: 1px dashed #cbd5e1;
        }
        .company-hero-brand-placeholder strong {
            color: #334155;
            font-size: 11px;
            word-break: break-all;
        }
        .company-hero-brand-hint {
            display: block;
            font-size: 11px;
            color: #94a3b8;
        }
        .company-hero-user {
            width: 100%;
            padding: 16px 18px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }
        .company-hero-user h3 {
            margin: 0 0 12px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #64748b !important;
        }
        .company-hero-user p {
            margin: 0 0 10px;
            font-size: 14px;
            line-height: 1.45;
            color: #334155 !important;
        }
        .company-hero-user p:last-child {
            margin-bottom: 0;
        }
        .company-hero-user p strong {
            display: block;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #94a3b8;
            margin-bottom: 2px;
        }
        .company-hero-right {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 14px;
            width: 100%;
        }
        .dashboard-calendar {
            border-radius: 12px;
            border: 1px solid #d8dee9;
            background: #fff;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06), 0 8px 24px rgba(15, 23, 42, 0.06);
            padding: 18px 16px 16px;
            color: #0f172a;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .dashboard-calendar-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 14px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .dashboard-calendar-head h3,
        .dashboard-calendar #cal-month-label {
            margin: 0;
            font-size: 17px;
            font-weight: 700;
            letter-spacing: -0.01em;
            text-align: center;
            flex: 1;
            color: #0f172a !important;
            text-shadow: none;
        }
        .calendar-nav-btn {
            width: 34px;
            height: 34px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #fff;
            color: #334155;
            font-size: 18px;
            line-height: 1;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background .15s, border-color .15s;
        }
        .calendar-nav-btn:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
            color: #0f172a;
        }
        .calendar-weekdays,
        .calendar-days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
        }
        .calendar-weekdays {
            margin-bottom: 8px;
        }
        .calendar-weekdays span {
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            padding: 2px 0;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .calendar-day {
            aspect-ratio: 1;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: #1e293b;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            font-family: inherit;
        }
        .calendar-day:hover:not(:disabled) {
            background: #e8f4fc;
            color: #0f6cbd;
        }
        .calendar-day.is-outside {
            color: #94a3b8;
        }
        .calendar-day.is-today {
            background: #0f6cbd;
            color: #fff;
            font-weight: 700;
        }
        .calendar-day.is-selected:not(.is-today) {
            background: #deecf9;
            color: #005a9e;
            box-shadow: inset 0 0 0 1px #0f6cbd;
        }
        .calendar-day:disabled {
            cursor: default;
            visibility: hidden;
        }
        .calendar-footer {
            margin-top: auto;
            padding-top: 12px;
            display: flex;
            justify-content: center;
        }
        .calendar-today-btn {
            border: 1px solid #0f6cbd;
            border-radius: 8px;
            background: #fff;
            color: #0f6cbd;
            font-size: 12px;
            font-weight: 600;
            padding: 7px 16px;
            cursor: pointer;
            font-family: inherit;
            transition: background .15s, color .15s;
        }
        .calendar-today-btn:hover {
            background: #0f6cbd;
            color: #fff;
        }
        .dashboard-warning {
            max-width: 520px;
            padding: 12px 16px;
            margin-bottom: 16px;
            background: #fff8e6;
            border: 1px solid #e8d9a8;
            border-radius: 8px;
            font-size: 13px;
            color: #7a5c00;
        }
        @media (max-width: 1100px) {
            .dashboard-panels {
                grid-template-columns: 1fr;
            }
            .dashboard-calendar {
                max-width: 360px;
            }
        }
        @media (max-width: 860px) {
            .company-hero-content {
                grid-template-columns: 1fr;
                align-items: start;
                min-height: 0;
                padding: 22px 20px;
            }
            .company-hero-title {
                font-size: 26px;
            }
            .company-hero-right {
                max-width: none;
            }
            .dashboard-calendar {
                max-width: none;
            }
        }
    </style>
</head>
<body class="dashboard-page">
    @include('partials.global-company-selector')
    @php
        $companyCode = strtoupper((string) ($currentCompanyCode ?? $globalSelectedCompany ?? request()->query('company', '')));
        $companyQuery = $companyCode !== '' ? ['company' => $companyCode] : [];
    @endphp
    @php
        $authCanAccessMasters = $authCanAccessMasters ?? ($authShowMastersSettingsNav ?? false);
        $authIsSuperAdmin = $authIsSuperAdmin ?? ($authShowMastersSettingsNav ?? false);
    @endphp
    @include('settings.rbac.partials.sidebar')

    <main class="main">
        <div class="dashboard-shell">
        <div class="main-header">
            <div>
                <h2>Dashboard</h2>
                <div class="main-subtitle">Company workspace overview</div>
            </div>
        </div>

        @if (session('warning'))
            <div class="dashboard-warning">
                {{ session('warning') }}
            </div>
        @endif

        @php
            $selectedCompany = collect($companies ?? [])->first(function ($company) use ($companyCode) {
                return strtoupper((string) ($company->d365_id ?? '')) === $companyCode;
            });
            $selectedCompanyName = trim((string) ($selectedCompany->name ?? 'PROSCAPE LLC'));
            if ($selectedCompanyName === '') {
                $selectedCompanyName = 'PROSCAPE LLC';
            }
            $companyLogoUrl = null;
            $companyHeroBgUrl = null;
            if ($companyCode !== '') {
                $logoStem = $companyCode;
                $logoAliases = config('company_logos.logo_stem_aliases', []);
                if (is_array($logoAliases) && isset($logoAliases[$companyCode])) {
                    $logoStem = strtoupper((string) $logoAliases[$companyCode]);
                }
                foreach (['png', 'jpg', 'jpeg', 'webp', 'svg', 'gif'] as $ext) {
                    $relPath = 'images/companies/' . $logoStem . '.' . $ext;
                    $absPath = public_path($relPath);
                    if (file_exists($absPath)) {
                        $companyLogoUrl = asset($relPath) . '?v=' . (string) filemtime($absPath);
                        break;
                    }
                }
                foreach (['gif', 'webp', 'png', 'jpg', 'jpeg'] as $ext) {
                    $heroPath = 'images/companies/' . $companyCode . '_bg.' . $ext;
                    $heroAbs = public_path($heroPath);
                    if (file_exists($heroAbs)) {
                        $companyHeroBgUrl = asset($heroPath) . '?v=' . (string) filemtime($heroAbs);
                        break;
                    }
                }
            }
        @endphp

        <div class="dashboard-panels">
        <section class="company-hero{{ $companyHeroBgUrl ? ' company-hero--custom-bg' : '' }}" aria-label="Selected company dashboard hero">
            @if ($companyHeroBgUrl)
                <img class="company-hero-bg" src="{{ $companyHeroBgUrl }}" alt="{{ $companyCode }} workspace background">
                <div class="company-hero-bg-scrim" aria-hidden="true"></div>
            @endif
            <div class="company-hero-content">
                <div class="company-hero-top">
                    <h3 class="company-hero-title">{{ $selectedCompanyName }}</h3>
                    <div class="company-hero-meta">
                        Company ID: <strong>{{ $companyCode !== '' ? $companyCode : '—' }}</strong>
                    </div>
                    <div class="company-hero-chip">Active Workspace</div>
                </div>

                <div class="company-hero-right">
                    <div class="company-hero-brand">
                        @if ($companyLogoUrl)
                            <img
                                src="{{ $companyLogoUrl }}"
                                alt="{{ $companyCode }} logo"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"
                            >
                        @endif
                        <div class="company-hero-brand-placeholder" style="{{ $companyLogoUrl ? 'display:none;' : '' }}">
                            Each company uses its own logo file (not shared with others).
                            <strong>public/images/companies/{{ $companyCode ?: 'CODE' }}.png</strong>
                            <span class="company-hero-brand-hint">Same pattern for other IDs, e.g. ML.jpg, TS.webp. Optional hero: {{ $companyCode ?: 'CODE' }}_bg.gif</span>
                        </div>
                    </div>

                    <div class="company-hero-user">
                        <h3>User Details</h3>
                        <p><strong>Name</strong>{{ auth()->user()->name }}</p>
                        <p><strong>Email</strong>{{ auth()->user()->email }}</p>
                        <p><strong>User ID</strong>{{ auth()->user()->id }}</p>
                    </div>
                </div>
            </div>
        </section>

        <aside class="dashboard-calendar" aria-label="Calendar">
            <div class="dashboard-calendar-head">
                <button type="button" class="calendar-nav-btn" id="cal-prev" aria-label="Previous month">&#8249;</button>
                <h3 id="cal-month-label"></h3>
                <button type="button" class="calendar-nav-btn" id="cal-next" aria-label="Next month">&#8250;</button>
            </div>
            <div class="calendar-weekdays" aria-hidden="true">
                <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
            </div>
            <div class="calendar-days" id="cal-days" role="grid" aria-labelledby="cal-month-label"></div>
            <div class="calendar-footer">
                <button type="button" class="calendar-today-btn" id="cal-today">Today</button>
            </div>
        </aside>
        </div>
        </div>
    </main>

    <script>
    (() => {
        const monthLabel = document.getElementById('cal-month-label');
        const daysEl = document.getElementById('cal-days');
        const prevBtn = document.getElementById('cal-prev');
        const nextBtn = document.getElementById('cal-next');
        const todayBtn = document.getElementById('cal-today');
        if (!monthLabel || !daysEl) return;

        const today = new Date();
        let viewYear = today.getFullYear();
        let viewMonth = today.getMonth();
        let selected = new Date(today.getFullYear(), today.getMonth(), today.getDate());

        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'];

        function sameDay(a, b) {
            return a.getFullYear() === b.getFullYear()
                && a.getMonth() === b.getMonth()
                && a.getDate() === b.getDate();
        }

        function renderCalendar() {
            monthLabel.textContent = monthNames[viewMonth] + ' ' + viewYear;
            daysEl.innerHTML = '';

            const first = new Date(viewYear, viewMonth, 1);
            const startOffset = first.getDay();
            const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();
            const daysInPrev = new Date(viewYear, viewMonth, 0).getDate();
            const totalCells = Math.ceil((startOffset + daysInMonth) / 7) * 7;

            for (let i = 0; i < totalCells; i++) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'calendar-day';
                btn.setAttribute('role', 'gridcell');

                let cellDate;
                let outside = false;
                if (i < startOffset) {
                    const day = daysInPrev - startOffset + i + 1;
                    cellDate = new Date(viewYear, viewMonth - 1, day);
                    outside = true;
                } else if (i >= startOffset + daysInMonth) {
                    const day = i - (startOffset + daysInMonth) + 1;
                    cellDate = new Date(viewYear, viewMonth + 1, day);
                    outside = true;
                } else {
                    const day = i - startOffset + 1;
                    cellDate = new Date(viewYear, viewMonth, day);
                }

                btn.textContent = String(cellDate.getDate());
                if (outside) btn.classList.add('is-outside');
                if (sameDay(cellDate, today)) btn.classList.add('is-today');
                if (sameDay(cellDate, selected)) btn.classList.add('is-selected');

                btn.addEventListener('click', () => {
                    selected = new Date(cellDate.getFullYear(), cellDate.getMonth(), cellDate.getDate());
                    if (selected.getMonth() !== viewMonth || selected.getFullYear() !== viewYear) {
                        viewYear = selected.getFullYear();
                        viewMonth = selected.getMonth();
                    }
                    renderCalendar();
                });

                daysEl.appendChild(btn);
            }
        }

        prevBtn?.addEventListener('click', () => {
            viewMonth -= 1;
            if (viewMonth < 0) { viewMonth = 11; viewYear -= 1; }
            renderCalendar();
        });
        nextBtn?.addEventListener('click', () => {
            viewMonth += 1;
            if (viewMonth > 11) { viewMonth = 0; viewYear += 1; }
            renderCalendar();
        });
        todayBtn?.addEventListener('click', () => {
            viewYear = today.getFullYear();
            viewMonth = today.getMonth();
            selected = new Date(today.getFullYear(), today.getMonth(), today.getDate());
            renderCalendar();
        });

        renderCalendar();
    })();
    </script>

</body>
</html>

