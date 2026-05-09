<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GlowCare Admin Dashboard</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500&display=swap">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
body {
            font-family: 'DM Sans', sans-serif;
            background: #fdf0f5;
            color: #3d1a22;
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: 260px;
            background: #3d1a22;
            min-height: 100vh;
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: transform 0.3s ease;
        }

        .sidebar-logo {
            padding: 32px 28px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-logo .brand {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-style: italic;
            color: #f2c4ce;
            letter-spacing: 1px;
        }

        .sidebar-logo .role {
            font-size: 9px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.3);
            margin-top: 4px;
        }

        .sidebar-nav {
            flex: 1;
            padding: 24px 0;
            overflow-y: auto;
        }

        .nav-section {
            margin-bottom: 8px;
        }

        .nav-section-label {
            font-size: 8px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.25);
            padding: 12px 28px 8px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 28px;
            font-size: 13px;
            color: rgba(255,255,255,0.5);
            cursor: pointer;
            transition: all 0.2s;
            border-left: 3px solid transparent;
            text-decoration: none;
        }

        .nav-item:hover {
            background: rgba(255,255,255,0.05);
            color: rgba(255,255,255,0.85);
        }

        .nav-item.active {
            background: rgba(197,80,133,0.15);
            color: #f2c4ce;
            border-left-color: #c55085;
        }

        .nav-icon {
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
        }

        .nav-badge {
            margin-left: auto;
            background: #c55085;
            color: #fff;
            font-size: 9px;
            padding: 2px 7px;
            border-radius: 20px;
            font-weight: 500;
        }

        .sidebar-footer {
            padding: 20px 28px;
            border-top: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #c55085;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #fff;
            font-family: 'Playfair Display', serif;
        }

        .user-info .name {
            font-size: 13px;
            color: rgba(255,255,255,0.8);
            font-weight: 500;
        }

        .user-info .label {
            font-size: 10px;
            color: rgba(255,255,255,0.3);
            letter-spacing: 0.5px;
        }

        /* ── MAIN ── */
        .main {
            margin-left: 260px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── TOPBAR ── */
        .topbar {
            background: #ffffff;
            border-bottom: 1px solid #f2c4ce;
            padding: 0 40px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-left .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 400;
            color: #3d1a22;
        }

        .topbar-left .breadcrumb {
            font-size: 11px;
            color: #b89098;
            letter-spacing: 0.5px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .topbar-search {
            background: #fdf0f5;
            border: 1px solid #f2c4ce;
            border-radius: 50px;
            padding: 8px 18px;
            font-size: 12px;
            font-family: 'DM Sans', sans-serif;
            color: #3d1a22;
            outline: none;
            width: 220px;
            transition: border-color 0.3s;
        }

        .topbar-search:focus { border-color: #c55085; }
        .topbar-search::placeholder { color: #b89098; }

        .notif-btn {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: #fdf0f5;
            border: 1px solid #f2c4ce;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            font-size: 16px;
            position: relative;
            transition: background 0.2s;
        }

        .notif-btn:hover { background: #f9e7ef; }

        .notif-dot {
            position: absolute;
            top: 6px; right: 7px;
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #c55085;
            border: 2px solid white;
        }

        /* ── CONTENT ── */
        .content {
            padding: 36px 40px;
            flex: 1;
        }

        /* ── PANELS (views) ── */
        .panel { display: none; }
        .panel.active { display: block; animation: fadeIn 0.3s ease; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── SECTION TITLE ── */
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 400;
            color: #3d1a22;
            margin-bottom: 6px;
        }

        .section-title em { font-style: italic; color: #c55085; }

        .section-sub {
            font-size: 13px;
            color: #b89098;
            margin-bottom: 32px;
            font-weight: 300;
        }

        /* ── STAT CARDS ── */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 24px;
            border: 1px solid #f2c4ce;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(197,80,133,0.1);
        }

        .stat-card::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: #c55085;
        }

        .stat-card.green::before { background: #6dbf9e; }
        .stat-card.purple::before { background: #9b7dd4; }
        .stat-card.orange::before { background: #e8956d; }

        .stat-icon {
            font-size: 22px;
            margin-bottom: 12px;
        }

        .stat-value {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: #3d1a22;
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 11px;
            color: #b89098;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .stat-change {
            position: absolute;
            top: 24px; right: 24px;
            font-size: 11px;
            color: #6dbf9e;
            font-weight: 500;
        }

        .stat-change.down { color: #e8956d; }

        /* ── TWO-COL LAYOUT ── */
        .two-col {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        /* ── CARD ── */
        .card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #f2c4ce;
            overflow: hidden;
        }

        .card-header {
            padding: 20px 24px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .card-title {
            font-family: 'Playfair Display', serif;
            font-size: 16px;
            font-weight: 400;
            color: #3d1a22;
        }

        .card-action {
            font-size: 11px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #c55085;
            cursor: pointer;
            text-decoration: none;
        }

        .card-action:hover { color: #a33d6d; }

        /* ── TABLE ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead tr {
            background: #fdf0f5;
        }

        .data-table thead th {
            padding: 12px 20px;
            font-size: 9px;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: #b89098;
            font-weight: 500;
            text-align: left;
        }

        .data-table tbody tr {
            border-bottom: 1px solid #fdf0f5;
            transition: background 0.15s;
        }

        .data-table tbody tr:last-child { border-bottom: none; }
        .data-table tbody tr:hover { background: #fdf0f5; }

        .data-table tbody td {
            padding: 14px 20px;
            font-size: 13px;
            color: #3d1a22;
            font-weight: 300;
        }

        .td-name {
            font-family: 'Playfair Display', serif;
            font-size: 14px;
            font-weight: 400;
        }

        .td-sub {
            font-size: 10px;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #b89098;
            display: block;
            margin-top: 2px;
        }

        /* ── BADGE STATUS ── */
        .badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 50px;
            font-size: 10px;
            letter-spacing: 0.5px;
            font-weight: 500;
        }

        .badge-green  { background: #e8f9f1; color: #3dab74; }
        .badge-pink   { background: #fde8f2; color: #c55085; }
        .badge-yellow { background: #fef9e7; color: #c9970e; }
        .badge-gray   { background: #f5f5f5; color: #888; }

        /* ── AVATAR ── */
        .avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: #f2c4ce;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 12px;
            font-family: 'Playfair Display', serif;
            color: #c55085;
            margin-right: 8px;
        }

        /* ── ACTION BTNS ── */
        .act-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            padding: 4px 6px;
            border-radius: 4px;
            transition: background 0.15s;
            color: #b89098;
        }

        .act-btn:hover { background: #fdf0f5; color: #c55085; }

        /* ── ACTIVITY FEED ── */
        .activity-list { padding: 0 24px 24px; }

        .activity-item {
            display: flex;
            gap: 14px;
            padding: 12px 0;
            border-bottom: 1px solid #fdf0f5;
        }

        .activity-item:last-child { border-bottom: none; }

        .activity-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #c55085;
            margin-top: 6px;
            flex-shrink: 0;
        }

        .activity-dot.green { background: #6dbf9e; }
        .activity-dot.orange { background: #e8956d; }

        .activity-text {
            font-size: 13px;
            color: #3d1a22;
            line-height: 1.5;
            font-weight: 300;
        }

        .activity-text strong { font-weight: 500; color: #3d1a22; }

        .activity-time {
            font-size: 10px;
            color: #b89098;
            margin-top: 3px;
            letter-spacing: 0.5px;
        }

        /* ── ADD/EDIT FORM MODAL ── */
        .modal-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(61,26,34,0.4);
            backdrop-filter: blur(4px);
            z-index: 999;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.open { display: flex; animation: fadeIn 0.2s ease; }

        .modal {
            background: #ffffff;
            border-radius: 16px;
            width: 540px;
            max-height: 90vh;
            overflow-y: auto;
            padding: 40px;
        }

        .modal-title {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            color: #3d1a22;
            margin-bottom: 4px;
        }

        .modal-title em { color: #c55085; font-style: italic; }
        .modal-sub { font-size: 12px; color: #b89098; margin-bottom: 28px; font-weight: 300; }

        .modal-close {
            position: absolute; top: 16px; right: 20px;
            background: none; border: none;
            font-size: 20px; cursor: pointer;
            color: #b89098;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 16px;
        }

        .form-group.full { grid-column: 1 / -1; }

        .form-label {
            font-size: 9px;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: #7a4d5c;
        }

        .form-input, .form-select, .form-textarea {
            background: #fdf0f5;
            border: 1px solid transparent;
            border-radius: 6px;
            padding: 11px 14px;
            font-size: 13px;
            font-weight: 300;
            color: #3d1a22;
            font-family: 'DM Sans', sans-serif;
            outline: none;
            width: 100%;
            transition: border-color 0.3s;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: #f2c4ce;
            background: #ffffff;
        }

        .form-textarea { resize: vertical; min-height: 80px; }

        .modal-footer {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 8px;
        }

        .btn-cancel {
            background: transparent;
            border: 1px solid #f2c4ce;
            color: #7a4d5c;
            padding: 10px 24px;
            border-radius: 50px;
            font-size: 11px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-cancel:hover { background: #fdf0f5; }

        .btn-save {
            background: #c55085;
            color: #fff;
            border: none;
            padding: 10px 28px;
            border-radius: 50px;
            font-size: 11px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
        }

        .btn-save:hover { background: #a33d6d; transform: translateY(-1px); }

        /* ── ADD NEW BUTTON ── */
        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #c55085;
            color: #fff;
            padding: 10px 22px;
            border-radius: 50px;
            font-size: 11px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-family: 'DM Sans', sans-serif;
            border: none;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
        }

        .btn-add:hover { background: #a33d6d; transform: translateY(-1px); }

        /* ── PAGE HEADER ROW ── */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        /* ── FILTER BAR ── */
        .filter-bar {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-input {
            background: #ffffff;
            border: 1px solid #f2c4ce;
            border-radius: 50px;
            padding: 8px 18px;
            font-size: 12px;
            font-family: 'DM Sans', sans-serif;
            color: #3d1a22;
            outline: none;
            transition: border-color 0.3s;
        }

        .filter-input:focus { border-color: #c55085; }

        .filter-select {
            background: #ffffff;
            border: 1px solid #f2c4ce;
            border-radius: 50px;
            padding: 8px 18px;
            font-size: 12px;
            font-family: 'DM Sans', sans-serif;
            color: #3d1a22;
            outline: none;
            cursor: pointer;
        }

        /* ── SCHEDULE GRID ── */
        .schedule-week {
            display: grid;
            grid-template-columns: 80px repeat(6, 1fr);
            gap: 4px;
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #f2c4ce;
            overflow: hidden;
            margin-bottom: 24px;
        }

        .sch-head {
            padding: 14px 10px;
            text-align: center;
            font-size: 9px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #b89098;
            background: #fdf0f5;
            border-bottom: 1px solid #f2c4ce;
        }

        .sch-time {
            padding: 12px 10px;
            font-size: 10px;
            color: #b89098;
            text-align: right;
            background: #fdf0f5;
            border-right: 1px solid #f2c4ce;
        }

        .sch-cell {
            padding: 8px;
            min-height: 52px;
            border-bottom: 1px solid #fdf0f5;
            cursor: pointer;
            transition: background 0.15s;
            position: relative;
        }

        .sch-cell:hover { background: #fdf0f5; }

        .sch-event {
            background: #c55085;
            color: #fff;
            border-radius: 6px;
            padding: 4px 8px;
            font-size: 10px;
            line-height: 1.4;
        }

        .sch-event.teal { background: #5bab8b; }
        .sch-event.purple { background: #8b6dc8; }
        .sch-event.orange { background: #d4895a; }

        /* ── REPORT CARDS ── */
        .report-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 28px;
        }

        .report-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #f2c4ce;
            padding: 28px 24px;
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }

        .report-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(197,80,133,0.1);
        }

        .report-icon { font-size: 28px; margin-bottom: 14px; }

        .report-name {
            font-family: 'Playfair Display', serif;
            font-size: 16px;
            color: #3d1a22;
            margin-bottom: 6px;
        }

        .report-desc {
            font-size: 12px;
            color: #b89098;
            line-height: 1.7;
            font-weight: 300;
        }

        .report-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 16px;
            font-size: 10px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #c55085;
            text-decoration: none;
            font-weight: 500;
        }

        /* ── CHART PLACEHOLDER ── */
        .chart-area {
            padding: 24px;
        }

        .chart-bars {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            height: 140px;
            border-bottom: 1px solid #f2c4ce;
            padding-bottom: 8px;
        }

        .chart-bar-wrap {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            height: 100%;
            justify-content: flex-end;
        }

        .chart-bar {
            width: 100%;
            background: linear-gradient(to top, #c55085, #e87fb4);
            border-radius: 4px 4px 0 0;
            transition: opacity 0.2s;
        }

        .chart-bar:hover { opacity: 0.8; }

        .chart-label {
            font-size: 9px;
            color: #b89098;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 8px;
        }

        .chart-legend {
            display: flex;
            gap: 20px;
            margin-top: 16px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            color: #b89098;
        }

        .legend-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #c55085;
        }

        .legend-dot.green { background: #6dbf9e; }

        /* ── PROFIL ── */
        .profil-hero {
            background: #ffffff;
            border: 1px solid #f2c4ce;
            border-radius: 16px;
            padding: 36px;
            margin-bottom: 24px;
            display: flex;
            gap: 32px;
            align-items: flex-start;
        }
        .profil-avatar-wrap { position: relative; flex-shrink: 0; }
        .profil-avatar {
            width: 96px; height: 96px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f2c4ce, #c55085);
            display: flex; align-items: center; justify-content: center;
            font-size: 36px;
            font-family: 'Playfair Display', serif;
            color: #fff;
            border: 3px solid #f2c4ce;
        }
        .profil-edit-avatar {
            position: absolute;
            bottom: 4px; right: 4px;
            width: 28px; height: 28px;
            border-radius: 50%;
            background: #c55085;
            color: #fff;
            font-size: 11px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            border: 2px solid #fff;
        }
        .profil-info { flex: 1; }
        .profil-name {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            font-weight: 400;
            color: #3d1a22;
            margin-bottom: 4px;
        }
        .profil-role {
            font-size: 10px;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: #c55085;
            margin-bottom: 16px;
        }
        .profil-meta { display: flex; flex-wrap: wrap; gap: 24px; }
        .profil-meta-item { display: flex; flex-direction: column; gap: 2px; }
        .profil-meta-label { font-size: 9px; letter-spacing: 2px; text-transform: uppercase; color: #b89098; }
        .profil-meta-value { font-size: 13px; color: #3d1a22; }
        .profil-stats { display: flex; gap: 32px; flex-shrink: 0; }
        .profil-stat-val {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            color: #c55085;
            text-align: center;
        }
        .profil-stat-lbl { font-size: 9px; letter-spacing: 1.5px; text-transform: uppercase; color: #b89098; text-align: center; }
        .section-card {
            background: #ffffff;
            border: 1px solid #f2c4ce;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .section-card-header {
            padding: 18px 24px;
            border-bottom: 1px solid #fdf0f5;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .section-card-title { font-family: 'Playfair Display', serif; font-size: 16px; color: #3d1a22; }
        .section-card-title em { color: #c55085; font-style: italic; }
        .section-card-body { padding: 24px; }
        .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .log-item {
            display: flex;
            gap: 14px;
            padding: 12px 0;
            border-bottom: 1px solid #fdf0f5;
            align-items: flex-start;
        }
        .log-item:last-child { border-bottom: none; }
        .log-dot { width: 8px; height: 8px; border-radius: 50%; background: #c55085; margin-top: 5px; flex-shrink: 0; }
        .log-dot.green { background: #6dbf9e; }
        .log-dot.orange { background: #e8956d; }
        .log-dot.gray { background: #b89098; }
        .log-text { font-size: 13px; color: #3d1a22; font-weight: 300; line-height: 1.5; }
        .log-text strong { font-weight: 500; }
        .log-time { font-size: 10px; color: #b89098; margin-top: 3px; }

        /* ── TOAST ── */
        .toast {
            position: fixed;
            bottom: 32px; right: 32px;
            background: #3d1a22;
            color: #fff;
            padding: 14px 24px;
            border-radius: 50px;
            font-size: 13px;
            z-index: 9999;
            display: none;
            align-items: center;
            gap: 10px;
            animation: slideUp 0.3s ease;
        }

        .toast.show { display: flex; }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<!-- ══ SIDEBAR ══ -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="brand">GlowCare Clinic</div>
        <div class="role">Admin Panel</div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-label">Utama</div>
            <a class="nav-item active" onclick="showPanel('dashboard', this)">
                <span class="nav-icon">📊</span> Dashboard
            </a>
            <a class="nav-item" onclick="showPanel('aktivitas', this)">
                <span class="nav-icon">🔔</span> Aktivitas
                <span class="nav-badge">5</span>
            </a>
        </div>
        <div class="nav-section">
            <div class="nav-section-label">Manajemen</div>
            <a class="nav-item" onclick="showPanel('pasien', this)">
                <span class="nav-icon">👤</span> Data Pasien
            </a>
            <a class="nav-item" onclick="showPanel('dokter', this)">
                <span class="nav-icon">🩺</span> Data Dokter
            </a>
            <a class="nav-item" onclick="showPanel('jadwal', this)">
                <span class="nav-icon">📅</span> Jadwal Dokter
            </a>
        </div>
        <div class="nav-section">
            <div class="nav-section-label">Konten</div>
            <a class="nav-item" onclick="showPanel('treatment', this)">
                <span class="nav-icon">💉</span> Kelola Treatment
            </a>
        </div>
        <div class="nav-section">
            <div class="nav-section-label">Laporan</div>
            <a class="nav-item" onclick="showPanel('laporan', this)">
                <span class="nav-icon">📋</span> Laporan
            </a>
        </div>
        <div class="nav-section">
            <div class="nav-section-label">Akun</div>
            <a class="nav-item" onclick="showPanel('profil', this)">
                <span class="nav-icon">⚙️</span> Profil & Keamanan
            </a>
        </div>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-user" onclick="showPanel('profil', document.querySelector('[onclick*=profil]'))" style="cursor:pointer">
            <div class="user-avatar">A</div>
            <div class="user-info">
                <div class="name">Admin</div>
                <div class="label">Administrator</div>
            </div>
        </div>
    </div>
</aside>

<!-- ══ MAIN ══ -->
<div class="main">

    <!-- TOPBAR -->
    <div class="topbar">
        <div class="topbar-left">
            <div class="page-title" id="topbar-title">Dashboard</div>
            <div class="breadcrumb" id="topbar-bc">GlowCare Admin → Dashboard</div>
        </div>
        <div class="topbar-right">
            <input class="topbar-search" type="text" placeholder="🔍  Cari pasien, dokter...">
            <div class="notif-btn">🔔<div class="notif-dot"></div></div>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <!-- ══ PANEL: DASHBOARD ══ -->
        <div class="panel active" id="panel-dashboard">
            <p class="section-sub">Selamat datang kembali — Sabtu, 02 Mei 2026</p>

            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-icon">👤</div>
                    <div class="stat-value">1.248</div>
                    <div class="stat-label">Total Pasien</div>
                    <div class="stat-change">↑ 12%</div>
                </div>
                <div class="stat-card green">
                    <div class="stat-icon">📅</div>
                    <div class="stat-value">34</div>
                    <div class="stat-label">Janji Hari Ini</div>
                    <div class="stat-change">↑ 8%</div>
                </div>
                <div class="stat-card purple">
                    <div class="stat-icon">🩺</div>
                    <div class="stat-value">8</div>
                    <div class="stat-label">Dokter Aktif</div>
                    <div class="stat-change">→ 0%</div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-icon">💰</div>
                    <div class="stat-value">68Jt</div>
                    <div class="stat-label">Pendapatan Bulan Ini</div>
                    <div class="stat-change down">↓ 3%</div>
                </div>
            </div>

            <div class="two-col">
                <!-- Chart -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Kunjungan Pasien — 2026</div>
                        <a class="card-action">Lihat detail →</a>
                    </div>
                    <div class="chart-area">
                        <div class="chart-bars">
                            <div class="chart-bar-wrap"><div class="chart-bar" style="height:55%"></div><div class="chart-label">Jan</div></div>
                            <div class="chart-bar-wrap"><div class="chart-bar" style="height:70%"></div><div class="chart-label">Feb</div></div>
                            <div class="chart-bar-wrap"><div class="chart-bar" style="height:62%"></div><div class="chart-label">Mar</div></div>
                            <div class="chart-bar-wrap"><div class="chart-bar" style="height:88%"></div><div class="chart-label">Apr</div></div>
                            <div class="chart-bar-wrap"><div class="chart-bar" style="height:95%"></div><div class="chart-label">Mei</div></div>
                            <div class="chart-bar-wrap"><div class="chart-bar" style="height:40%"></div><div class="chart-label">Jun</div></div>
                        </div>
                        <div class="chart-legend">
                            <div class="legend-item"><div class="legend-dot"></div> Kunjungan</div>
                            <div class="legend-item"><div class="legend-dot green"></div> Target</div>
                        </div>
                    </div>
                </div>

                <!-- Aktivitas terkini -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Aktivitas Terkini</div>
                        <a class="card-action" onclick="showPanel('aktivitas', document.querySelector('[onclick*=aktivitas]'))">Semua →</a>
                    </div>
                    <div class="activity-list">
                        <div class="activity-item">
                            <div class="activity-dot"></div>
                            <div>
                                <div class="activity-text"><strong>Siti Rahayu</strong> mendaftar pasien baru</div>
                                <div class="activity-time">5 menit lalu</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-dot green"></div>
                            <div>
                                <div class="activity-text"><strong>Dr. Michael Chen</strong> konfirmasi jadwal Senin</div>
                                <div class="activity-time">18 menit lalu</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-dot orange"></div>
                            <div>
                                <div class="activity-text"><strong>Budi Santoso</strong> membatalkan appointment</div>
                                <div class="activity-time">42 menit lalu</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-dot"></div>
                            <div>
                                <div class="activity-text"><strong>Dr. Anisa Putri</strong> menyelesaikan treatment Facelift</div>
                                <div class="activity-time">1 jam lalu</div>
                            </div>
                        </div>
                        <div class="activity-item">
                            <div class="activity-dot green"></div>
                            <div>
                                <div class="activity-text"><strong>3 pasien baru</strong> menyelesaikan pembayaran</div>
                                <div class="activity-time">2 jam lalu</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appointment hari ini -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Appointment Hari Ini</div>
                    <a class="card-action" onclick="showPanel('jadwal', document.querySelector('[onclick*=jadwal]'))">Kelola Jadwal →</a>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Pasien</th>
                            <th>Dokter</th>
                            <th>Treatment</th>
                            <th>Jam</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="avatar">S</span><span class="td-name">Siti Rahayu</span></td>
                            <td>Dr. Anisa Putri</td>
                            <td>Facelift Consultation</td>
                            <td class="td-jam">09.00</td>
                            <td><span class="badge badge-green">Selesai</span></td>
                        </tr>
                        <tr>
                            <td><span class="avatar">A</span><span class="td-name">Andini Kusuma</span></td>
                            <td>Dr. Michael Chen</td>
                            <td>Laser Treatment</td>
                            <td class="td-jam">10.30</td>
                            <td><span class="badge badge-yellow">Berlangsung</span></td>
                        </tr>
                        <tr>
                            <td><span class="avatar">R</span><span class="td-name">Rina Wulandari</span></td>
                            <td>Dr. Marina Crystine</td>
                            <td>Botox & Fillers</td>
                            <td class="td-jam">13.00</td>
                            <td><span class="badge badge-pink">Menunggu</span></td>
                        </tr>
                        <tr>
                            <td><span class="avatar">D</span><span class="td-name">Dewi Anggraini</span></td>
                            <td>Dr. Anisa Putri</td>
                            <td>Body Contouring</td>
                            <td class="td-jam">15.00</td>
                            <td><span class="badge badge-pink">Menunggu</span></td>
                        </tr>
                        <tr>
                            <td><span class="avatar">M</span><span class="td-name">Maya Sari</span></td>
                            <td>Dr. Michael Chen</td>
                            <td>Botox</td>
                            <td class="td-jam">16.30</td>
                            <td><span class="badge badge-gray">Terjadwal</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ══ PANEL: PASIEN ══ -->
        <div class="panel" id="panel-pasien">
            <div class="page-header">
                <div>
                    <h2 class="section-title">Data <em>Pasien</em></h2>
                    <p class="section-sub">Kelola seluruh data pasien terdaftar di GlowCare Clinic</p>
                </div>
                <button class="btn-add" onclick="openModal('modal-pasien')">+ Tambah Pasien</button>
            </div>

            <div class="filter-bar">
                <input class="filter-input" type="text" placeholder="🔍 Cari nama, nomor...">
                <select class="filter-select">
                    <option>Semua Treatment</option>
                    <option>Facelift</option>
                    <option>Botox & Fillers</option>
                    <option>Laser Treatment</option>
                    <option>Body Contouring</option>
                </select>
                <select class="filter-select">
                    <option>Semua Status</option>
                    <option>Aktif</option>
                    <option>Tidak Aktif</option>
                </select>
            </div>

            <div class="card">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Pasien</th>
                            <th>Kontak</th>
                            <th>Treatment Terakhir</th>
                            <th>Kunjungan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="color:#b89098; font-size:11px">#P-0041</td>
                            <td><span class="avatar">S</span><div style="display:inline-block"><span class="td-name">Siti Rahayu</span><span class="td-sub">28 Tahun · Perempuan</span></div></td>
                            <td><div style="font-size:13px">+62 812 1234 5678</div><div style="font-size:11px;color:#b89098">siti@email.com</div></td>
                            <td>Facelift Consultation</td>
                            <td style="text-align:center">12</td>
                            <td><span class="badge badge-green">Aktif</span></td>
                            <td>
                                <button class="act-btn" title="Edit" onclick="openModal('modal-pasien')">✏️</button>
                                <button class="act-btn" title="Detail">👁️</button>
                                <button class="act-btn" title="Hapus">🗑️</button>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#b89098; font-size:11px">#P-0042</td>
                            <td><span class="avatar">A</span><div style="display:inline-block"><span class="td-name">Andini Kusuma</span><span class="td-sub">32 Tahun · Perempuan</span></div></td>
                            <td><div style="font-size:13px">+62 813 9876 5432</div><div style="font-size:11px;color:#b89098">andini@email.com</div></td>
                            <td>Laser Treatment</td>
                            <td style="text-align:center">7</td>
                            <td><span class="badge badge-green">Aktif</span></td>
                            <td>
                                <button class="act-btn" onclick="openModal('modal-pasien')">✏️</button>
                                <button class="act-btn">👁️</button>
                                <button class="act-btn">🗑️</button>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#b89098; font-size:11px">#P-0043</td>
                            <td><span class="avatar">B</span><div style="display:inline-block"><span class="td-name">Budi Santoso</span><span class="td-sub">45 Tahun · Laki-laki</span></div></td>
                            <td><div style="font-size:13px">+62 819 1111 2222</div><div style="font-size:11px;color:#b89098">budi@email.com</div></td>
                            <td>Body Contouring</td>
                            <td style="text-align:center">3</td>
                            <td><span class="badge badge-gray">Tidak Aktif</span></td>
                            <td>
                                <button class="act-btn" onclick="openModal('modal-pasien')">✏️</button>
                                <button class="act-btn">👁️</button>
                                <button class="act-btn">🗑️</button>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#b89098; font-size:11px">#P-0044</td>
                            <td><span class="avatar">R</span><div style="display:inline-block"><span class="td-name">Rina Wulandari</span><span class="td-sub">25 Tahun · Perempuan</span></div></td>
                            <td><div style="font-size:13px">+62 857 3333 4444</div><div style="font-size:11px;color:#b89098">rina@email.com</div></td>
                            <td>Botox & Fillers</td>
                            <td style="text-align:center">5</td>
                            <td><span class="badge badge-green">Aktif</span></td>
                            <td>
                                <button class="act-btn" onclick="openModal('modal-pasien')">✏️</button>
                                <button class="act-btn">👁️</button>
                                <button class="act-btn">🗑️</button>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#b89098; font-size:11px">#P-0045</td>
                            <td><span class="avatar">D</span><div style="display:inline-block"><span class="td-name">Dewi Anggraini</span><span class="td-sub">38 Tahun · Perempuan</span></div></td>
                            <td><div style="font-size:13px">+62 878 5555 6666</div><div style="font-size:11px;color:#b89098">dewi@email.com</div></td>
                            <td>Body Contouring</td>
                            <td style="text-align:center">9</td>
                            <td><span class="badge badge-green">Aktif</span></td>
                            <td>
                                <button class="act-btn" onclick="openModal('modal-pasien')">✏️</button>
                                <button class="act-btn">👁️</button>
                                <button class="act-btn">🗑️</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div style="padding:16px 20px; border-top:1px solid #fdf0f5; display:flex; justify-content:space-between; align-items:center; font-size:12px; color:#b89098">
                    <span>Menampilkan 1–5 dari 1.248 pasien</span>
                    <div style="display:flex; gap:8px">
                        <button class="act-btn">← Prev</button>
                        <button class="act-btn" style="background:#c55085;color:#fff;border-radius:4px;padding:4px 10px">1</button>
                        <button class="act-btn">2</button>
                        <button class="act-btn">3</button>
                        <button class="act-btn">Next →</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ PANEL: DOKTER ══ -->
        <div class="panel" id="panel-dokter">
            <div class="page-header">
                <div>
                    <h2 class="section-title">Data <em>Dokter</em></h2>
                    <p class="section-sub">Kelola profil dan spesialisasi seluruh dokter klinik</p>
                </div>
                <button class="btn-add" onclick="openModal('modal-dokter')">+ Tambah Dokter</button>
            </div>

            <div class="card">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Dokter</th>
                            <th>Spesialisasi</th>
                            <th>Pengalaman</th>
                            <th>Total Pasien</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="color:#b89098; font-size:11px">#D-001</td>
                            <td><span class="avatar">A</span><span class="td-name">Dr. Anisa Putri</span></td>
                            <td><span class="badge badge-pink">Plastic Surgeon</span></td>
                            <td>10+ Tahun</td>
                            <td style="text-align:center">412</td>
                            <td>⭐ 5.0</td>
                            <td><span class="badge badge-green">Aktif</span></td>
                            <td>
                                <button class="act-btn" onclick="openModal('modal-dokter')">✏️</button>
                                <button class="act-btn">👁️</button>
                                <button class="act-btn">🗑️</button>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#b89098; font-size:11px">#D-002</td>
                            <td><span class="avatar">M</span><span class="td-name">Dr. Marina Crystine</span></td>
                            <td><span class="badge badge-pink">Aesthetic Physician</span></td>
                            <td>8 Tahun</td>
                            <td style="text-align:center">387</td>
                            <td>⭐ 5.0</td>
                            <td><span class="badge badge-green">Aktif</span></td>
                            <td>
                                <button class="act-btn" onclick="openModal('modal-dokter')">✏️</button>
                                <button class="act-btn">👁️</button>
                                <button class="act-btn">🗑️</button>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#b89098; font-size:11px">#D-003</td>
                            <td><span class="avatar">M</span><span class="td-name">Dr. Michael Chen</span></td>
                            <td><span class="badge badge-pink">Dermatologist</span></td>
                            <td>12 Tahun</td>
                            <td style="text-align:center">449</td>
                            <td>⭐ 5.0</td>
                            <td><span class="badge badge-green">Aktif</span></td>
                            <td>
                                <button class="act-btn" onclick="openModal('modal-dokter')">✏️</button>
                                <button class="act-btn">👁️</button>
                                <button class="act-btn">🗑️</button>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#b89098; font-size:11px">#D-004</td>
                            <td><span class="avatar">S</span><span class="td-name">Dr. Sarah Dewi</span></td>
                            <td><span class="badge badge-pink">Aesthetic Physician</span></td>
                            <td>5 Tahun</td>
                            <td style="text-align:center">198</td>
                            <td>⭐ 4.8</td>
                            <td><span class="badge badge-yellow">Cuti</span></td>
                            <td>
                                <button class="act-btn" onclick="openModal('modal-dokter')">✏️</button>
                                <button class="act-btn">👁️</button>
                                <button class="act-btn">🗑️</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ══ PANEL: JADWAL ══ -->
        <div class="panel" id="panel-jadwal">
            <div class="page-header">
                <div>
                    <h2 class="section-title">Jadwal <em>Dokter</em></h2>
                    <p class="section-sub">Atur dan pantau jadwal praktik seluruh dokter klinik</p>
                </div>
                <button class="btn-add" onclick="openModal('modal-jadwal')">+ Tambah Jadwal</button>
            </div>

            <!-- Week nav -->
            <div style="display:flex; align-items:center; gap:16px; margin-bottom:20px">
                <button class="act-btn">← Minggu Lalu</button>
                <span style="font-family:'Playfair Display',serif; font-size:16px; color:#3d1a22">28 Apr – 03 Mei 2026</span>
                <button class="act-btn">Minggu Depan →</button>
                <button class="btn-add" style="margin-left:auto; background:transparent; color:#c55085; border:1px solid #f2c4ce; padding:8px 18px; font-size:11px">📋 Tampilan List</button>
            </div>

            <div class="schedule-week">
                <!-- Header -->
                <div class="sch-head">Jam</div>
                <div class="sch-head">Senin<br><span style="font-size:11px; color:#c55085">28 Apr</span></div>
                <div class="sch-head">Selasa<br><span style="font-size:11px">29 Apr</span></div>
                <div class="sch-head">Rabu<br><span style="font-size:11px">30 Apr</span></div>
                <div class="sch-head">Kamis<br><span style="font-size:11px">01 Mei</span></div>
                <div class="sch-head">Jumat<br><span style="font-size:11px">02 Mei</span></div>
                <div class="sch-head">Sabtu<br><span style="font-size:11px">03 Mei</span></div>

                <!-- 09:00 -->
                <div class="sch-time">09:00</div>
                <div class="sch-cell"><div class="sch-event">Dr. Anisa<br>Facelift</div></div>
                <div class="sch-cell"></div>
                <div class="sch-cell"><div class="sch-event teal">Dr. Marina<br>Botox</div></div>
                <div class="sch-cell"></div>
                <div class="sch-cell"><div class="sch-event">Dr. Michael<br>Laser</div></div>
                <div class="sch-cell"></div>

                <!-- 10:30 -->
                <div class="sch-time">10:30</div>
                <div class="sch-cell"></div>
                <div class="sch-cell"><div class="sch-event purple">Dr. Michael<br>Dermatologi</div></div>
                <div class="sch-cell"></div>
                <div class="sch-cell"><div class="sch-event">Dr. Anisa<br>Rhinoplasty</div></div>
                <div class="sch-cell"></div>
                <div class="sch-cell"><div class="sch-event teal">Dr. Marina<br>Thread</div></div>

                <!-- 13:00 -->
                <div class="sch-time">13:00</div>
                <div class="sch-cell"><div class="sch-event orange">Dr. Marina<br>CoolSculpting</div></div>
                <div class="sch-cell"></div>
                <div class="sch-cell"><div class="sch-event purple">Dr. Michael<br>Botox</div></div>
                <div class="sch-cell"><div class="sch-event">Dr. Anisa<br>Konsul</div></div>
                <div class="sch-cell"></div>
                <div class="sch-cell"></div>

                <!-- 15:00 -->
                <div class="sch-time">15:00</div>
                <div class="sch-cell"></div>
                <div class="sch-cell"><div class="sch-event">Dr. Anisa<br>Body Cont.</div></div>
                <div class="sch-cell"></div>
                <div class="sch-cell"></div>
                <div class="sch-cell"><div class="sch-event teal">Dr. Marina<br>Ultherapy</div></div>
                <div class="sch-cell"><div class="sch-event purple">Dr. Michael<br>Laser</div></div>
            </div>

            <!-- Jadwal list -->
            <div class="card">
                <div class="card-header"><div class="card-title">Semua Jadwal Aktif</div></div>
                <table class="data-table">
                    <thead>
                        <tr><th>Dokter</th><th>Hari</th><th>Jam Mulai</th><th>Jam Selesai</th><th>Treatment</th><th>Slot Tersisa</th><th>Status</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="td-name">Dr. Anisa Putri</span></td>
                            <td>Senin, Rabu, Jumat</td>
                            <td class="td-jam">09:00</td>
                            <td class="td-jam">17:00</td>
                            <td>Facelift / Rhinoplasty</td>
                            <td style="text-align:center; color:#c55085">3 / 6</td>
                            <td><span class="badge badge-green">Aktif</span></td>
                            <td><button class="act-btn" onclick="openModal('modal-jadwal')">✏️</button><button class="act-btn">🗑️</button></td>
                        </tr>
                        <tr>
                            <td><span class="td-name">Dr. Marina Crystine</span></td>
                            <td>Selasa, Kamis, Sabtu</td>
                            <td class="td-jam">10:00</td>
                            <td class="td-jam">18:00</td>
                            <td>Botox / CoolSculpting</td>
                            <td style="text-align:center; color:#c55085">5 / 8</td>
                            <td><span class="badge badge-green">Aktif</span></td>
                            <td><button class="act-btn" onclick="openModal('modal-jadwal')">✏️</button><button class="act-btn">🗑️</button></td>
                        </tr>
                        <tr>
                            <td><span class="td-name">Dr. Michael Chen</span></td>
                            <td>Senin – Jumat</td>
                            <td class="td-jam">09:00</td>
                            <td class="td-jam">16:00</td>
                            <td>Laser / Dermatologi</td>
                            <td style="text-align:center; color:#6dbf9e">7 / 10</td>
                            <td><span class="badge badge-green">Aktif</span></td>
                            <td><button class="act-btn" onclick="openModal('modal-jadwal')">✏️</button><button class="act-btn">🗑️</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ══ PANEL: AKTIVITAS ══ -->
        <div class="panel" id="panel-aktivitas">
            <h2 class="section-title">Log <em>Aktivitas</em></h2>
            <p class="section-sub">Pantau seluruh aktivitas dan kejadian dalam sistem</p>

            <div class="stats-row" style="grid-template-columns: repeat(3,1fr)">
                <div class="stat-card">
                    <div class="stat-icon">📋</div>
                    <div class="stat-value">128</div>
                    <div class="stat-label">Aktivitas Hari Ini</div>
                </div>
                <div class="stat-card green">
                    <div class="stat-icon">✅</div>
                    <div class="stat-value">34</div>
                    <div class="stat-label">Appointment Selesai</div>
                </div>
                <div class="stat-card orange">
                    <div class="stat-icon">⚠️</div>
                    <div class="stat-value">3</div>
                    <div class="stat-label">Pembatalan</div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">Riwayat Aktivitas Sistem</div>
                    <div style="display:flex; gap:8px">
                        <select class="filter-select" style="font-size:11px; padding:6px 14px">
                            <option>Semua Tipe</option>
                            <option>Pasien</option>
                            <option>Dokter</option>
                            <option>Pembayaran</option>
                        </select>
                    </div>
                </div>
                <div style="padding: 0 24px 24px">
                    <div class="activity-item">
                        <div class="activity-dot"></div>
                        <div style="flex:1">
                            <div class="activity-text"><strong>Pasien Baru</strong> — Siti Rahayu mendaftar sebagai pasien baru (ID: #P-0041)</div>
                            <div class="activity-time">Hari ini, 08:14 · Admin</div>
                        </div>
                        <span class="badge badge-pink">Pasien</span>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot green"></div>
                        <div style="flex:1">
                            <div class="activity-text"><strong>Appointment Selesai</strong> — Dr. Anisa Putri menyelesaikan sesi Facelift untuk Siti Rahayu</div>
                            <div class="activity-time">Hari ini, 09:55 · Sistem</div>
                        </div>
                        <span class="badge badge-green">Treatment</span>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot orange"></div>
                        <div style="flex:1">
                            <div class="activity-text"><strong>Pembatalan</strong> — Budi Santoso membatalkan appointment pukul 11:00 (Body Contouring)</div>
                            <div class="activity-time">Hari ini, 10:22 · Pasien</div>
                        </div>
                        <span class="badge badge-yellow">Pembatalan</span>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot"></div>
                        <div style="flex:1">
                            <div class="activity-text"><strong>Pembayaran Diterima</strong> — Rp 2.500.000 dari Andini Kusuma untuk Laser Treatment</div>
                            <div class="activity-time">Hari ini, 10:48 · Kasir</div>
                        </div>
                        <span class="badge badge-green">Pembayaran</span>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot"></div>
                        <div style="flex:1">
                            <div class="activity-text"><strong>Jadwal Diperbarui</strong> — Dr. Marina Crystine mengkonfirmasi jadwal Sabtu 03 Mei</div>
                            <div class="activity-time">Hari ini, 11:03 · Admin</div>
                        </div>
                        <span class="badge badge-pink">Jadwal</span>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot green"></div>
                        <div style="flex:1">
                            <div class="activity-text"><strong>Appointment Selesai</strong> — Dr. Michael Chen menyelesaikan sesi Dermatologi untuk Andini Kusuma</div>
                            <div class="activity-time">Hari ini, 11:30 · Sistem</div>
                        </div>
                        <span class="badge badge-green">Treatment</span>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot"></div>
                        <div style="flex:1">
                            <div class="activity-text"><strong>Data Dokter Diperbarui</strong> — Profil Dr. Sarah Dewi diperbarui (status: Cuti)</div>
                            <div class="activity-time">Hari ini, 12:10 · Admin</div>
                        </div>
                        <span class="badge badge-yellow">Dokter</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══ PANEL: LAPORAN ══ -->
        <div class="panel" id="panel-laporan">
            <h2 class="section-title">Laporan <em>Data</em></h2>
            <p class="section-sub">Generate dan unduh laporan lengkap data klinik</p>

            <div class="report-grid">
                <div class="report-card">
                    <div class="report-icon">👥</div>
                    <div class="report-name">Laporan Pasien</div>
                    <div class="report-desc">Data lengkap seluruh pasien terdaftar, riwayat kunjungan, dan treatment yang pernah dilakukan.</div>
                    <a class="report-link" onclick="showToast('Mengunduh laporan pasien...')">Unduh Laporan →</a>
                </div>
                <div class="report-card">
                    <div class="report-icon">🩺</div>
                    <div class="report-name">Laporan Dokter</div>
                    <div class="report-desc">Performa dokter, jumlah pasien ditangani, rating, dan jadwal kerja dalam periode tertentu.</div>
                    <a class="report-link" onclick="showToast('Mengunduh laporan dokter...')">Unduh Laporan →</a>
                </div>
                <div class="report-card">
                    <div class="report-icon">💰</div>
                    <div class="report-name">Laporan Keuangan</div>
                    <div class="report-desc">Pendapatan bulanan, rincian pembayaran, dan tren keuangan klinik per treatment.</div>
                    <a class="report-link" onclick="showToast('Mengunduh laporan keuangan...')">Unduh Laporan →</a>
                </div>
                <div class="report-card">
                    <div class="report-icon">📅</div>
                    <div class="report-name">Laporan Jadwal</div>
                    <div class="report-desc">Rekap appointment, pembatalan, dan tingkat kehadiran pasien per dokter per bulan.</div>
                    <a class="report-link" onclick="showToast('Mengunduh laporan jadwal...')">Unduh Laporan →</a>
                </div>
                <div class="report-card">
                    <div class="report-icon">💉</div>
                    <div class="report-name">Laporan Treatment</div>
                    <div class="report-desc">Treatment terpopuler, statistik keberhasilan, dan feedback pasien per kategori layanan.</div>
                    <a class="report-link" onclick="showToast('Mengunduh laporan treatment...')">Unduh Laporan →</a>
                </div>
                <div class="report-card">
                    <div class="report-icon">🔔</div>
                    <div class="report-name">Laporan Aktivitas</div>
                    <div class="report-desc">Log lengkap aktivitas sistem, tindakan admin, dan catatan kejadian penting dalam klinik.</div>
                    <a class="report-link" onclick="showToast('Mengunduh laporan aktivitas...')">Unduh Laporan →</a>
                </div>
            </div>

            <!-- Summary table -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Ringkasan Bulanan — Mei 2026</div>
                    <div style="display:flex; gap:8px">
                        <select class="filter-select" style="font-size:11px; padding:6px 14px">
                            <option>Mei 2026</option>
                            <option>Apr 2026</option>
                            <option>Mar 2026</option>
                        </select>
                        <button class="btn-add" style="font-size:10px; padding:8px 16px" onclick="showToast('Mengekspor ke PDF...')">Export PDF</button>
                    </div>
                </div>
                <table class="data-table">
                    <thead>
                        <tr><th>Dokter</th><th>Total Pasien</th><th>Appointment</th><th>Pembatalan</th><th>Pendapatan</th><th>Rating Avg</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="td-name">Dr. Anisa Putri</span></td>
                            <td style="text-align:center">86</td>
                            <td style="text-align:center">92</td>
                            <td style="text-align:center; color:#c55085">6</td>
                            <td>Rp 28.500.000</td>
                            <td>⭐ 5.0</td>
                        </tr>
                        <tr>
                            <td><span class="td-name">Dr. Marina Crystine</span></td>
                            <td style="text-align:center">74</td>
                            <td style="text-align:center">78</td>
                            <td style="text-align:center; color:#c55085">4</td>
                            <td>Rp 21.000.000</td>
                            <td>⭐ 5.0</td>
                        </tr>
                        <tr>
                            <td><span class="td-name">Dr. Michael Chen</span></td>
                            <td style="text-align:center">92</td>
                            <td style="text-align:center">96</td>
                            <td style="text-align:center; color:#c55085">4</td>
                            <td>Rp 18.500.000</td>
                            <td>⭐ 5.0</td>
                        </tr>
                        <tr style="background:#fdf0f5; font-weight:500">
                            <td><strong>Total</strong></td>
                            <td style="text-align:center"><strong>252</strong></td>
                            <td style="text-align:center"><strong>266</strong></td>
                            <td style="text-align:center; color:#c55085"><strong>14</strong></td>
                            <td><strong>Rp 68.000.000</strong></td>
                            <td><strong>⭐ 5.0</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

<div class="modal-overlay" id="modal-pasien" onclick="closeModalOutside(event,'modal-pasien')">
    <div class="modal" style="position:relative">
        <h3 class="modal-title">Tambah / Edit <em>Pasien</em></h3>
        <p class="modal-sub">Isi data pasien dengan lengkap dan benar</p>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Nama Lengkap</label><input class="form-input" placeholder="Nama lengkap pasien"></div>
            <div class="form-group"><label class="form-label">Tanggal Lahir</label><input class="form-input" type="date"></div>
            <div class="form-group"><label class="form-label">Jenis Kelamin</label><select class="form-select"><option>Perempuan</option><option>Laki-laki</option></select></div>
            <div class="form-group"><label class="form-label">No. Telepon</label><input class="form-input" placeholder="+62"></div>
            <div class="form-group full"><label class="form-label">Email</label><input class="form-input" placeholder="email@contoh.com"></div>
            <div class="form-group full"><label class="form-label">Alamat</label><input class="form-input" placeholder="Alamat lengkap"></div>
            <div class="form-group"><label class="form-label">Treatment Pilihan</label><select class="form-select"><option>Facelift</option><option>Botox & Fillers</option><option>Laser Treatment</option><option>Body Contouring</option></select></div>
            <div class="form-group"><label class="form-label">Status</label><select class="form-select"><option>Aktif</option><option>Tidak Aktif</option></select></div>
            <div class="form-group full"><label class="form-label">Catatan Medis</label><textarea class="form-textarea" placeholder="Alergi, kondisi khusus, catatan lainnya..."></textarea></div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal('modal-pasien')">Batal</button>
            <button class="btn-save" onclick="closeModal('modal-pasien'); showToast('Data pasien berhasil disimpan ✓')">Simpan Data</button>
        </div>
    </div>
</div>

<!-- ══ MODAL: DOKTER ══ -->
<div class="modal-overlay" id="modal-dokter" onclick="closeModalOutside(event,'modal-dokter')">
    <div class="modal" style="position:relative">
        <h3 class="modal-title">Tambah / Edit <em>Dokter</em></h3>
        <p class="modal-sub">Lengkapi profil dan spesialisasi dokter</p>
        <div class="form-row">
            <div class="form-group"><label class="form-label">Nama Lengkap</label><input class="form-input" placeholder="Dr. Nama Lengkap"></div>
            <div class="form-group"><label class="form-label">No. STR / SIP</label><input class="form-input" placeholder="Nomor lisensi"></div>
            <div class="form-group full"><label class="form-label">Spesialisasi</label><select class="form-select"><option>Plastic Surgeon</option><option>Aesthetic Physician</option><option>Dermatologist</option></select></div>
            <div class="form-group"><label class="form-label">No. Telepon</label><input class="form-input" placeholder="+62"></div>
            <div class="form-group"><label class="form-label">Email</label><input class="form-input" placeholder="dokter@glowcare.com"></div>
            <div class="form-group"><label class="form-label">Pengalaman (Tahun)</label><input class="form-input" type="number" placeholder="0"></div>
            <div class="form-group"><label class="form-label">Status</label><select class="form-select"><option>Aktif</option><option>Cuti</option><option>Tidak Aktif</option></select></div>
            <div class="form-group full"><label class="form-label">Bio Singkat</label><textarea class="form-textarea" placeholder="Deskripsi singkat tentang dokter..."></textarea></div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal('modal-dokter')">Batal</button>
            <button class="btn-save" onclick="closeModal('modal-dokter'); showToast('Data dokter berhasil disimpan ✓')">Simpan Data</button>
        </div>
    </div>
</div>

<!-- ══ MODAL: JADWAL ══ -->
<div class="modal-overlay" id="modal-jadwal" onclick="closeModalOutside(event,'modal-jadwal')">
    <div class="modal" style="position:relative">
        <h3 class="modal-title">Tambah / Edit <em>Jadwal</em></h3>
        <p class="modal-sub">Atur jadwal praktik dokter</p>
        <div class="form-row">
            <div class="form-group full"><label class="form-label">Dokter</label><select class="form-select"><option>Dr. Anisa Putri</option><option>Dr. Marina Crystine</option><option>Dr. Michael Chen</option></select></div>
            <div class="form-group full"><label class="form-label">Hari Praktik</label>
                <div style="display:flex; flex-wrap:wrap; gap:8px; margin-top:4px">
                    <label style="font-size:12px; display:flex; gap:4px; align-items:center"><input type="checkbox"> Sen</label>
                    <label style="font-size:12px; display:flex; gap:4px; align-items:center"><input type="checkbox"> Sel</label>
                    <label style="font-size:12px; display:flex; gap:4px; align-items:center"><input type="checkbox"> Rab</label>
                    <label style="font-size:12px; display:flex; gap:4px; align-items:center"><input type="checkbox"> Kam</label>
                    <label style="font-size:12px; display:flex; gap:4px; align-items:center"><input type="checkbox"> Jum</label>
                    <label style="font-size:12px; display:flex; gap:4px; align-items:center"><input type="checkbox"> Sab</label>
                    <label style="font-size:12px; display:flex; gap:4px; align-items:center"><input type="checkbox"> Min</label>
                </div>
            </div>
            <div class="form-group"><label class="form-label">Jam Mulai</label><input class="form-input" type="time" value="09:00"></div>
            <div class="form-group"><label class="form-label">Jam Selesai</label><input class="form-input" type="time" value="17:00"></div>
            <div class="form-group"><label class="form-label">Max Pasien/Hari</label><input class="form-input" type="number" placeholder="8"></div>
            <div class="form-group"><label class="form-label">Treatment</label><select class="form-select"><option>Semua</option><option>Facelift</option><option>Botox & Fillers</option><option>Laser Treatment</option><option>Body Contouring</option></select></div>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal('modal-jadwal')">Batal</button>
            <button class="btn-save" onclick="closeModal('modal-jadwal'); showToast('Jadwal berhasil disimpan ✓')">Simpan Jadwal</button>
        </div>
    </div>
</div>


        <!-- ══ PANEL: TREATMENT ══ -->
        <div class="panel" id="panel-treatment">
            <div class="page-header">
                <div>
                    <h2 class="section-title">Kelola <em>Treatment</em></h2>
                    <p class="section-sub">Data treatment yang akan ditampilkan di halaman utama (index)</p>
                </div>
                <button class="btn-add" onclick="openModal('modal-treatment')">+ Tambah Treatment</button>
            </div>

            <!-- Info banner -->
            <div style="background:linear-gradient(135deg,#fde8f2,#fdf0f5); border:1px solid #f2c4ce; border-radius:12px; padding:16px 20px; margin-bottom:24px; display:flex; align-items:center; gap:14px">
                <span style="font-size:22px">💡</span>
                <div>
                    <div style="font-size:13px; font-weight:500; color:#3d1a22; margin-bottom:3px">Data treatment ini ditampilkan langsung di halaman index</div>
                    <div style="font-size:12px; color:#7a4d5c; font-weight:300">Perubahan yang disimpan akan langsung memperbarui tampilan di landing page GlowCare Clinic.</div>
                </div>
            </div>

            <!-- Treatment cards grid (preview seperti di index) -->
            <div class="card" style="margin-bottom:24px">
                <div class="card-header">
                    <div class="card-title">Preview <em>Tampilan Index</em></div>
                    <div style="display:flex; gap:8px; align-items:center">
                        <span style="font-size:11px; color:#b89098">4 treatment aktif</span>
                        <a href="../index.html" target="_blank" class="card-action">Buka Index →</a>
                    </div>
                </div>
                <div style="padding:0 24px 24px; display:grid; grid-template-columns:repeat(4,1fr); gap:16px">

                    <!-- Card 1 -->
                    <div style="background:#fdf0f5; border-radius:10px; overflow:hidden; border:1px solid #f2c4ce">
                        <div style="position:relative; aspect-ratio:4/3; overflow:hidden">
                            <img src="https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=400&auto=format&fit=crop&q=80" style="width:100%; height:100%; object-fit:cover">
                            <div style="position:absolute; inset:0; background:linear-gradient(to top,rgba(197,80,133,0.18),transparent)"></div>
                            <div style="position:absolute; top:8px; left:8px; background:#fff; border-radius:50px; padding:3px 10px; font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:#b89098">Surgical</div>
                        </div>
                        <div style="padding:14px 16px">
                            <div style="font-family:'Playfair Display',serif; font-size:14px; color:#c55085; margin-bottom:4px">Facelift Procedures</div>
                            <div style="font-size:11px; color:#7a4d5c; font-weight:300; line-height:1.6">Teknik bedah canggih untuk memulihkan kontur wajah.</div>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div style="background:#fdf0f5; border-radius:10px; overflow:hidden; border:1px solid #f2c4ce">
                        <div style="position:relative; aspect-ratio:4/3; overflow:hidden">
                            <img src="https://images.unsplash.com/photo-1512290923902-8a9f81dc236c?w=400&auto=format&fit=crop&q=80" style="width:100%; height:100%; object-fit:cover">
                            <div style="position:absolute; inset:0; background:linear-gradient(to top,rgba(197,80,133,0.18),transparent)"></div>
                            <div style="position:absolute; top:8px; left:8px; background:#fff; border-radius:50px; padding:3px 10px; font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:#b89098">Injectable</div>
                        </div>
                        <div style="padding:14px 16px">
                            <div style="font-family:'Playfair Display',serif; font-size:14px; color:#c55085; margin-bottom:4px">Botox & Fillers</div>
                            <div style="font-size:11px; color:#7a4d5c; font-weight:300; line-height:1.6">Perawatan suntik untuk menghaluskan kerutan wajah.</div>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div style="background:#fdf0f5; border-radius:10px; overflow:hidden; border:1px solid #f2c4ce">
                        <div style="position:relative; aspect-ratio:4/3; overflow:hidden">
                            <img src="https://images.unsplash.com/photo-1515377905703-c4788e51af15?w=400&auto=format&fit=crop&q=80" style="width:100%; height:100%; object-fit:cover">
                            <div style="position:absolute; inset:0; background:linear-gradient(to top,rgba(197,80,133,0.18),transparent)"></div>
                            <div style="position:absolute; top:8px; left:8px; background:#fff; border-radius:50px; padding:3px 10px; font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:#b89098">Technology</div>
                        </div>
                        <div style="padding:14px 16px">
                            <div style="font-family:'Playfair Display',serif; font-size:14px; color:#c55085; margin-bottom:4px">Laser Treatments</div>
                            <div style="font-size:11px; color:#7a4d5c; font-weight:300; line-height:1.6">Teknologi laser untuk pigmentasi & tanda penuaan.</div>
                        </div>
                    </div>

                    <!-- Card 4 -->
                    <div style="background:#fdf0f5; border-radius:10px; overflow:hidden; border:1px solid #f2c4ce">
                        <div style="position:relative; aspect-ratio:4/3; overflow:hidden">
                            <img src="https://images.unsplash.com/photo-1607619056574-7b8d3ee536b2?w=400&auto=format&fit=crop&q=80" style="width:100%; height:100%; object-fit:cover">
                            <div style="position:absolute; inset:0; background:linear-gradient(to top,rgba(197,80,133,0.18),transparent)"></div>
                            <div style="position:absolute; top:8px; left:8px; background:#fff; border-radius:50px; padding:3px 10px; font-size:9px; letter-spacing:1.5px; text-transform:uppercase; color:#b89098">Contouring</div>
                        </div>
                        <div style="padding:14px 16px">
                            <div style="font-family:'Playfair Display',serif; font-size:14px; color:#c55085; margin-bottom:4px">Body Contouring</div>
                            <div style="font-size:11px; color:#7a4d5c; font-weight:300; line-height:1.6">Membentuk & merampingkan tubuh dengan presisi.</div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Management table -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Daftar <em>Treatment</em></div>
                    <div style="display:flex; gap:8px">
                        <select class="filter-select" style="font-size:11px; padding:6px 14px">
                            <option>Semua Kategori</option>
                            <option>Surgical</option>
                            <option>Injectable</option>
                            <option>Technology</option>
                            <option>Contouring</option>
                        </select>
                        <select class="filter-select" style="font-size:11px; padding:6px 14px">
                            <option>Semua Status</option>
                            <option>Aktif</option>
                            <option>Nonaktif</option>
                        </select>
                    </div>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Nama Treatment</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Urutan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="color:#b89098; font-size:11px">1</td>
                            <td>
                                <img src="https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=100&auto=format&fit=crop&q=80"
                                    style="width:52px; height:40px; object-fit:cover; border-radius:6px; border:1px solid #f2c4ce">
                            </td>
                            <td>
                                <div class="td-name">Facelift Procedures</div>
                                <div class="td-sub">pages/treatment/facelift.php</div>
                            </td>
                            <td><span class="badge badge-pink">Surgical</span></td>
                            <td style="max-width:200px; font-size:12px; color:#7a4d5c; font-weight:300">Teknik bedah canggih untuk memulihkan kontur wajah yang tampak muda.</td>
                            <td>
                                <div style="display:flex; align-items:center; gap:4px">
                                    <button class="act-btn" style="font-size:12px; padding:2px 6px">↑</button>
                                    <span style="font-size:13px; font-weight:500; color:#3d1a22; min-width:16px; text-align:center">1</span>
                                    <button class="act-btn" style="font-size:12px; padding:2px 6px">↓</button>
                                </div>
                            </td>
                            <td><span class="badge badge-green">Aktif</span></td>
                            <td>
                                <button class="act-btn" title="Edit" onclick="openModal('modal-treatment')">✏️</button>
                                <button class="act-btn" title="Pratinjau" onclick="openModal('modal-preview-treatment')">👁️</button>
                                <button class="act-btn" title="Nonaktifkan" onclick="showToast('Treatment dinonaktifkan')">🔕</button>
                                <button class="act-btn" title="Hapus">🗑️</button>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#b89098; font-size:11px">2</td>
                            <td>
                                <img src="https://images.unsplash.com/photo-1512290923902-8a9f81dc236c?w=100&auto=format&fit=crop&q=80"
                                    style="width:52px; height:40px; object-fit:cover; border-radius:6px; border:1px solid #f2c4ce">
                            </td>
                            <td>
                                <div class="td-name">Botox & Fillers</div>
                                <div class="td-sub">pages/treatment/botox.php</div>
                            </td>
                            <td><span class="badge badge-yellow">Injectable</span></td>
                            <td style="max-width:200px; font-size:12px; color:#7a4d5c; font-weight:300">Perawatan suntik yang disetujui FDA untuk menghaluskan kerutan wajah.</td>
                            <td>
                                <div style="display:flex; align-items:center; gap:4px">
                                    <button class="act-btn" style="font-size:12px; padding:2px 6px">↑</button>
                                    <span style="font-size:13px; font-weight:500; color:#3d1a22; min-width:16px; text-align:center">2</span>
                                    <button class="act-btn" style="font-size:12px; padding:2px 6px">↓</button>
                                </div>
                            </td>
                            <td><span class="badge badge-green">Aktif</span></td>
                            <td>
                                <button class="act-btn" onclick="openModal('modal-treatment')">✏️</button>
                                <button class="act-btn" onclick="openModal('modal-preview-treatment')">👁️</button>
                                <button class="act-btn" onclick="showToast('Treatment dinonaktifkan')">🔕</button>
                                <button class="act-btn">🗑️</button>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#b89098; font-size:11px">3</td>
                            <td>
                                <img src="https://images.unsplash.com/photo-1515377905703-c4788e51af15?w=100&auto=format&fit=crop&q=80"
                                    style="width:52px; height:40px; object-fit:cover; border-radius:6px; border:1px solid #f2c4ce">
                            </td>
                            <td>
                                <div class="td-name">Laser Treatments</div>
                                <div class="td-sub">pages/treatment/laser.php</div>
                            </td>
                            <td><span class="badge badge-blue">Technology</span></td>
                            <td style="max-width:200px; font-size:12px; color:#7a4d5c; font-weight:300">Teknologi laser untuk mengatasi pigmentasi, bekas jerawat, dan tanda penuaan.</td>
                            <td>
                                <div style="display:flex; align-items:center; gap:4px">
                                    <button class="act-btn" style="font-size:12px; padding:2px 6px">↑</button>
                                    <span style="font-size:13px; font-weight:500; color:#3d1a22; min-width:16px; text-align:center">3</span>
                                    <button class="act-btn" style="font-size:12px; padding:2px 6px">↓</button>
                                </div>
                            </td>
                            <td><span class="badge badge-green">Aktif</span></td>
                            <td>
                                <button class="act-btn" onclick="openModal('modal-treatment')">✏️</button>
                                <button class="act-btn" onclick="openModal('modal-preview-treatment')">👁️</button>
                                <button class="act-btn" onclick="showToast('Treatment dinonaktifkan')">🔕</button>
                                <button class="act-btn">🗑️</button>
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#b89098; font-size:11px">4</td>
                            <td>
                                <img src="https://images.unsplash.com/photo-1607619056574-7b8d3ee536b2?w=100&auto=format&fit=crop&q=80"
                                    style="width:52px; height:40px; object-fit:cover; border-radius:6px; border:1px solid #f2c4ce">
                            </td>
                            <td>
                                <div class="td-name">Body Contouring</div>
                                <div class="td-sub">pages/treatment/contouring.php</div>
                            </td>
                            <td><span class="badge badge-green">Contouring</span></td>
                            <td style="max-width:200px; font-size:12px; color:#7a4d5c; font-weight:300">Perawatan khusus untuk membentuk dan merampingkan tubuh dengan presisi.</td>
                            <td>
                                <div style="display:flex; align-items:center; gap:4px">
                                    <button class="act-btn" style="font-size:12px; padding:2px 6px">↑</button>
                                    <span style="font-size:13px; font-weight:500; color:#3d1a22; min-width:16px; text-align:center">4</span>
                                    <button class="act-btn" style="font-size:12px; padding:2px 6px">↓</button>
                                </div>
                            </td>
                            <td><span class="badge badge-green">Aktif</span></td>
                            <td>
                                <button class="act-btn" onclick="openModal('modal-treatment')">✏️</button>
                                <button class="act-btn" onclick="openModal('modal-preview-treatment')">👁️</button>
                                <button class="act-btn" onclick="showToast('Treatment dinonaktifkan')">🔕</button>
                                <button class="act-btn">🗑️</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div style="padding:16px 20px; border-top:1px solid #fdf0f5; display:flex; justify-content:space-between; align-items:center; font-size:12px; color:#b89098">
                    <span>4 treatment aktif dari 4 total</span>
                    <div style="display:flex; gap:8px; align-items:center">
                        <span style="font-size:11px; color:#b89098">Urutan menentukan tampilan di index</span>
                        <button class="btn-save" style="font-size:10px; padding:7px 16px" onclick="showToast('Urutan berhasil disimpan ✓')">Simpan Urutan</button>
                    </div>
                </div>
            </div>

        </div>
        <!-- ══ PANEL: PROFIL ══ -->
        <div class="panel" id="panel-profil">
            <div class="page-header">
                <div>
                    <h2 class="section-title">Profil & <em>Keamanan</em></h2>
                    <p class="section-sub">Kelola informasi akun dan keamanan admin</p>
                </div>
                <button class="btn-save" onclick="showToast('Perubahan berhasil disimpan ✓')">Simpan Perubahan</button>
            </div>

            <!-- Hero profil -->
            <div class="profil-hero">
                <div class="profil-avatar-wrap">
                    <div class="profil-avatar">A</div>
                    <div class="profil-edit-avatar" onclick="showToast('Fitur upload foto segera hadir')">✏️</div>
                </div>
                <div class="profil-info">
                    <div class="profil-name">Administrator</div>
                    <div class="profil-role">Super Admin · GlowCare Clinic</div>
                    <div class="profil-meta">
                        <div class="profil-meta-item">
                            <span class="profil-meta-label">Username</span>
                            <span class="profil-meta-value">admin_glowcare</span>
                        </div>
                        <div class="profil-meta-item">
                            <span class="profil-meta-label">Email</span>
                            <span class="profil-meta-value">admin@glowcareclinic.com</span>
                        </div>
                        <div class="profil-meta-item">
                            <span class="profil-meta-label">Bergabung</span>
                            <span class="profil-meta-value">Januari 2020</span>
                        </div>
                        <div class="profil-meta-item">
                            <span class="profil-meta-label">Akses Terakhir</span>
                            <span class="profil-meta-value">Hari ini, 08:45</span>
                        </div>
                    </div>
                </div>
                <div class="profil-stats">
                    <div>
                        <div class="profil-stat-val">1.248</div>
                        <div class="profil-stat-lbl">Total Pasien</div>
                    </div>
                    <div>
                        <div class="profil-stat-val">8</div>
                        <div class="profil-stat-lbl">Dokter Aktif</div>
                    </div>
                    <div>
                        <div class="profil-stat-val">6 Thn</div>
                        <div class="profil-stat-lbl">Bergabung</div>
                    </div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px">

                <!-- Data pribadi -->
                <div class="section-card">
                    <div class="section-card-header">
                        <div class="section-card-title">Data <em>Pribadi</em></div>
                    </div>
                    <div class="section-card-body">
                        <div class="form-row-2">
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap</label>
                                <input class="form-input" value="Administrator">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Username</label>
                                <input class="form-input" value="admin_glowcare">
                            </div>
                            <div class="form-group">
                                <label class="form-label">No. Telepon</label>
                                <input class="form-input" value="+62 812 0000 0001">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Jabatan</label>
                                <select class="form-select">
                                    <option selected>Super Admin</option>
                                    <option>Admin Operasional</option>
                                    <option>Admin Keuangan</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input class="form-input" type="email" value="admin@glowcareclinic.com">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Alamat</label>
                            <input class="form-input" value="Jl. Kecantikan No. 12, Mataram, NTB">
                        </div>
                    </div>
                </div>

                <!-- Keamanan + Log -->
                <div style="display:flex; flex-direction:column; gap:20px">

                    <!-- Ganti password -->
                    <div class="section-card">
                        <div class="section-card-header">
                            <div class="section-card-title">Keamanan <em>Password</em></div>
                        </div>
                        <div class="section-card-body">
                            <div class="form-group">
                                <label class="form-label">Password Saat Ini</label>
                                <input class="form-input" type="password" placeholder="••••••••">
                            </div>
                            <div class="form-row-2">
                                <div class="form-group">
                                    <label class="form-label">Password Baru</label>
                                    <input class="form-input" type="password" placeholder="Min. 8 karakter">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Konfirmasi Password</label>
                                    <input class="form-input" type="password" placeholder="Ulangi password">
                                </div>
                            </div>
                            <div style="background:#fdf0f5; border-radius:8px; padding:10px 14px; font-size:11px; color:#7a4d5c; margin-bottom:16px; font-weight:300; line-height:1.7">
                                🔒 Password minimal 8 karakter, kombinasi huruf besar, kecil, angka, dan simbol.
                            </div>
                            <button class="btn-save" style="width:100%" onclick="showToast('Password berhasil diubah ✓')">Ubah Password</button>
                        </div>
                    </div>

                    <!-- Preferensi notifikasi -->
                    <div class="section-card">
                        <div class="section-card-header">
                            <div class="section-card-title">Preferensi <em>Notifikasi</em></div>
                        </div>
                        <div class="section-card-body" style="display:flex; flex-direction:column; gap:10px">
                            <label style="display:flex; align-items:center; justify-content:space-between; font-size:13px; color:#3d1a22; cursor:pointer; padding:10px 14px; background:#fdf0f5; border-radius:8px">
                                Email notifikasi pasien baru
                                <input type="checkbox" checked>
                            </label>
                            <label style="display:flex; align-items:center; justify-content:space-between; font-size:13px; color:#3d1a22; cursor:pointer; padding:10px 14px; background:#fdf0f5; border-radius:8px">
                                Notifikasi pembatalan jadwal
                                <input type="checkbox" checked>
                            </label>
                            <label style="display:flex; align-items:center; justify-content:space-between; font-size:13px; color:#3d1a22; cursor:pointer; padding:10px 14px; background:#fdf0f5; border-radius:8px">
                                Laporan harian otomatis
                                <input type="checkbox">
                            </label>
                            <label style="display:flex; align-items:center; justify-content:space-between; font-size:13px; color:#3d1a22; cursor:pointer; padding:10px 14px; background:#fdf0f5; border-radius:8px">
                                Notifikasi sistem & maintenance
                                <input type="checkbox" checked>
                            </label>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Log aktivitas akun -->
            <div class="section-card">
                <div class="section-card-header">
                    <div class="section-card-title">Log <em>Aktivitas Akun</em></div>
                    <span style="font-size:11px; color:#b89098">30 hari terakhir</span>
                </div>
                <div class="section-card-body">
                    <div class="log-item">
                        <div class="log-dot green"></div>
                        <div>
                            <div class="log-text"><strong>Login berhasil</strong> — via Chrome · Mataram, NTB</div>
                            <div class="log-time">Hari ini, 08:45 · IP: 192.168.1.10</div>
                        </div>
                    </div>
                    <div class="log-item">
                        <div class="log-dot"></div>
                        <div>
                            <div class="log-text"><strong>Data pasien diperbarui</strong> — #P-0041 Siti Rahayu</div>
                            <div class="log-time">Kemarin, 14:22</div>
                        </div>
                    </div>
                    <div class="log-item">
                        <div class="log-dot"></div>
                        <div>
                            <div class="log-text"><strong>Jadwal dokter ditambahkan</strong> — Dr. Anisa Putri · Senin-Rabu</div>
                            <div class="log-time">28 Apr 2026, 10:05</div>
                        </div>
                    </div>
                    <div class="log-item">
                        <div class="log-dot orange"></div>
                        <div>
                            <div class="log-text"><strong>Percobaan login gagal</strong> — 3x dari IP tidak dikenal</div>
                            <div class="log-time">25 Apr 2026, 23:11 · IP: 103.xx.xx.xx</div>
                        </div>
                    </div>
                    <div class="log-item">
                        <div class="log-dot green"></div>
                        <div>
                            <div class="log-text"><strong>Password diubah</strong> — berhasil diperbarui</div>
                            <div class="log-time">20 Apr 2026, 09:00</div>
                        </div>
                    </div>
                    <div class="log-item">
                        <div class="log-dot gray"></div>
                        <div>
                            <div class="log-text"><strong>Logout</strong> — sesi berakhir</div>
                            <div class="log-time">19 Apr 2026, 18:30</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

</div><!-- /content -->
</div><!-- /main -->

<!-- ══ MODAL: TAMBAH/EDIT TREATMENT ══ -->
<div class="modal-overlay" id="modal-treatment" onclick="closeModalOutside(event,'modal-treatment')">
    <div class="modal" style="width:620px">
        <h3 class="modal-title">Tambah / Edit <em>Treatment</em></h3>
        <p class="modal-sub">Data ini akan ditampilkan di halaman index klinik</p>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px">
            <div class="form-group" style="grid-column:1/-1">
                <label class="form-label">Nama Treatment</label>
                <input class="form-input" placeholder="Contoh: Facelift Procedures" value="Facelift Procedures">
            </div>
            <div class="form-group">
                <label class="form-label">Kategori / Tag</label>
                <select class="form-select">
                    <option selected>Surgical</option>
                    <option>Injectable</option>
                    <option>Technology</option>
                    <option>Contouring</option>
                    <option>Skincare</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Link Halaman Detail</label>
                <input class="form-input" placeholder="pages/treatment/facelift.php" value="pages/treatment/facelift.php">
            </div>
            <div class="form-group" style="grid-column:1/-1">
                <label class="form-label">Deskripsi Singkat (tampil di index)</label>
                <textarea class="form-textarea" style="min-height:80px">Teknik bedah canggih untuk memulihkan kontur wajah yang tampak muda dan segar.</textarea>
            </div>

            <!-- Gambar -->
            <div class="form-group" style="grid-column:1/-1">
                <label class="form-label">Gambar Treatment</label>
                <div style="border:2px dashed #f2c4ce; border-radius:10px; padding:28px; text-align:center; background:#fdf0f5; cursor:pointer; transition:border-color 0.2s" onmouseover="this.style.borderColor='#c55085'" onmouseout="this.style.borderColor='#f2c4ce'">
                    <div style="font-size:28px; margin-bottom:8px">🖼️</div>
                    <div style="font-size:13px; color:#c55085; font-weight:500">Klik untuk upload gambar</div>
                    <div style="font-size:11px; color:#b89098; margin-top:4px">JPG, PNG, WebP · Maks 2MB · Rekomendasi 700×525px</div>
                    <input type="file" accept="image/*" style="display:none">
                </div>
                <div style="margin-top:10px; display:flex; gap:10px; align-items:center">
                    <img src="https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=120&auto=format&fit=crop&q=80" style="width:80px; height:60px; object-fit:cover; border-radius:6px; border:1px solid #f2c4ce">
                    <div>
                        <div style="font-size:12px; color:#3d1a22; font-weight:400">Gambar saat ini</div>
                        <div style="font-size:10px; color:#b89098; margin-top:2px">facelift.jpg · 486KB</div>
                        <button style="margin-top:6px; background:none; border:none; color:#c55085; font-size:10px; letter-spacing:1px; text-transform:uppercase; cursor:pointer; font-family:'DM Sans',sans-serif">Hapus gambar</button>
                    </div>
                </div>
            </div>

            <!-- URL gambar alternatif -->
            <div class="form-group" style="grid-column:1/-1">
                <label class="form-label">Atau Gunakan URL Gambar</label>
                <input class="form-input" placeholder="https://images.unsplash.com/..." value="https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=700&auto=format&fit=crop&q=80">
            </div>

            <div class="form-group">
                <label class="form-label">Urutan Tampil di Index</label>
                <input class="form-input" type="number" value="1" min="1" max="10">
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select class="form-select">
                    <option selected>Aktif</option>
                    <option>Nonaktif</option>
                </select>
            </div>

            <!-- Konten halaman detail -->
            <div class="form-group" style="grid-column:1/-1">
                <label class="form-label">Deskripsi Lengkap (halaman detail)</label>
                <textarea class="form-textarea" style="min-height:100px" placeholder="Deskripsi lengkap yang akan ditampilkan di halaman detail treatment...">Facelift (rhytidectomy) adalah prosedur bedah yang mengencangkan dan mengangkat jaringan wajah yang kendur untuk menciptakan tampilan yang lebih muda. Prosedur ini efektif mengatasi kerutan dalam, kulit kendur di pipi dan leher, serta garis rahang yang memudar.</textarea>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal('modal-treatment')">Batal</button>
            <button class="btn-save" onclick="closeModal('modal-treatment'); showToast('Treatment berhasil disimpan ✓ Tampilan index diperbarui')">Simpan & Publikasikan</button>
        </div>
    </div>
</div>

<!-- ══ MODAL: PREVIEW TREATMENT ══ -->
<div class="modal-overlay" id="modal-preview-treatment" onclick="closeModalOutside(event,'modal-preview-treatment')">
    <div class="modal" style="width:560px; padding:0; overflow:hidden">
        <!-- Preview header -->
        <div style="position:relative; height:200px; overflow:hidden">
            <img src="https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=700&auto=format&fit=crop&q=80" style="width:100%; height:100%; object-fit:cover">
            <div style="position:absolute; inset:0; background:linear-gradient(to top,rgba(61,26,34,0.7),transparent)"></div>
            <div style="position:absolute; top:16px; left:16px; background:rgba(255,255,255,0.9); border-radius:50px; padding:4px 12px; font-size:9px; letter-spacing:2px; text-transform:uppercase; color:#b89098">Surgical</div>
            <div style="position:absolute; top:16px; right:16px; display:flex; gap:8px">
                <span class="badge badge-green">Aktif</span>
                <span style="background:rgba(255,255,255,0.9); border-radius:50px; padding:3px 10px; font-size:10px; color:#3d1a22">Urutan #1</span>
            </div>
            <div style="position:absolute; bottom:16px; left:20px">
                <div style="font-family:'Playfair Display',serif; font-size:22px; color:#fff; margin-bottom:2px">Facelift Procedures</div>
                <div style="font-size:10px; letter-spacing:1.5px; text-transform:uppercase; color:rgba(255,255,255,0.7)">pages/treatment/facelift.php</div>
            </div>
        </div>

        <div style="padding:28px">
            <div style="font-size:13px; font-weight:500; color:#b89098; letter-spacing:1px; text-transform:uppercase; margin-bottom:10px">Tampilan di Index</div>
            <!-- Mini card preview -->
            <div style="background:#f9e7ef; border-radius:10px; overflow:hidden; border:1px solid #f2c4ce; margin-bottom:20px">
                <div style="position:relative; height:120px; overflow:hidden">
                    <img src="https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=400&auto=format&fit=crop&q=80" style="width:100%; height:100%; object-fit:cover">
                </div>
                <div style="padding:12px 16px">
                    <div style="font-size:9px; letter-spacing:2px; text-transform:uppercase; color:#b89098; margin-bottom:4px">Surgical</div>
                    <div style="font-family:'Playfair Display',serif; font-size:14px; color:#c55085; margin-bottom:4px">Facelift Procedures</div>
                    <div style="font-size:11px; color:#7a4d5c; font-weight:300; line-height:1.6">Teknik bedah canggih untuk memulihkan kontur wajah yang tampak muda.</div>
                    <div style="display:inline-flex; align-items:center; gap:5px; margin-top:8px; font-size:10px; letter-spacing:1.5px; text-transform:uppercase; color:#c55085">Learn more <span>→</span></div>
                </div>
            </div>

            <div style="font-size:13px; font-weight:500; color:#b89098; letter-spacing:1px; text-transform:uppercase; margin-bottom:10px">Deskripsi Singkat</div>
            <div style="font-size:13px; color:#7a4d5c; font-weight:300; line-height:1.8; margin-bottom:20px">
                Teknik bedah canggih untuk memulihkan kontur wajah yang tampak muda dan segar.
            </div>

            <div style="display:flex; gap:10px">
                <button class="btn-cancel" onclick="closeModal('modal-preview-treatment')">Tutup</button>
                <button class="btn-save" onclick="closeModal('modal-preview-treatment'); openModal('modal-treatment')">✏️ Edit Treatment</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast">✅ <span id="toast-msg">Berhasil disimpan</span></div>

<script>
    function showPanel(id, el) {
        document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
        document.getElementById('panel-' + id).classList.add('active');
        document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
        if (el) el.classList.add('active');

        const titles = {
            dashboard: 'Dashboard',
            pasien: 'Data Pasien',
            dokter: 'Data Dokter',
            jadwal: 'Jadwal Dokter',
            aktivitas: 'Aktivitas',
            laporan: 'Laporan',
            treatment: 'Kelola Treatment',
            profil: 'Profil & Keamanan'
        };
        document.getElementById('topbar-title').textContent = titles[id] || id;
        document.getElementById('topbar-bc').textContent = 'GlowCare Admin → ' + (titles[id] || id);
    }

    function openModal(id) {
        document.getElementById(id).classList.add('open');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
    }

    function closeModalOutside(e, id) {
        if (e.target.classList.contains('modal-overlay')) closeModal(id);
    }

    function showToast(msg) {
        const t = document.getElementById('toast');
        document.getElementById('toast-msg').textContent = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3000);
    }
</script>
</body>
</html>