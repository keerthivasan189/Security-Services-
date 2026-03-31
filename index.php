<?php
session_start();
define('BASE_PATH', __DIR__);

// ── Auto-detect BASE_URL ──────────────────────────────────────────────────────
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'];
// Detect subfolder: everything between docroot and index.php
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$scriptDir = rtrim($scriptDir, '/');
define('BASE_URL', $protocol . '://' . $host . $scriptDir);

require_once BASE_PATH . '/config/db.php';
require_once BASE_PATH . '/helpers/Session.php';
require_once BASE_PATH . '/helpers/Helper.php';

// ── Route map ─────────────────────────────────────────────────────────────────
$routeMap = [
    'dashboard'  => 'DashboardController',
    'auth'       => 'AuthController',
    'employees'  => 'EmployeeController',
    'employee'   => 'EmployeeController',
    'clients'    => 'ClientController',
    'client'     => 'ClientController',
    'attendance' => 'AttendanceController',
    'payments'   => 'PaymentsController',
    'payment'    => 'PaymentsController',
    'receipts'   => 'ReceiptsController',
    'receipt'    => 'ReceiptsController',
    'accounts'   => 'AccountsController',
    'account'    => 'AccountsController',
    'positions'  => 'PositionController',
    'position'   => 'PositionController',
    'reports'    => 'ReportsController',
    'report'     => 'ReportsController',
    'inventory'  => 'InventoryController',
    'masterdata' => 'MasterDataController',
    'crm'        => 'CrmController',
];

// ── Parse URL ─────────────────────────────────────────────────────────────────
$rawUrl = isset($_GET['url']) ? trim($_GET['url'], '/') : '';
$rawUrl = preg_replace('/\.php.*$/', '', $rawUrl);       // strip .php extension
$rawUrl = preg_replace('/[^a-zA-Z0-9_\/]/', '', $rawUrl); // safe chars only

if (empty($rawUrl)) {
    $rawUrl = Session::isLoggedIn() ? 'dashboard/index' : 'auth/login';
}

$parts  = explode('/', $rawUrl);
$seg    = strtolower($parts[0]);
$method = (isset($parts[1]) && $parts[1] !== '') ? $parts[1] : 'index';
$param  = $parts[2] ?? null;

// ── Redirect unknown segments ─────────────────────────────────────────────────
if (!isset($routeMap[$seg])) {
    $loc = Session::isLoggedIn()
        ? BASE_URL . '/index.php?url=dashboard/index'
        : BASE_URL . '/index.php?url=auth/login';
    header('Location: ' . $loc);
    exit;
}

// ── Load & dispatch controller ────────────────────────────────────────────────
$controllerName = $routeMap[$seg];
$controllerFile = BASE_PATH . '/controllers/' . $controllerName . '.php';

if (!file_exists($controllerFile)) {
    http_response_code(404);
    die("<h2>404 – Controller file not found: {$controllerName}.php</h2>");
}

require_once $controllerFile;

if (!class_exists($controllerName)) {
    http_response_code(500);
    die("<h2>500 – Class '{$controllerName}' not defined.</h2>");
}

$controller = new $controllerName($pdo);

if (!method_exists($controller, $method)) {
    // Unknown method → redirect to module index
    header('Location: ' . BASE_URL . '/index.php?url=' . $seg . '/index');
    exit;
}

$controller->$method($param);
