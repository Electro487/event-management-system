<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - <?php echo htmlspecialchars(defined('SITE_NAME') ? SITE_NAME : 'Event Management System'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/organizer-layout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="/EventManagementSystem/public/assets/css/notifications.css?v=<?php echo time(); ?>">
    <style>
        .reports-container {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .reports-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .reports-header h2 {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .reports-header p {
            font-size: 14px;
            color: #64748b;
        }

        .header-controls {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .date-filter {
            background: white;
            padding: 8px 15px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            font-size: 13px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-generate {
            background: #FFC24A;
            color: #1e293b;
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-generate:hover {
            background: #eab308;
            transform: translateY(-1px);
        }

        /* Stats Grid */
        .analytics-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .a-stat-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .a-stat-card .label {
            font-size: 11px;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: block;
        }

        .a-stat-card .value {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
        }

        .a-stat-card .trend {
            position: absolute;
            top: 24px;
            right: 24px;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 20px;
        }

        .trend.positive { background: #e6fcf0; color: #246A55; }
        .trend.negative { background: #feebeb; color: #e74c3c; }

        /* Middle Grid */
        .analytics-grid {
            display: grid;
            grid-template-columns: 1.8fr 1fr;
            gap: 25px;
        }

        .chart-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .chart-card h3 {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 20px;
        }

        /* Category Breakdown Circle */
        .category-breakdown {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .circle-container {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: #f1f5f9;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0px 0px 0px 15px #f1f5f9; /* Initial appearance */
        }
        
        .circle-inner {
            width: 150px;
            height: 150px;
            background: white;
            border-radius: 50%;
            position: absolute;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .circle-info .pct {
            font-size: 28px;
            font-weight: 800;
            color: #1e293b;
            display: block;
        }

        .circle-info .lbl {
            font-size: 11px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
        }

        .legend {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            width: 100%;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #475569;
        }

        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        /* Top Events */
        .top-events-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .top-event-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px;
            border-radius: 10px;
            transition: background 0.2s;
        }

        .top-event-item:hover {
            background: #f8fafc;
        }

        .top-event-item img {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            object-fit: cover;
        }

        .te-info { flex: 1; }
        .te-name { font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 2px; }
        .te-cat { font-size: 11px; color: #64748b; font-weight: 600; }
        .te-revenue { font-size: 14px; font-weight: 800; color: #246A55; text-align: right; }

        /* Package Breakdown Bars */
        .package-breakdown {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .package-row {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .package-meta {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
        }

        .p-bar-bg {
            height: 10px;
            background: #f1f5f9;
            border-radius: 5px;
            overflow: hidden;
        }

        .p-bar-fill {
            height: 100%;
            background: #246A55;
            border-radius: 5px;
            transition: width 1s ease-out;
        }

        /* Transactions Table */
        .transactions-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .table-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .table-search {
            position: relative;
            width: 250px;
        }

        .table-search input {
            width: 100%;
            padding: 8px 12px 8px 35px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            font-size: 13px;
        }

        .table-search i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .table-export {
            display: flex;
            gap: 10px;
        }

        .btn-export {
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            background: white;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .btn-export:hover { background: #f8fafc; }

        .tr-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .tr-status-paid { background: #e6fcf0; color: #246A55; }
        .tr-status-pending { background: #fff4e5; color: #e67e22; }
        .tr-status-failed { background: #feebeb; color: #e74c3c; }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .sidebar {
                display: none !important;
            }
            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 0 !important;
            }
            .header-controls {
                display: none !important;
            }
            body {
                background: white !important;
            }
            .chart-card, .a-stat-card, .transactions-card {
                box-shadow: none !important;
                border: 1px solid #e2e8f0 !important;
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }
            /* Break the layout for cleaner printing */
            .analytics-grid {
                display: block !important;
            }
            .analytics-grid > div {
                margin-bottom: 20px !important;
            }
        }

        /* Mobile Responsiveness for Reports */
        @media (max-width: 1024px) {
            .analytics-stats { grid-template-columns: repeat(2, 1fr); gap: 15px; }
            .analytics-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .main-content { padding: 20px; }
            .header { flex-direction: column; align-items: flex-start; gap: 15px; }
            .header-controls { width: 100%; flex-wrap: wrap; }
            .date-filter { width: 100%; justify-content: space-between; }
            .btn-generate { width: 100%; justify-content: center; }
            .analytics-stats { grid-template-columns: 1fr; }
            .table-controls { flex-direction: column; gap: 15px; align-items: flex-start; }
            .table-search { width: 100%; }
            .transactions-card { overflow-x: auto; -webkit-overflow-scrolling: touch; }
            .transactions-card table { min-width: 800px; }
        }

        @media (max-width: 480px) {
            .main-content { padding: 15px; }
            .a-stat-card { padding: 15px; }
            .a-stat-card .value { font-size: 20px; }
        }
    </style>
</head>

<body>

    <?php include_once __DIR__ . '/partials/sidebar.php'; ?>

    <main class="main-content">
        <header class="header">
            <div class="reports-header-text">
                <h2>Reports & Analytics</h2>
                <p>Generate and export platform performance reports.</p>
            </div>
            <div class="header-controls">
                <div class="date-filter">
                    <i class="fa-regular fa-calendar-range"></i>
                    <input type="date" id="filter-start" style="border:none; outline:none; background:transparent; font-size:13px; color:#64748b; font-family:inherit;">
                    <span>-</span>
                    <input type="date" id="filter-end" style="border:none; outline:none; background:transparent; font-size:13px; color:#64748b; font-family:inherit;">
                </div>
                <button class="btn-generate" onclick="window.print()">
                    <i class="fa-solid fa-file-export"></i> Generate Report
                </button>
            </div>
        </header>

        <div class="reports-container">
            <!-- Top Stats Row -->
            <div class="analytics-stats">
                <div class="a-stat-card">
                    <span class="label">Total Revenue</span>
                    <div class="value" id="stat-revenue">Rs. 0.00</div>
                </div>
                <div class="a-stat-card">
                    <span class="label">Total Bookings</span>
                    <div class="value" id="stat-sales">0</div>
                </div>
                <div class="a-stat-card">
                    <span class="label">Booking Rate</span>
                    <div class="value" id="stat-rate">0%</div>
                </div>
                <div class="a-stat-card">
                    <span class="label">Total Events</span>
                    <div class="value" id="stat-events">0</div>
                </div>
            </div>

            <!-- Middle Charts/Lists Grid -->
            <div class="analytics-grid">
                <!-- Revenue Over Time -->
                <div class="chart-card">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
                        <h3 style="margin:0;">Revenue Over Time</h3>
                        <div style="display:flex; gap:10px; font-size:11px; font-weight:600; color:#64748b;">
                            <span style="display:flex; align-items:center; gap:5px;"><div class="dot" style="background:#246A55;"></div> Revenue for Selected Dates</span>
                        </div>
                    </div>
                    <div style="height: 288px; width: 100%; display: flex; align-items: flex-end; gap: 8px; padding-bottom: 15px;" id="revenue-bar-chart">
                        <p style="text-align:center; width:100%; color:#64748b; font-size:13px; align-self:center;">Loading data...</p>
                    </div>
                </div>

                <!-- Revenue By Category -->
                <div class="chart-card">
                    <h3>Revenue By Event Category</h3>
                    <div class="category-breakdown">
                        <div class="circle-container" id="revenue-circle">
                            <div class="circle-inner">
                                <span class="pct" id="cat-pct">0%</span>
                                <span class="lbl" id="cat-lbl">Loading...</span>
                            </div>
                        </div>
                        <div class="legend" id="cat-legend">
                            <!-- Dynamic -->
                        </div>
                    </div>
                </div>

                <!-- Bookings By Package Type -->
                <div class="chart-card">
                    <h3>Bookings By Package Type</h3>
                    <div class="package-breakdown" id="package-breakdown">
                        <p style="text-align:center; color:#64748b; font-size:13px;">Loading data...</p>
                    </div>
                </div>

                <!-- Top Performing Events -->
                <div class="chart-card">
                    <h3>Top Performing Events</h3>
                    <div class="top-events-list" id="top-events-list">
                        <p style="text-align:center; color:#64748b; font-size:13px;">Loading data...</p>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="transactions-card">
                <div class="table-controls">
                    <h3 style="margin:0; font-size:16px;">Recent Transactions</h3>
                    <div class="header-controls">
                        <div class="table-search">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="transactions-search" placeholder="Search transactions...">
                        </div>
                        <div class="table-export">
                            <button type="button" class="btn-export" id="btn-export-pdf"><i class="fa-solid fa-file-pdf"></i> Export as PDF</button>
                            <button type="button" class="btn-export" id="btn-export-csv"><i class="fa-solid fa-file-csv"></i> Export as CSV</button>
                        </div>
                    </div>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Client</th>
                            <th>Event Name</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Method</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="transactions-body">
                        <!-- Dynamic -->
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="/EventManagementSystem/public/assets/js/apiClient.js?v=<?php echo time(); ?>"></script>
    <script src="/EventManagementSystem/public/assets/js/notifications.js?v=<?php echo time(); ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
    <script>
        let allTransactions = [];

        document.addEventListener('DOMContentLoaded', () => {
            const startInput = document.getElementById('filter-start');
            const endInput = document.getElementById('filter-end');
            const searchInput = document.getElementById('transactions-search');

            const today = new Date();
            // Start local first/last day logic
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const lastData = new Date(year, today.getMonth() + 1, 0);
            
            startInput.value = `${year}-${month}-01`;
            endInput.value = `${year}-${month}-${String(lastData.getDate()).padStart(2, '0')}`;

            function handleFilterChange() {
                if (startInput.value && endInput.value) {
                    fetchAnalytics(startInput.value, endInput.value);
                }
            }

            startInput.addEventListener('change', handleFilterChange);
            endInput.addEventListener('change', handleFilterChange);
            searchInput.addEventListener('input', () => renderTransactions(allTransactions, searchInput.value));

            document.getElementById('btn-export-csv').addEventListener('click', exportTransactionsCsv);
            document.getElementById('btn-export-pdf').addEventListener('click', exportTransactionsPdf);

            handleFilterChange();
        });

        function fetchAnalytics(start, end) {
            if (!window.emsApi) return;

            window.emsApi.apiFetch(`/api/v1/admin/analytics?start=${start}&end=${end}`)
                .then(res => {
                    if (res.success) {
                        updateUI(res.data);
                    }
                })
                .catch(err => console.error('Error:', err));
        }

        function updateUI(data) {
            // 1. Stats
            document.getElementById('stat-revenue').innerText = 'Rs. ' + parseFloat(data.stats.revenue).toLocaleString();
            document.getElementById('stat-sales').innerText = data.stats.sales;
            document.getElementById('stat-rate').innerText = data.stats.booking_rate + '%';
            document.getElementById('stat-events').innerText = data.stats.total_events;

            const barChart = document.getElementById('revenue-bar-chart');
            // Ensure container anchors items to the bottom gracefully
            barChart.style.alignItems = 'flex-end';
            barChart.style.justifyContent = 'flex-start'; // Align left as requested
            barChart.style.gap = '15px';
            
            barChart.innerHTML = '';
            if (data.revenue_over_time && data.revenue_over_time.length > 0) {
                const maxRev = Math.max(...data.revenue_over_time.map(d => parseFloat(d.total))) || 1;
                
                // Set max-width purely for aesthetics
                const maxBarWidth = data.revenue_over_time.length < 5 ? '65px' : 'auto';

                data.revenue_over_time.forEach(item => {
                    const val = parseFloat(item.total);
                    // Minimum height set to 15% so text doesn't crush the bar
                    const hPct = Math.max((val / maxRev) * 100, 15); 
                    
                    const dateObj = new Date(item.date);
                    const shortDate = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    const displayVal = val >= 1000 ? (val/1000).toFixed(1) + 'k' : val;

                    barChart.insertAdjacentHTML('beforeend', `
                        <div style="flex:1; max-width:${maxBarWidth}; height:${hPct}%; display:flex; flex-direction:column; align-items:center; justify-content:flex-end;">
                            <span style="font-size:10px; color:#64748b; font-weight:600; text-align:center; padding-bottom: 4px;">Rs.${displayVal}</span>
                            <div style="width:100%; flex:1; background:#246A55; border-radius:4px 4px 0 0;" title="${dateObj.toLocaleDateString()}: Rs.${val.toLocaleString()}"></div>
                            <span style="font-size:10px; color:#94a3b8; font-weight:500; text-align:center; display:block; padding-top: 6px; white-space:nowrap;">${shortDate}</span>
                        </div>
                    `);
                });
            } else {
                barChart.innerHTML = '<p style="text-align:center; width:100%; color:#64748b; font-size:13px; align-self:center;">No revenue data for selected period.</p>';
            }

            // 3. Top Events
            const topList = document.getElementById('top-events-list');
            topList.innerHTML = '';
            if (data.top_events && data.top_events.length > 0) {
                data.top_events.forEach(ev => {
                    let img = '/EventManagementSystem/public/assets/images/placeholder.jpg';
                    if (ev.image_path) {
                        img = (ev.image_path[0] === '/') ? ev.image_path : '/EventManagementSystem/public/assets/images/events/' + ev.image_path;
                    }
                    topList.insertAdjacentHTML('beforeend', `
                        <div class="top-event-item">
                            <img src="${img}" alt="Event">
                            <div class="te-info">
                                <div class="te-name">${ev.title}</div>
                                <div class="te-cat">${ev.category}</div>
                            </div>
                            <div class="te-revenue">Rs. ${(ev.total_revenue / 1000).toFixed(1)}K</div>
                        </div>
                    `);
                });
            } else {
                topList.innerHTML = '<p style="text-align:center; color:#64748b; font-size:13px;">No data yet.</p>';
            }

            // 3. Categories Legend & Circle
            const legend = document.getElementById('cat-legend');
            legend.innerHTML = '';
            const colors = ['#246A55', '#eab308', '#e74c3c', '#9b59b6', '#3498db', '#f39c12', '#34495e', '#16a085'];
            
            if (data.categories && data.categories.length > 0) {
                // Sort categories by highest revenue
                data.categories.sort((a, b) => parseFloat(b.total_revenue) - parseFloat(a.total_revenue));
                
                const totalRev = data.categories.reduce((acc, c) => acc + parseFloat(c.total_revenue), 0);
                
                let gradientStops = [];
                let cumulativePct = 0;

                data.categories.forEach((cat, i) => {
                    const rev = parseFloat(cat.total_revenue);
                    const pct = totalRev > 0 ? (rev / totalRev) * 100 : 0;
                    const color = colors[i % colors.length];

                    legend.insertAdjacentHTML('beforeend', `
                        <div class="legend-item"><div class="dot" style="background:${color};"></div> ${cat.category}</div>
                    `);

                    if (pct > 0) {
                        gradientStops.push(`${color} ${cumulativePct}% ${cumulativePct + pct}%`);
                        cumulativePct += pct;
                    }

                    if (i === 0) { // Set highest as main inside circle
                        document.getElementById('cat-pct').innerText = pct.toFixed(1) + '%';
                        document.getElementById('cat-lbl').innerText = cat.category;
                    }
                });

                if (gradientStops.length > 0) {
                    const circle = document.getElementById('revenue-circle');
                    circle.style.background = `conic-gradient(${gradientStops.join(', ')})`;
                    circle.style.boxShadow = 'none'; // remove initial gray
                } else {
                    document.getElementById('revenue-circle').style.background = '#f1f5f9';
                    document.getElementById('revenue-circle').style.boxShadow = 'inset 0px 0px 0px 15px #f1f5f9';
                }
            } else {
                document.getElementById('cat-pct').innerText = '0%';
                document.getElementById('cat-lbl').innerText = 'No Data';
                document.getElementById('revenue-circle').style.background = '#f1f5f9';
                document.getElementById('revenue-circle').style.boxShadow = 'inset 0px 0px 0px 15px #f1f5f9';
            }

            // 4. Packages Breakdown
            const pkgContainer = document.getElementById('package-breakdown');
            pkgContainer.innerHTML = '';
            
            // Standardize tiers so it always shows even if 0
            const defaultTiers = ['premium', 'standard', 'basic'];
            const pkgColors = ['#246A55', '#FFC24A', '#e74c3c'];
            const pkgMap = {};
            
            let totalPkgs = 0;
            if (data.packages && data.packages.length > 0) {
                totalPkgs = data.packages.reduce((acc, p) => acc + parseInt(p.count), 0);
                data.packages.forEach(p => {
                    const t = p.package_tier ? p.package_tier.toLowerCase() : 'standard';
                    pkgMap[t] = (pkgMap[t] || 0) + parseInt(p.count);
                });
            }

            defaultTiers.forEach((tier, index) => {
                const count = pkgMap[tier] || 0;
                const pct = totalPkgs > 0 ? ((count / totalPkgs) * 100).toFixed(0) : 0;
                const color = pkgColors[index];
                const displayTier = tier === 'premium' ? 'Premium / Corporate' 
                                  : tier === 'standard' ? 'Standard / Public' 
                                  : 'Basic / Economy';
                
                pkgContainer.insertAdjacentHTML('beforeend', `
                    <div class="package-row">
                        <div class="package-meta"><span>${displayTier}</span><span>${pct}% (${count})</span></div>
                        <div class="p-bar-bg"><div class="p-bar-fill" style="width: ${pct}%; background: ${color};"></div></div>
                    </div>
                `);
            });

            // 5. Transactions
            allTransactions = data.recent_transactions || [];
            const searchInput = document.getElementById('transactions-search');
            renderTransactions(allTransactions, searchInput ? searchInput.value : '');
        }

        function renderTransactions(transactions, query = '') {
            const trBody = document.getElementById('transactions-body');
            const term = query.trim().toLowerCase();
            const filtered = term
                ? transactions.filter(tr =>
                    [tr.id, tr.client, tr.event, tr.method, tr.status, tr.date, String(tr.amount)]
                        .some(val => String(val).toLowerCase().includes(term))
                )
                : transactions;

            trBody.innerHTML = '';
            if (filtered.length === 0) {
                trBody.innerHTML = `
                    <tr>
                        <td colspan="7" style="text-align:center; color:#64748b; padding:24px;">
                            ${transactions.length === 0 ? 'No transactions in the selected date range.' : 'No transactions match your search.'}
                        </td>
                    </tr>
                `;
                return;
            }

            filtered.forEach(tr => {
                const statusKey = (tr.status_class || tr.status || 'pending').toLowerCase();
                const statusClass = 'tr-status-' + statusKey;
                trBody.insertAdjacentHTML('beforeend', `
                    <tr>
                        <td style="font-weight:600; color:#1f6f59;"># ${escapeHtml(tr.id)}</td>
                        <td>${escapeHtml(tr.client)}</td>
                        <td>${escapeHtml(tr.event)}</td>
                        <td style="font-weight:700;">Rs. ${Number(tr.amount).toLocaleString()}</td>
                        <td>${escapeHtml(tr.date)}</td>
                        <td><i class="fa-solid fa-money-bill-transfer"></i> ${escapeHtml(tr.method)}</td>
                        <td><span class="tr-badge ${statusClass}">${escapeHtml(tr.status)}</span></td>
                    </tr>
                `);
            });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text ?? '';
            return div.innerHTML;
        }

        function getFilteredTransactions() {
            const searchInput = document.getElementById('transactions-search');
            const term = (searchInput?.value || '').trim().toLowerCase();
            if (!term) return [...allTransactions];
            return allTransactions.filter(tr =>
                [tr.id, tr.client, tr.event, tr.method, tr.status, tr.date, String(tr.amount)]
                    .some(val => String(val).toLowerCase().includes(term))
            );
        }

        function getExportDateRange() {
            const start = document.getElementById('filter-start')?.value || '';
            const end = document.getElementById('filter-end')?.value || '';
            return { start, end, label: start && end ? `${start}_to_${end}` : 'export' };
        }

        function csvEscape(value) {
            const str = String(value ?? '');
            if (/[",\n\r]/.test(str)) {
                return `"${str.replace(/"/g, '""')}"`;
            }
            return str;
        }

        function downloadFile(content, filename, mimeType) {
            const blob = new Blob([content], { type: mimeType });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            link.remove();
            URL.revokeObjectURL(url);
        }

        function exportTransactionsCsv() {
            const rows = getFilteredTransactions();
            if (!rows.length) {
                alert('No transactions to export for the selected date range.');
                return;
            }

            const headers = ['Transaction ID', 'Client', 'Event Name', 'Amount (Rs.)', 'Date', 'Method', 'Status'];
            const lines = [
                headers.map(csvEscape).join(','),
                ...rows.map(tr => [
                    tr.id,
                    tr.client,
                    tr.event,
                    Number(tr.amount),
                    tr.date,
                    tr.method,
                    tr.status
                ].map(csvEscape).join(','))
            ];

            const { label } = getExportDateRange();
            downloadFile('\uFEFF' + lines.join('\n'), `transactions_${label}.csv`, 'text/csv;charset=utf-8;');
        }

        function exportTransactionsPdf() {
            const rows = getFilteredTransactions();
            if (!rows.length) {
                alert('No transactions to export for the selected date range.');
                return;
            }

            if (!window.jspdf || !window.jspdf.jsPDF) {
                exportTransactionsPdfPrint(rows);
                return;
            }

            const { start, end, label } = getExportDateRange();
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });

            doc.setFontSize(16);
            doc.setTextColor(30, 41, 59);
            doc.text('Recent Transactions Report', 14, 16);

            doc.setFontSize(10);
            doc.setTextColor(100, 116, 139);
            if (start && end) {
                doc.text(`Period: ${start} to ${end}`, 14, 23);
            }
            doc.text(`Generated: ${new Date().toLocaleString()}`, 14, 29);
            doc.text(`Total records: ${rows.length}`, 14, 35);

            doc.autoTable({
                startY: 42,
                head: [['Transaction ID', 'Client', 'Event Name', 'Amount', 'Date', 'Method', 'Status']],
                body: rows.map(tr => [
                    tr.id,
                    tr.client,
                    tr.event,
                    'Rs. ' + Number(tr.amount).toLocaleString(),
                    tr.date,
                    tr.method,
                    tr.status
                ]),
                styles: { fontSize: 9, cellPadding: 3 },
                headStyles: { fillColor: [36, 106, 85], textColor: 255, fontStyle: 'bold' },
                alternateRowStyles: { fillColor: [248, 250, 252] },
                margin: { left: 14, right: 14 }
            });

            doc.save(`transactions_${label}.pdf`);
        }

        function exportTransactionsPdfPrint(rows) {
            const { start, end } = getExportDateRange();
            const win = window.open('', '_blank');
            if (!win) {
                alert('Please allow pop-ups to export as PDF.');
                return;
            }

            const tableRows = rows.map(tr => `
                <tr>
                    <td>${escapeHtml(tr.id)}</td>
                    <td>${escapeHtml(tr.client)}</td>
                    <td>${escapeHtml(tr.event)}</td>
                    <td>Rs. ${Number(tr.amount).toLocaleString()}</td>
                    <td>${escapeHtml(tr.date)}</td>
                    <td>${escapeHtml(tr.method)}</td>
                    <td>${escapeHtml(tr.status)}</td>
                </tr>
            `).join('');

            win.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Transactions Report</title>
                    <style>
                        body { font-family: Inter, Arial, sans-serif; padding: 24px; color: #1e293b; }
                        h1 { font-size: 20px; margin: 0 0 8px; }
                        p { color: #64748b; font-size: 12px; margin: 0 0 20px; }
                        table { width: 100%; border-collapse: collapse; font-size: 12px; }
                        th, td { border: 1px solid #e2e8f0; padding: 8px 10px; text-align: left; }
                        th { background: #246A55; color: #fff; }
                        tr:nth-child(even) td { background: #f8fafc; }
                    </style>
                </head>
                <body>
                    <h1>Recent Transactions Report</h1>
                    <p>Period: ${escapeHtml(start)} to ${escapeHtml(end)} &bull; Generated: ${new Date().toLocaleString()}</p>
                    <table>
                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>Client</th>
                                <th>Event Name</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Method</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>${tableRows}</tbody>
                    </table>
                </body>
                </html>
            `);
            win.document.close();
            win.focus();
            setTimeout(() => win.print(), 300);
        }
    </script>
</body>

</html>
