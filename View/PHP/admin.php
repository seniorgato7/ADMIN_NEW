<?php
/**
 * 1. SESSION & CACHE SECURITY
 */
session_start();

// Redirect if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Global Role Normalization (Fixes button visibility case-sensitivity)
$sessionRole = isset($_SESSION['authority_level']) ? strtolower(trim($_SESSION['authority_level'])) : '';
$currentSessionId = isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : 0;

// Security: Prevent browser back-button caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

/**
 * 2. DATABASE INITIALIZATION
 */
include('db_connect.php');

/**
 * 3. ANALYTICS & DASHBOARD STATS
 */
// Total Resident Count
$resCount = $conn->query("SELECT COUNT(*) as total FROM residents");
$totalResidents = ($resCount) ? $resCount->fetch_assoc()['total'] : 0;

// Active Resident Count
$activeCount = $conn->query("SELECT COUNT(*) as total FROM residents WHERE resident_status = 'Active'");
$activeResidents = ($activeCount) ? $activeCount->fetch_assoc()['total'] : 0;

// Revenue Calculation (Utility Bills)
$moneyQuery = $conn->query("SELECT SUM(total_bill) as total FROM utility_bills");
$totalMoney = ($moneyQuery && $row = $moneyQuery->fetch_assoc()) ? ($row['total'] ?? 0) : 0;

/**
 * 4. DATA FETCHING (SUBDIVISIONS & RESIDENTS)
 */
// Subdivisions for dropdowns
$projects = $conn->query("SELECT * FROM subdivisions ORDER BY project_name ASC");

// Comprehensive Resident & Latest Utility Data (17 Fields)
$resQuery = $conn->query("
    SELECT
        r.resident_id, r.subdivision_id, s.project_name as project,
        r.phase, r.block_no, r.lot_no, r.tct_no, r.tct_file,
        r.buyer_name, r.new_buyer_assumed, r.buyer_representative,
        r.contact_no, r.social_media, r.email_address,
        r.account_number, r.account_address,
        r.resident_status, r.remarks, r.created_at,
        u.prev_reading, u.present_reading, u.total_bill, u.bill_status, u.remaining_balance, u.current_bill
    FROM residents r
    LEFT JOIN subdivisions s ON r.subdivision_id = s.subdivision_id
    LEFT JOIN (
        SELECT * FROM utility_bills WHERE bill_id IN (
            SELECT MAX(bill_id) FROM utility_bills GROUP BY resident_id
        )
    ) u ON r.resident_id = u.resident_id
    ORDER BY r.resident_id DESC
");

$residentsArray = [];
if ($resQuery) {
    while($row = $resQuery->fetch_assoc()) {
        $residentsArray[] = $row;
    }
}

$solarHousesQuery = $conn->query("
    SELECT
        r.resident_id,
        r.subdivision_id,
        s.project_name AS project,
        r.block_no,
        r.lot_no,
        r.buyer_name,
        r.resident_status
    FROM residents r
    LEFT JOIN subdivisions s ON r.subdivision_id = s.subdivision_id
    ORDER BY r.resident_id DESC
");

$solarHousesArray = [];

if ($solarHousesQuery) {
    while ($row = $solarHousesQuery->fetch_assoc()) {
        $solarHousesArray[] = $row;
    }
}

$connovateQuery = $conn->query("
    SELECT
        id,
        project_name,
        block_no,
        lot_no,
        floor_name,
        panel_key,
        connovate_part,
        quantity,
        status,
        created_at AS started_at,
        completed_at
    FROM connovate_panels
    ORDER BY id DESC
");

$connovatePanelsArray = [];
if ($connovateQuery) {
    while ($row = $connovateQuery->fetch_assoc()) {
        $connovatePanelsArray[] = $row;
    }
}

$solarPanelsArray = [];

$solarCheck = $conn->query("SHOW TABLES LIKE 'solar_panel_parts'");

if ($solarCheck && $solarCheck->num_rows > 0) {
    $solarQuery = $conn->query("
        SELECT
            id,
            resident_id,
            project_name,
            block_no,
            lot_no,
            solar_type,
            part_name,
            solar_status,
            installation_date,
            provider,
            capacity_details,
            proof_file,
            remarks,
            created_at,
            updated_at
        FROM solar_panel_parts
        ORDER BY updated_at DESC
    ");

    if ($solarQuery) {
        while ($row = $solarQuery->fetch_assoc()) {
            $solarPanelsArray[] = $row;
        }
    }
}
/**
 * 5. SYSTEM LOGS & ADMINISTRATIVE DATA
 */
// Audit Logs (Latest 100 actions)
$audit_logs = $conn->query("
    SELECT
        l.log_id, l.admin_id, l.action_type, l.details, l.timestamp,
        a.admin_name
    FROM admin_logs l
    LEFT JOIN admins a ON l.admin_id = a.admin_id
    ORDER BY l.timestamp DESC
    LIMIT 100
");

$auditLogsArray = [];
if ($audit_logs && $audit_logs->num_rows > 0) {
    while($row = $audit_logs->fetch_assoc()) {
        $auditLogsArray[] = $row;
    }
    $audit_logs->data_seek(0); // Reset pointer for HTML rendering
}

// Administrative Users Management
$admins = $conn->query("SELECT admin_id, admin_name, authority_level, admin_status, auth_key FROM admins ORDER BY admin_id ASC");

// My Own Profile (topbar avatar + Edit Profile modal)
$myProfile = ['admin_name' => $_SESSION['admin_name'] ?? 'Admin', 'auth_key' => '', 'profile_photo' => null];
$myProfileStmt = $conn->prepare("SELECT admin_name, auth_key, profile_photo FROM admins WHERE admin_id = ?");
$myProfileStmt->bind_param("i", $currentSessionId);
$myProfileStmt->execute();
$myProfileResult = $myProfileStmt->get_result()->fetch_assoc();
if ($myProfileResult) {
    $myProfile = $myProfileResult;
}

$profilePhotoWebPath = null;
if (!empty($myProfile['profile_photo'])) {
    $photoFsPath = __DIR__ . '/../assets/img/profiles/' . $myProfile['profile_photo'];
    if (file_exists($photoFsPath)) {
        $profilePhotoWebPath = '../assets/img/profiles/' . $myProfile['profile_photo'] . '?v=' . filemtime($photoFsPath);
    }
}

/**
 * 6. SYSTEM UTILITIES
 */
function insert_audit_log($conn, $admin_name, $action_type, $module, $details) {
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $conn->prepare("INSERT INTO admin_logs (admin_id, action_type, details) VALUES ((SELECT admin_id FROM admins WHERE admin_name = ? LIMIT 1), ?, ?)");
    $stmt->bind_param("sss", $admin_name, $action_type, $details);
    return $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imperial House - Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css?v=<?php echo filemtime(__DIR__ . '/../assets/style.css'); ?>">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
</head>
<body>

    <div id="leftMenu" class="left-menu">

        <div class="left-menu-header">
            <img src="../assets/img/logo/imperialhouse_logo.png?v=20260618" alt="Imperial Homes" class="sidebar-logo" width="38" height="38">
            <div class="imperial-brand-text">
                <div class="imperial-brand-name imperial-heading-gradient">IMPERIAL HOMES</div>
                <div class="imperial-brand-sub imperial-heading-gradient-sub">CORPORATION</div>
            </div>
        </div>

        <ul class="left-menu-nav">
            <li class="left-menu-item active" data-page="dashboard">
                <img src="../assets/img/icons/dashboardBlack.png" class="menu-icon icon-default" alt="">
                <img src="../assets/img/icons/dashboardWhite.png" class="menu-icon icon-active" alt="">
                <span class="menu-label">Dashboard</span>
            </li>
            <li class="left-menu-item" data-page="residents">
                <img src="../assets/img/icons/residentsBlack.png" class="menu-icon icon-default" alt="">
                <img src="../assets/img/icons/residentsWhite.png" class="menu-icon icon-active" alt="">
                <span class="menu-label">Residents</span>
            </li>
            <li class="left-menu-item" data-page="connovate">
                <img src="../assets/img/icons/connovateBlack.png" class="menu-icon icon-default" alt="">
                <img src="../assets/img/icons/connovateWhite.png" class="menu-icon icon-active" alt="">
                <span class="menu-label">Connovate</span>
            </li>
            <li class="left-menu-item" data-page="solar">
                <img src="../assets/img/icons/solarBlack.png" class="menu-icon icon-default" alt="">
                <img src="../assets/img/icons/solarWhite.png" class="menu-icon icon-active" alt="">
                <span class="menu-label">Solar Panels</span>
            </li>
            <li class="left-menu-item" data-page="admins">
                <img src="../assets/img/icons/adminBlack.png" class="menu-icon icon-default" alt="">
                <img src="../assets/img/icons/adminWhite.png" class="menu-icon icon-active" alt="">
                <span class="menu-label">Admin Management</span>
            </li>
            <li class="left-menu-item" data-page="reports">
                <img src="../assets/img/icons/auditBlack.png" class="menu-icon icon-default" alt="">
                <img src="../assets/img/icons/auditWhite.png" class="menu-icon icon-active" alt="">
                <span class="menu-label">Audit Logs</span>
            </li>
        </ul>

    </div>

<header class="topbar">
    <button type="button" id="sidebarToggle" class="sidebar-toggle" aria-controls="leftMenu" aria-expanded="true" title="Toggle navigation">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <div class="topbar-date">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
            <rect x="3" y="4" width="18" height="18" rx="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
        </svg>
        <span><?php echo date('l, F j, Y'); ?></span>
    </div>

    <!-- RIGHT SIDE: Welcome + User Dropdown -->
    <div class="topbar-user-area">
        <span class="topbar-welcome">
            Welcome, <strong><?php echo htmlspecialchars(strtoupper($_SESSION['admin_name'] ?? 'Admin')); ?></strong>!
        </span>

        <div class="topbar-user-menu" id="topbarUserMenu">
            <!-- Orange user icon button -->
            <button class="topbar-avatar-btn" onclick="toggleUserDropdown(event)" aria-label="User menu">
                <?php if ($profilePhotoWebPath): ?>
                    <img src="<?php echo htmlspecialchars($profilePhotoWebPath); ?>" alt="Profile" id="topbarAvatarImg" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                <?php else: ?>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="22" height="22">
                    <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                </svg>
                <?php endif; ?>
            </button>

            <!-- Dropdown -->
            <div class="topbar-dropdown" id="topbarDropdown">
                <button class="topbar-dropdown-item" onclick="openEditProfileModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="15" height="15"><path d="M3 17.25V21h3.75l11.06-11.06-3.75-3.75L3 17.25zm17.71-10.21a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                    Edit Profile
                </button>
                <button class="topbar-dropdown-item topbar-dropdown-signout" onclick="confirmLogout(event)">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="15" height="15"><path d="M10.09 15.59L11.5 17l5-5-5-5-1.41 1.41L12.67 11H3v2h9.67l-2.58 2.59zM19 3H5a2 2 0 0 0-2 2v4h2V5h14v14H5v-4H3v4a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2z"/></svg>
                    Sign Out
                </button>
            </div>
        </div>
    </div>
</header>


<main class="main-content">
        <section id="section-dashboard" class="app-page active">
            <div class="page-header" style="margin-bottom: 25px;"><h2 class="page-title">Dashboard</h2></div>
            <div class="stats-ribbon">
                <div class="stat-card">
                    <div class="stat-card-text">
                        <div class="stat-label">Global Residents</div>
                        <div class="stat-value"><?php echo number_format($totalResidents); ?></div>
                    </div>
                    <div class="stat-icon stat-icon-green">
                        <img src="../assets/img/icons/global.png" alt="">
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-text">
                        <div class="stat-label">Active Connections</div>
                        <div class="stat-value"><?php echo number_format($activeResidents); ?></div>
                    </div>
                    <div class="stat-icon stat-icon-blue">
                        <img src="../assets/img/icons/social-network (1).png" alt="">
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-text">
                        <div class="stat-label">Total Receivables</div>
                        <div class="stat-value">₱ <?php echo number_format($totalMoney, 2); ?></div>
                    </div>
                    <div class="stat-icon stat-icon-orange">
                        <img src="../assets/img/icons/dollar.png" alt="">
                    </div>
                </div>
            </div>

            <div class="location-selector-section">
                <h2 class="section-title">Project Selection</h2>
                <div class="selector-wrapper" style="display: flex; gap: 15px; margin-top: 15px;">
                    <select id="locationSelect" class="header-select">
                        <option value="">-- Select Project --</option>
                        <?php
                        $projects->data_seek(0);
                        while($p = $projects->fetch_assoc()): ?>
                            <option value="<?php echo $p['subdivision_id']; ?>"
                                    data-name="<?php echo htmlspecialchars($p['project_name']); ?>">
                                <?php echo htmlspecialchars($p['project_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>

                    <button onclick="handleLocationChange()" class="btn-load">Load Project</button>
                </div>
            </div>

            <div class="map-wrapper" style="margin-top: 30px;">
                <div id="map-controls" style="display: none; justify-content: flex-end; margin-bottom: 10px;">
                    <button class="danger-btn" onclick="window.closeMapSection()" style="background:#ef4444; color:white; border:none; padding: 8px 16px; border-radius:8px; cursor:pointer;">✖ Close Map</button>
                </div>
                <div id="mapContainer" style="display:none; height: 550px; border-radius: 15px; z-index: 1;"></div>
            </div>

            <div id="project-analytics-box" style="margin-top: 30px; background: #1e293b; padding: 25px; border-radius: 15px; border: 1px solid #334155;">
                <h3 id="analytics-title" style="color: #d49006; margin-bottom: 20px; font-weight: 700; text-transform: uppercase;">Project Analytics Overview</h3>
                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 300px; height: 300px; background: #161e2e; padding: 15px; border-radius: 10px;"><canvas id="populationChart"></canvas></div>
                    <div style="flex: 1; min-width: 300px; height: 300px; background: #161e2e; padding: 15px; border-radius: 10px;"><canvas id="billingChart"></canvas></div>
                </div>
            </div>
        </section>

        <section id="section-residents" class="app-page">
            <div class="page-header" style="margin-bottom: 25px;"><h2 class="page-title">Residents Management</h2></div>
            <div class="residents-toolbar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div class="admin-search-group">
                    <span class="admin-search-label">Search:</span>
                    <!-- Fake fields to trick Chrome autofill away from the search bar -->
                    <input type="text" style="display:none;" aria-hidden="true">
                    <input type="password" style="display:none;" aria-hidden="true">
                    <input type="text" id="residentSearch" placeholder="Search by Name, TCT, or Account" class="search-input admin-search-input" autocomplete="off" name="search_residents">
                </div>
                <button class="primary-btn btn-add-resident" onclick="openResidentForm()"><span class="plus-icon">+</span> Add Resident</button>
            </div>
            <div class="residents-table-wrapper">
                <table class="residents-table">
                    <thead>
                        <tr>
                            <th>ID</th><th>Subdivision</th><th>Phase</th><th>Block</th><th>Lot</th>
                            <th>TCT No.</th><th>Buyer Name</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="residentsTableBody"></tbody>
                </table>
            </div>
        </section>

<section id="section-connovate" class="app-page">
            <div class="page-header" style="margin-bottom: 25px;"><h2 class="page-title">Connovate</h2></div>
            <div class="connovate-toolbar location-selector-section">
                <h2 class="section-title">Project Selection</h2>
                <div class="selector-wrapper connovate-selector-wrapper">
                    <select id="connovateProjectSelect" class="header-select">
                        <option value="">-- Select Project --</option>
                    </select>
                </div>
            </div>
            <div class="connovate-chart-wrapper">
                <div class="connovate-chart-card connovate-board-card">
                    <div class="connovate-chart-header">
                        <div>
                            <h3 id="connovateBoardTitle">Connovate Panel Board</h3>
                            <p id="connovateBoardSubtitle">Select a project to view Ground Floor and Second Floor totals.</p>
                        </div>
                    </div>
                    <div class="connovate-board-stage" id="connovateBoardStage">
                        <div class="connovate-board-strip">
                            <span class="board-box-label">Project Summary</span>
                            <strong id="connovateBoardProjectTotal">0%</strong>
                            <small id="connovateBoardProjectMeta">0 of 0 houses finished</small>
                        </div>

                        <div class="connovate-board-main connovate-board-chart-block">
                            <div class="connovate-chart-frame">
                                <canvas id="connovateFloorChart"></canvas>
                            </div>
                            <div class="connovate-floor-meta">
                                <span>Ground Floor Parts: <strong id="connovateGroundFloorPanels">0</strong></span>
                                <span>Ground Floor Produced: <strong id="connovateGroundFloorDone">0</strong></span>
                                <span>Second Floor Parts: <strong id="connovateSecondFloorPanels">0</strong></span>
                                <span>Second Floor Produced: <strong id="connovateSecondFloorDone">0</strong></span>
                            </div>
                        </div>

                        <div class="connovate-board-strip connovate-board-strip-bottom">
                            <span class="board-box-label">Remaining Summary</span>
                            <strong id="connovateBoardRemainingQuantity">0</strong>
                            <small id="connovateBoardRemainingMeta">Pending quantity</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="connovate-panel-toolbar" style="display: flex; justify-content: space-between; align-items: center; margin-top: 30px; margin-bottom: 20px;">
                <h3 id="connovateRecordsTitle" style="margin: 0; font-size: 16px; font-weight: 600;">Connovated Records</h3>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <label style="color: #6b6b6b; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Search:</label>
                    <input type="text" id="connovateRecordsSearch" style="padding: 6px 12px; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 6px; color: #1a1a1a; font-size: 13px; min-width: 180px; outline: none;">
                    <label style="color: #6b6b6b; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Block:</label>
                    <select id="connovateRecordsBlockFilter" style="padding: 6px 12px; background: #ffffff; border: 1px solid #e5e7eb; border-radius: 6px; color: #1a1a1a; font-size: 13px; min-width: 100px; cursor: pointer; outline: none;">
                        <option value="">All</option>
                    </select>
                </div>
            </div>
            <div class="connovate-table-wrapper">
                <table class="connovate-table" id="connovateRecordsTable">
                    <thead>
                        <tr>
                            <th>Block</th><th>Lot</th><th>Phase</th><th>Connovated Quantity</th><th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="connovateRecordsTableBody"></tbody>
                </table>
                <p id="connovateRecordsEmpty" style="display:none; text-align:center; color:#94a3b8; padding: 20px; font-size: 13px;">Select a project above to view Block/Lot records.</p>
            </div>
            <div class="connovate-panel-toolbar" style="display: flex; justify-content: space-between; align-items: center; margin-top: 30px; margin-bottom: 20px;">
                <h3 id="connovatePartsListTitle" style="margin: 0; font-size: 16px; font-weight: 600;">Connovate Parts List</h3>
                <div id="connovateFilterContainer" style="display: flex; gap: 10px; align-items: center;"></div>
            </div>
            <div class="connovate-table-wrapper">
                <table class="connovate-table">
                    <thead>
                        <tr>
                            <th>Connovate Part</th><th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody id="connovateTableBody"></tbody>
                </table>
            </div>
        </section>


    <div class="modal-overlay" id="modalOverlay">
        <div class="modal-container" style="max-width: 650px;">
            <div class="modal-top-bar">
                <span id="modalTitle">Property Detail View</span>
                <button class="close-x" id="modalClose" onclick="window.closeMarkerModal()">✕</button>
            </div>
            <div id="markerModalContent" class="modal-body" style="position: relative; max-height: 65vh; overflow-y: auto; background: #1e293b; color: white;">
                <div class="accent-bar" style="height: 4px; background: #d49006;"></div>
                <div class="details-section" style="padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; border-bottom: 1px solid #334155; padding-bottom: 15px;">
                        <div>
                            <span style="color: #94a3b8; font-size: 10px; font-weight: bold; text-transform: uppercase; display: block;">Primary Buyer</span>
                            <h2 id="infoClient" style="margin: 0; color: #f8fafc; font-size: 20px;">-</h2>
                            <span id="infoStatus" class="status-tag" style="margin-top: 5px; display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">-</span>
                        </div>
                        <div style="text-align: right;">
                            <span style="color: #94a3b8; font-size: 10px; font-weight: bold; text-transform: uppercase; display: block;">Resident ID</span>
                            <span id="infoResId" style="font-weight: 700; color: #d49006;">-</span>
                        </div>
                    </div>

                    <!-- REPLACE the entire property grid div in the Property Detail View modal -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">

                        <div>
                            <label style="color: #94a3b8; font-size: 10px; display: block;">SUBDIVISION / PROJECT</label>
                            <span id="infoAddress" style="font-size: 13px;">-</span>
                        </div>
                        <div>
                            <label style="color: #94a3b8; font-size: 10px; display: block;">TCT NUMBER</label>
                            <span id="infoTct" style="font-size: 13px;">-</span>
                        </div>

                        <div>
                            <label style="color: #94a3b8; font-size: 10px; display: block;">PHASE / BLK / LOT</label>
                            <span id="infoProperty" style="font-size: 13px;">-</span>
                        </div>
                        <div>
                            <label style="color: #94a3b8; font-size: 10px; display: block;">TCT FILE</label>
                            <div id="infoTctFile" style="font-size: 13px; margin-top: 2px;">-</div>
                        </div>

                        <div>
                            <label style="color: #94a3b8; font-size: 10px; display: block;">CONTACT NO.</label>
                            <span id="infoContact" style="font-size: 13px;">-</span>
                        </div>
                        <div>
                            <label style="color: #94a3b8; font-size: 10px; display: block;">EMAIL ADDRESS</label>
                            <span id="infoEmail" style="font-size: 13px;">-</span>
                        </div>

                        <div>
                            <label style="color: #94a3b8; font-size: 10px; display: block;">SOCIAL MEDIA</label>
                            <span id="infoSocial" style="font-size: 13px;">-</span>
                        </div>
                        <div>
                            <label style="color: #94a3b8; font-size: 10px; display: block;">NEW BUYER / ASSUMED</label>
                            <span id="infoNewBuyer" style="font-size: 13px;">-</span>
                        </div>

                        <div>
                            <label style="color: #94a3b8; font-size: 10px; display: block;">REPRESENTATIVE</label>
                            <span id="infoRep" style="font-size: 13px;">-</span>
                        </div>
                        <div></div>

                        <div style="grid-column: span 2;">
                            <label style="color: #94a3b8; font-size: 10px; display: block;">ACCOUNT ADDRESS</label>
                            <span id="infoAccAddress" style="font-size: 13px;">-</span>
                        </div>

                    </div>

                    <div style="background: #fffaf5; padding: 15px; border-radius: 8px; border: 1px solid #f3e4d7; margin-bottom: 20px;">
                        <h3 style="font-size: 11px; color: var(--orange); margin-top: 0; margin-bottom: 12px; text-transform: uppercase; font-weight: 700;">Latest Billing Summary</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div><label style="color: #92703a; font-size: 9px; display: block;">ACCOUNT NO.</label><span id="infoAccNo" style="font-size: 13px; font-weight: 600; color: #172033;">-</span></div>
                            <div><label style="color: #92703a; font-size: 9px; display: block;">BILL STATUS</label><span id="infoBillStatus" style="font-size: 13px; font-weight: bold; color: #172033;">-</span></div>
                            <div><label style="color: #92703a; font-size: 9px; display: block;">OUTSTANDING BAL.</label><span id="infoTotalBill" style="font-size: 16px; font-weight: bold; color: #dc2626;">₱ 0.00</span></div>
                            <div><label style="color: #92703a; font-size: 9px; display: block;">LAST UPDATE</label><span id="infoCreated" style="font-size: 13px; color: #172033;">-</span></div>
                        </div>
                    </div>

                    <div style="margin-bottom: 5px;">
                        <label style="color: #94a3b8; font-size: 10px; display: block;">REMARKS</label>
                        <p id="infoRemarks" style="font-size: 12px; color: #cbd5e1; font-style: italic; margin-top: 5px;">-</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="justify-content: flex-end; flex-wrap: wrap;">
                <button id="infoConnovateBtn" class="primary-btn connovate-btn" onclick="window.openConnovateFromInfo && window.openConnovateFromInfo()">Connovate</button>
                <button id="infoSolarBtn" class="primary-btn solar-btn" onclick="window.openSolarFromInfo && window.openSolarFromInfo()">Solar Panels</button>
                <button id="infoEditBtn" class="primary-btn">Go to Management Profile</button>
            </div>
        </div>
    </div>


    <div id="addResidentModal" class="modal-overlay">
        <div class="modal-container" style="max-width: 800px;">
            <div class="modal-top-bar"><span>New Resident Registration</span><button type="button" onclick="closeAddModal()">✕</button></div>
            <form id="addResidentForm">
                <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">
                    <div class="form-section-card">
                        <div class="form-section-card-title">Property Details</div>
                        <div class="form-grid">
                            <div>
                                <label>Subdivision Project</label>
                                <select id="addProject" name="subdivision_id" required>
                                    <option value="" disabled selected>Select Project</option>
                                    <?php $projects->data_seek(0); while($p = $projects->fetch_assoc()): ?>
                                        <option value="<?php echo htmlspecialchars($p['subdivision_id']); ?>"><?php echo $p['project_name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div>
                                <label>TCT Number</label>
                                <input type="text" id="addTct" name="tct_no">
                            </div>
                        </div>

                        <div style="margin-top: 10px;">
                            <label>TCT File</label>
                            <input type="file" id="addTctFile" accept=".pdf,.jpg,.jpeg,.png" style="display:none;">
                            <input type="hidden" id="addCurrentTctFile" name="tct_file">

                            <div class="custom-file-box" onclick="document.getElementById('addTctFile').click()">
                                <span id="addTctFileName">Choose File</span>
                                <button type="button"
                                        id="addTctRemoveBtn"
                                        class="custom-file-x"
                                        style="display:none;"
                                        onclick="event.stopPropagation(); removeAddTctFile();">
                                    ✕
                                </button>
                            </div>
                        </div>
                        <div style="display: flex; gap: 15px; margin-top: 10px; flex-wrap: wrap;">
                            <div style="flex: 1;"><label>Phase</label><input type="text" id="addPhase" name="phase"></div>
                            <div style="flex: 1;"><label>Block No.</label><input type="text" id="addBlock" name="block_no" style="text-transform: uppercase;" required></div>
                            <div style="flex: 1;"><label>Lot No.</label><input type="text" id="addLot" name="lot_no" style="text-transform: uppercase;" required></div>
                        </div>
                    </div>

                    <div class="form-section-card">
                        <div class="form-section-card-title">Ownership Information</div>
                        <div class="form-grid">
                            <div>
                                <label>Primary Buyer Name</label>
                                <input type="text" id="addName" name="buyer_name"
                                       pattern="^[a-zA-Z\sñÑ.]+$"
                                       title="Numbers are not allowed in the name."
                                       required>
                            </div>
                            <div><label>Account Number</label><input type="text" id="addAccountNo" name="account_number"></div>
                        </div>
                        <div class="form-grid" style="margin-top: 10px;">
                            <div><label>New Buyer Assumed</label><input type="text" id="addNewBuyer" name="new_buyer_assumed" pattern="^[a-zA-Z\sñÑ.]+$" title="Numbers are not allowed."></div>
                            <div><label>Buyer Representative</label><input type="text" id="addRep" name="buyer_representative" pattern="^[a-zA-Z\sñÑ.]+$" title="Numbers are not allowed."></div>
                        </div>
                        <label style="margin-top:10px;">Account/Billing Address</label>
                        <input type="text" id="addAccountAddress" name="account_address">
                    </div>

                    <div class="form-section-card">
                        <div class="form-section-card-title">Contact & Communication</div>
                        <div class="form-grid">
                            <div>
                                <label>Contact No.</label>
                                <input type="text" id="addContact" name="contact_no"
                                       oninput="this.value = this.value.replace(/[^0-9+]/g, '')"
                                       placeholder="09123456789">
                            </div>
                            <div><label>Email Address</label><input type="email" id="addEmail" name="email_address"></div>
                        </div>
                        <label style="margin-top:10px;">Social Media (FB/Messenger)</label>
                        <input type="text" id="addSocial" name="social_media">
                    </div>

                    <div class="form-section-card">
                        <div class="form-section-card-title">System Status & Remarks</div>
                        <div class="form-grid">
                            <div>
                                <label>Resident Status</label>
                                <select id="addStatus" name="resident_status">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                            <div><label>Registration Date</label><input type="date" id="addCreatedAt" name="created_at" value="<?php echo date('Y-m-d'); ?>"></div>
                        </div>
                        <label style="margin-top:10px;">Internal Remarks</label>
                        <textarea id="addRemarks" name="remarks" placeholder="Add any specific notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="footer-btn footer-btn-outline" onclick="openConnovateFromAddForm()">Connovate</button>
                    <button type="button" class="footer-btn footer-btn-outline" onclick="openSolarFromAddForm()">Solar Panels</button>
                    <button type="button" class="btn-delete" onclick="closeAddModal()">Cancel</button>
                    <button type="submit" class="footer-btn footer-btn-primary">Register Resident</button>
                </div>
            </form>
        </div>
    </div>

   <div id="editResidentModal" class="modal-overlay">
        <div class="modal-container" style="max-width: 800px;">
            <div class="modal-top-bar"><span>Edit Resident Information</span><button type="button" onclick="closeEditModal()">✕</button></div>
            <form id="editResidentForm">
                <input type="hidden" id="editResidentId" name="resident_id">
                <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">
                    <div class="modal-section-title">Property Details</div>
                    <div class="form-grid">
                        <div>
                            <label>Subdivision Project</label>
                            <select id="editProject" name="subdivision_id" required>
                                <option value="" disabled selected>Select Project</option>
                                <?php $projects->data_seek(0); while($p = $projects->fetch_assoc()): ?>
                                    <option value="<?php echo htmlspecialchars($p['subdivision_id']); ?>"><?php echo $p['project_name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div>
                            <label>TCT Number</label>
                            <input type="text" id="editTct" name="tct_no">
                        </div>

                        <div>
                            <label>TCT File</label>
                            <input type="file" id="editTctFile" accept=".pdf,.jpg,.jpeg,.png" style="display:none;">
                            <input type="hidden" id="editCurrentTctFile" name="tct_file">
                            <input type="hidden" id="editDeleteTctFile" value="0">

                            <div class="custom-file-box" onclick="document.getElementById('editTctFile').click()">
                                <span id="editTctFileName">Choose File</span>
                                <button type="button"
                                        id="editTctRemoveBtn"
                                        class="custom-file-x"
                                        style="display:none;"
                                        onclick="event.stopPropagation(); removeTctFile();">
                                    ✕
                                </button>
                            </div>
                        </div>
                        <div><label>Phase</label><input type="text" id="editPhase" name="phase"></div>
                        <div><label>Block No.</label><input type="text" id="editBlock" name="block_no" style="text-transform: uppercase;" required></div>
                        <div><label>Lot No.</label><input type="text" id="editLot" name="lot_no" style="text-transform: uppercase;" required></div>
                        <div><label>Created Date</label><input type="date" id="editCreatedAt" name="created_at"></div>
                    </div>

                    <div class="modal-section-title">Ownership & Assumption</div>
                    <div class="form-grid">
                        <div>
                            <label>Primary Buyer Name</label>
                            <input type="text" id="editName" name="buyer_name"
                                   pattern="^[a-zA-Z\sñÑ.]+$"
                                   title="Numbers are not allowed in the name."
                                   required>
                        </div>
                        <div>
                            <label>New Buyer / Assumed By</label>
                            <input type="text" id="editNewBuyer" name="new_buyer_assumed"
                                   pattern="^[a-zA-Z\sñÑ.]+$"
                                   title="Numbers are not allowed.">
                        </div>
                        <div>
                            <label>Buyer Representative</label>
                            <input type="text" id="editRep" name="buyer_representative"
                                   pattern="^[a-zA-Z\sñÑ.]+$"
                                   title="Numbers are not allowed.">
                        </div>
                        <div><label>Account Number</label><input type="text" id="editAccountNo" name="account_number"></div>
                    </div>

                    <div class="modal-section-title">Contact Information</div>
                    <div class="form-grid">
                        <div>
                            <label>Contact No.</label>
                            <input type="text" id="editContact" name="contact_no"
                                   oninput="this.value = this.value.replace(/[^0-9+]/g, '')"
                                   placeholder="09123456789">
                        </div>
                        <div><label>Email Address</label><input type="email" id="editEmail" name="email_address"></div>
                        <div><label>Social Media (Link/Handle)</label><input type="text" id="editSocial" name="social_media"></div>
                        <div><label>Account Address</label><input type="text" id="editAccountAddress" name="account_address"></div>
                    </div>

                    <div class="modal-section-title">Resident Status & Remarks</div>
                    <div class="form-grid">
                        <div>
                            <label>Resident Status</label>
                            <select id="editStatus" name="resident_status">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <div><label>Remarks</label><textarea id="editRemarks" name="remarks"></textarea></div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: space-between;">
                    <button type="button" class="danger-btn" onclick="openDeleteConfirmation()" style="background: #991b1b; color: white; border: none; padding: 10px 20px; border-radius: 30px; font-weight: 700; cursor: pointer;">Delete Resident</button>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: flex-end;">
                        <button type="button" class="primary-btn connovate-btn" onclick="openConnovateFromEditForm()">Connovate</button>
                        <button type="button" class="primary-btn solar-btn" onclick="openSolarFromEditForm()">Solar Panels</button>
                        <button type="button" class="btn-delete" onclick="closeEditModal()">Cancel</button>
                        <button type="submit" class="primary-btn">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteResidentModal" class="modal-overlay" style="z-index: 2000;">
        <div class="modal-container" style="max-width: 400px; text-align: center;">
            <div class="modal-top-bar" style="background: #991b1b;"><span>Confirm Deletion</span><button onclick="closeDeleteModal()">✕</button></div>
            <div class="modal-body" style="padding: 30px;">
                <p style="color: #f87171; font-weight: bold; margin-bottom: 20px;">This action cannot be undone. Enter Admin PIN to proceed.</p>
                <input type="password" id="adminPinInput" placeholder="Enter 4-Digit PIN"
                       style="text-align: center; font-size: 24px; letter-spacing: 10px; padding: 10px; width: 100%; border-radius: 8px; background: #0f172a; color: white; border: 1px solid #ef4444;">
            </div>
            <div class="modal-footer" style="justify-content: center; gap: 10px;">
                <button type="button" class="btn-delete" onclick="closeDeleteModal()">Cancel</button>
                <button type="button" class="danger-btn" onclick="processDeleteResident()" style="background: #ef4444;">Confirm Delete</button>
            </div>
        </div>
    </div>

<?php include('connovate.php'); ?>
<?php include('solar_panels.php'); ?>

        <section id="section-reports" class="app-page">
    <div class="page-header" style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h2 class="page-title">System Audit Log</h2>
            <p style="color: #64748b; font-size: 13px; margin: 0;">Track all administrative changes and system activities.</p>
        </div>

        <button onclick="exportAuditLog()"
                style="padding: 10px 18px; background: #d49006; color: #0f172a; border: none; border-radius: 8px; cursor: pointer; font-weight: 700; font-size: 13px; transition: 0.2s;">
            Export CSV
        </button>
    </div>

    <div class="audit-toolbar" style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; align-items: flex-end; background: #1e293b; padding: 15px; border-radius: 10px; border: 1px solid #334155;">

        <div class="filter-group">
            <label style="display:block; font-size:10px; color:#d49006; font-weight:800; margin-bottom:5px; text-transform:uppercase; letter-spacing:1px;">Search Details</label>
            <input type="text" id="auditSearch" onkeyup="filterAuditLog()" placeholder="Search content..."
                   style="padding: 8px 12px; background: #0f172a; border: 1px solid #334155; border-radius: 6px; color: #f8fafc; font-size: 13px; width: 200px; outline: none;">
        </div>

        <div class="filter-group">
            <label style="display:block; font-size:10px; color:#d49006; font-weight:800; margin-bottom:5px; text-transform:uppercase; letter-spacing:1px;">Admin</label>
            <select id="filterAdmin" onchange="filterAuditLog()"
                    style="padding: 8px 12px; background: #0f172a; border: 1px solid #334155; border-radius: 6px; color: #f8fafc; font-size: 13px; width: 140px; outline: none;">
                <option value="">All Admins</option>
                <?php
                // Dynamically populate admin names for the filter dropdown
                if(isset($admins)):
                    $admins->data_seek(0);
                    while($adm = $admins->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($adm['admin_name']); ?>">
                            <?php echo htmlspecialchars($adm['admin_name']); ?>
                        </option>
                <?php endwhile; endif; ?>
            </select>
        </div>

        <div class="filter-group">
            <label style="display:block; font-size:10px; color:#d49006; font-weight:800; margin-bottom:5px; text-transform:uppercase; letter-spacing:1px;">Action Type</label>
            <select id="filterAction" onchange="filterAuditLog()"
                    style="padding: 8px 12px; background: #0f172a; border: 1px solid #334155; border-radius: 6px; color: #f8fafc; font-size: 13px; width: 130px; outline: none;">
                <option value="">All Actions</option>
                <option value="CREATE">CREATE</option>
                <option value="UPDATE">UPDATE</option>
                <option value="DELETE">DELETE</option>
                <option value="LOGIN">LOGIN</option>
            </select>
        </div>

        <div class="filter-group">
            <label style="display:block; font-size:10px; color:#d49006; font-weight:800; margin-bottom:5px; text-transform:uppercase; letter-spacing:1px;">Year</label>
            <select id="filterYear" onchange="filterAuditLog()"
                    style="padding: 8px 12px; background: #0f172a; border: 1px solid #334155; border-radius: 6px; color: #f8fafc; font-size: 13px; width: 100px; outline: none;">
                <option value="">All Years</option>
                <?php for($y = date("Y"); $y >= 2024; $y--): ?>
                    <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <button onclick="resetAuditFilters()" class="btn-load" style="height: 35px; padding: 0 20px; font-size: 11px;">Reset View</button>
    </div>
    <div class="audit-table-wrapper">
        <table class="audit-log-table" id="auditLogTable">
            <thead>
                <tr>
                    <th style="width: 80px;">Log ID</th>
                    <th>Admin Details</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody id="auditLogBody">
                <?php if (!$audit_logs || $audit_logs->num_rows === 0): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 80px; color: #64748b;">
                            No activity logs found in the database.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

    <section id="section-admins" class="app-page">
    <div class="page-header">
        <h2 class="page-title">Admin Management</h2>
        <p>Master admins can manage all accounts. Staff can only edit their own profile.</p>
    </div>

    <div class="admin-toolbar">
        <div class="admin-search-group">
            <span class="admin-search-label">Search:</span>
            <input type="text" id="adminSearch" placeholder="Search" class="search-input admin-search-input" autocomplete="off">
        </div>

        <?php
        // Normalize role for comparison
        $sessionRole = isset($_SESSION['authority_level']) ? strtolower(trim($_SESSION['authority_level'])) : '';
        $iAmMaster = ($sessionRole === 'master');

        if($iAmMaster):
        ?>
        <button type="button" class="primary-btn btn-add-resident" onclick="openAddAdminModal()"><span class="plus-icon">+</span> Register New Admin</button>
        <?php endif; ?>
    </div>

    <div class="admin-list-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Access Level</th>
                    <th>Status</th>
                    <th style="text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody id="adminTableBody">
                <?php if(isset($admins) && $admins->num_rows > 0): ?>
                    <?php
                    $admins->data_seek(0);
                    while($row = $admins->fetch_assoc()):
                        $rowId = (int)$row['admin_id'];
                        $sessId = (int)$_SESSION['admin_id'];

                        $isMe = ($rowId === $sessId);
                        $rowLevelClean = strtolower(trim($row['authority_level']));

                        // UI Classes based on your DB values
                        $levelClass = ($rowLevelClean === 'master') ? 'level-master' : 'level-staff';

                        // FIXED STATUS LOGIC: Checking for 'active' instead of 'master'
                        $status = $row['admin_status'] ?? 'Deactivated';
                        $statusClass = (strtolower(trim($status)) === 'active') ? 'status-active' : 'status-inactive';

                        // Safe JSON for Edit Function
                        $adminJson = json_encode([
                            'admin_id' => $row['admin_id'],
                            'admin_name' => $row['admin_name'],
                            'auth_key' => $row['auth_key'],
                            'authority_level' => $row['authority_level'],
                            'admin_status' => $status
                        ]);
                    ?>
                    <tr>
                        <td class="admin-id-badge">#<?php echo $row['admin_id']; ?></td>
                        <td class="admin-name-cell">
                            <strong><?php echo htmlspecialchars($row['admin_name']); ?></strong>
                            <?php if($isMe): ?>
                                <span style="color: #3b82f6; font-size: 10px; font-weight: bold; text-transform: uppercase; margin-left: 5px;">(You)</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="level-tag <?php echo $levelClass; ?>">
                                <?php echo htmlspecialchars($row['authority_level']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge <?php echo $statusClass; ?>">
                                <?php echo htmlspecialchars($status); ?>
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 10px; justify-content: center;">
                                <?php if ($iAmMaster || $isMe): ?>
                                    <button type="button" class="btn-edit-admin" onclick='editAdmin(<?php echo htmlspecialchars($adminJson, ENT_QUOTES, 'UTF-8'); ?>)'>
                                        Edit
                                    </button>

                                    <?php if ($iAmMaster && !$isMe): ?>
                                        <button type="button" class="btn-delete-admin"
                                                onclick="confirmDeleteAdmin(<?php echo $row['admin_id']; ?>, '<?php echo addslashes($row['admin_name']); ?>')">
                                            Delete
                                        </button>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color: #64748b; font-size: 11px;">Restricted</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center; padding: 30px;">No administrators found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<div id="adminAccountModal" class="modal-overlay">
    <div class="admin-modal-content">
        <div class="admin-modal-header">
            <h3 id="adminModalTitle">Register New Admin</h3>
        </div>

        <form id="adminAccountForm" onsubmit="event.preventDefault(); saveAdminAccount();">
            <input type="hidden" id="modalAdminId">

            <div class="admin-form-group">
                <label>Username</label>
                <input type="text" id="modalAdminName" class="admin-form-input" required>
            </div>

            <div class="admin-form-group">
                <label>Account Status</label>
                <select id="modalAdminStatus" class="admin-form-input">
                    <option value="active">Active</option>
                    <option value="deactivated">Deactivated</option>
                </select>
            </div>

            <div class="admin-form-group">
                <label>Authority Level</label>
                <select id="modalAuthLevel" class="admin-form-input" <?php echo (!$iAmMaster) ? 'disabled' : ''; ?>>
                    <option value="Staff">Staff</option>
                    <option value="Master">Master</option>
                </select>
            </div>

            <div class="admin-form-group">
                <label>Master PIN / Auth Key</label>
                <div style="display: flex; gap: 5px;">
                    <input type="password" id="modalAdminKey" class="admin-form-input" maxlength="6" required>
                    <button type="button" onclick="toggleField('modalAdminKey', this)" style="background:none; border:1px solid #334155; color:#94a3b8; border-radius:4px; padding:0 10px; cursor:pointer;">SHOW</button>
                </div>
            </div>

            <div class="admin-form-group">
                <label>Login Password</label>
                <input type="password" id="modalAdminPassword" class="admin-form-input" placeholder="New password or leave blank">
            </div>

            <div class="admin-modal-footer">
                <button type="button" class="btn-cancel" onclick="closeAdminModal()">Cancel</button>
                <button type="submit" class="btn-save-admin">Save Account</button>
            </div>
        </form>
    </div>
</div>

<div id="adminSecurityModal" class="modal-overlay">
    <div class="admin-modal-content" style="max-width: 400px;">
        <div class="admin-modal-header">
            <h3 style="color: #ef4444;">Confirm Admin Deletion</h3>
        </div>
        <div style="padding: 20px;">
            <p>You are about to permanently delete admin: <b id="targetAdminNameText"></b></p>
            <p style="font-size: 13px; color: #64748b; margin-top: 10px;">To proceed, please enter <b>YOUR</b> Master Authorization Key:</p>

            <input type="hidden" id="deleteTargetId">

            <div class="admin-form-group" style="margin-top: 15px;">
                <input type="password" id="masterVerifyKey" class="admin-form-input" placeholder="Enter Master Key" maxlength="6">
            </div>
        </div>
        <div class="admin-modal-footer">
            <button type="button" class="btn-cancel" onclick="closeAdminSecurityModal()">Cancel</button>
            <button type="button" class="btn-save-admin" style="background: #ef4444;" onclick="executeVerifiedDelete()">Delete Permanent</button>
        </div>
    </div>
</div>



<div id="logoutModal" class="modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(15, 23, 42, 0.9); backdrop-filter:blur(5px); z-index:10001; justify-content:center; align-items:center;">
    <div style="background:#1e293b; border:1px solid #334155; padding:30px; border-radius:15px; width:350px; text-align:center; box-shadow: 0 20px 50px rgba(0,0,0,0.5);">
        <img src="../assets/img/icons/logout.png" alt="Sign Out" style="width:40px; height:40px; margin-bottom:15px;">
        <h3 style="color:#f8fafc; margin-bottom:10px; font-weight:800; text-transform:uppercase; letter-spacing:1px;">Confirm Sign Out</h3>
        <p style="color:#94a3b8; font-size:14px; margin-bottom:25px;">Are you sure you want to end your session?</p>

        <div style="display:flex; gap:10px;">
            <button onclick="closeLogoutModal()" style="flex:1; padding:12px; background:#334155; color:#f8fafc; border:none; border-radius:8px; cursor:pointer; font-weight:700; transition:0.2s;">CANCEL</button>
            <button onclick="processLogout()" style="flex:1; padding:12px; background:#d49006; color:#0f172a; border:none; border-radius:8px; cursor:pointer; font-weight:800; transition:0.2s;">LOG OUT</button>
        </div>
    </div>
</div>


<!-- EDIT PROFILE MODAL -->
<div id="editProfileModal" class="modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(15, 23, 42, 0.6); backdrop-filter:blur(5px); z-index:10040; justify-content:center; align-items:center;">
    <div class="admin-modal-content">
        <div class="admin-modal-header">
            <h3>Edit Profile</h3>
        </div>

        <form id="editProfileForm" class="edit-profile-form" onsubmit="event.preventDefault(); requestSaveProfile();" enctype="multipart/form-data">

            <div class="admin-modal-scroll-body">

                <div class="profile-avatar-upload">
                    <div class="profile-avatar-circle" onclick="document.getElementById('profilePhotoInput').click()">
                        <img id="profileAvatarPreview"
                             src="<?php echo $profilePhotoWebPath ? htmlspecialchars($profilePhotoWebPath) : ''; ?>"
                             alt="Profile Photo"
                             style="<?php echo $profilePhotoWebPath ? '' : 'display:none;'; ?>">
                        <div class="profile-avatar-default" id="profileAvatarDefault" style="<?php echo $profilePhotoWebPath ? 'display:none;' : ''; ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="38" height="38">
                                <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                            </svg>
                        </div>
                        <div class="profile-avatar-edit-badge">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M3 17.25V21h3.75l11.06-11.06-3.75-3.75L3 17.25zm17.71-10.21a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                        </div>
                    </div>
                    <input type="file" id="profilePhotoInput" accept=".jpg,.jpeg,.png,.gif" style="display:none;" onchange="previewProfilePhoto(event)">
                    <p class="admin-form-hint" style="text-align:center; margin-top:8px;">Click the photo to change it</p>
                </div>

                <div class="admin-form-group">
                    <label>Username</label>
                    <input type="text" id="profileUsername" class="admin-form-input" value="<?php echo htmlspecialchars($myProfile['admin_name']); ?>" required>
                </div>

                <div class="admin-form-group">
                    <label>Master PIN / Auth Key</label>
                    <div class="profile-readonly-field"><?php echo htmlspecialchars($myProfile['auth_key'] !== '' ? $myProfile['auth_key'] : '------'); ?></div>
                    <p class="admin-form-hint">This key is fixed and cannot be changed here.</p>
                </div>

                <hr class="admin-form-divider">
                <p class="admin-form-section-label">Change Password (optional)</p>

                <div class="admin-form-group">
                    <label>Current Password</label>
                    <div class="password-field-wrapper">
                        <input type="password" id="profileCurrentPassword" class="admin-form-input" placeholder="Enter current password">
                        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('profileCurrentPassword', this)" aria-label="Show password">
                            <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="icon-eye-off" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18" style="display:none;"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a18.6 18.6 0 0 1 5.06-5.94M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a18.6 18.6 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>

                <div class="admin-form-group">
                    <label>New Password</label>
                    <div class="password-field-wrapper">
                        <input type="password" id="profileNewPassword" class="admin-form-input" placeholder="Enter new password">
                        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('profileNewPassword', this)" aria-label="Show password">
                            <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="icon-eye-off" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18" style="display:none;"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a18.6 18.6 0 0 1 5.06-5.94M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a18.6 18.6 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>

                <div class="admin-form-group">
                    <label>Confirm New Password</label>
                    <div class="password-field-wrapper">
                        <input type="password" id="profileConfirmPassword" class="admin-form-input" placeholder="Confirm new password">
                        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility('profileConfirmPassword', this)" aria-label="Show password">
                            <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="icon-eye-off" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18" style="display:none;"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a18.6 18.6 0 0 1 5.06-5.94M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a18.6 18.6 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>

            </div>

            <div class="admin-modal-footer">
                <button type="button" class="btn-cancel" onclick="closeEditProfileModal()">Cancel</button>
                <button type="submit" class="btn-save-admin">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- CONFIRM SAVE PROFILE MODAL -->
<div id="confirmProfileModal" class="modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(15, 23, 42, 0.9); backdrop-filter:blur(5px); z-index:10050; justify-content:center; align-items:center;">
    <div style="background:#1e293b; border:1px solid #334155; padding:30px; border-radius:15px; width:350px; text-align:center; box-shadow: 0 20px 50px rgba(0,0,0,0.5);">
        <h3 style="color:#f8fafc; margin-bottom:10px; font-weight:800; text-transform:uppercase; letter-spacing:1px;">Save Changes?</h3>
        <p style="color:#94a3b8; font-size:14px; margin-bottom:25px;" id="confirmProfileText">Are you sure you want to save these changes?</p>

        <div style="display:flex; gap:10px;">
            <button type="button" onclick="closeConfirmProfileModal()" style="flex:1; padding:9px 12px; font-size:13px; background:#334155; color:#f8fafc; border:none; border-radius:8px; cursor:pointer; font-weight:700;">CANCEL</button>
<button type="button" onclick="submitProfileChanges()" id="confirmProfileSaveBtn" style="flex:1; padding:9px 12px; font-size:13px; background:#d49006; color:#0f172a; border:none; border-radius:8px; cursor:pointer; font-weight:700;">SAVE CHANGES</button>
        </div>
    </div>
</div>


<script>
    // Residents Data (Fail-safe: defaults to empty array if null)
    window.residents = <?php echo json_encode($residentsArray ?? []); ?>;
    window.solarHouses = <?php echo json_encode($solarHousesArray ?? []); ?>;
    window.connovatePanels = <?php echo json_encode($connovatePanelsArray ?? []); ?>;
    window.solarPanels = <?php echo json_encode($solarPanelsArray ?? []); ?>;

    // Audit Logs Data (Using the processed array from your PHP update)
    window.auditLogs = <?php echo json_encode($auditLogsArray ?? []); ?>;

    // Debugging Console - Helps verify data load on page start
    console.log("System Ready: " +
        (window.residents ? window.residents.length : 0) + " residents and " +
        (window.auditLogs ? window.auditLogs.length : 0) + " logs loaded."
    );
    
</script>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="get_maps_data.php"></script>

<script src="../javascript/marker.js?v=<?php echo filemtime(__DIR__ . '/../javascript/marker.js'); ?>"></script>
<script src="../javascript/ui_utils.js"></script>
<script src="../javascript/mapModal.js?v=<?php echo filemtime(__DIR__ . '/../javascript/mapModal.js'); ?>"></script>
<script src="../javascript/connovateModal.js?v=<?php echo filemtime(__DIR__ . '/../javascript/connovateModal.js'); ?>"></script>
<script src="../javascript/residentsManagement.js?v=<?php echo filemtime(__DIR__ . '/../javascript/residentsManagement.js'); ?>"></script>
<script src="../javascript/connovateManagement.js?v=<?php echo filemtime(__DIR__ . '/../javascript/connovateManagement.js'); ?>"></script>
<script src="../javascript/adminManagement.js"></script>
<script src="../javascript/auditReports.js"></script>
<script src="../javascript/projectAnalytics.js"></script>
<script src="../javascript/menu.js"></script>
<script src="../javascript/map.js"></script>
<script src="../javascript/logOut.js"></script>
<script src="../javascript/solarPanels.js?v=<?php echo filemtime(__DIR__ . '/../javascript/solarPanels.js'); ?>"></script>
<script src="../javascript/editProfile.js?v=<?php echo filemtime(__DIR__ . '/../javascript/editProfile.js'); ?>"></script>
<script>
function exportAuditLog() {
    window.location.href = "export_audit_log.php";
}
</script>


</body>
</html>
