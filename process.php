<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Log file paths for MAIN ACCIDENT SYSTEM
$ACCIDENT_LOG_FILE = 'accidents.json';
$ALERT_LOG_FILE = 'alerts.json';
$SMS_LOG_FILE = 'sms_log.json';

// Log file paths for AUTO DETECTION SYSTEM
$AUTO_DETECTIONS_FILE = 'auto_detections.json';
$SENSOR_DATA_FILE = 'sensor_data.json';
$SYSTEM_STATUS_FILE = 'system_status.json';
$POLICE_ALERTS_FILE = 'police_alerts.json';
$SYSTEM_LOGS_FILE = 'system_logs.json';

// Initialize all files if they don't exist
initializeAllFiles();

function initializeAllFiles() {
    global $ACCIDENT_LOG_FILE, $ALERT_LOG_FILE, $SMS_LOG_FILE;
    global $AUTO_DETECTIONS_FILE, $SENSOR_DATA_FILE, $SYSTEM_STATUS_FILE, $POLICE_ALERTS_FILE, $SYSTEM_LOGS_FILE;
    
    // Main Accident System files
    $mainSystemFiles = [
        $ACCIDENT_LOG_FILE => json_encode([]),
        $ALERT_LOG_FILE => json_encode([]),
        $SMS_LOG_FILE => json_encode([])
    ];
    
    // Auto Detection System files
    $autoDetectionFiles = [
        $AUTO_DETECTIONS_FILE => json_encode(['detections' => [], 'count' => 0], JSON_PRETTY_PRINT),
        $SENSOR_DATA_FILE => json_encode(['sensors' => [], 'last_updated' => date('Y-m-d H:i:s')], JSON_PRETTY_PRINT),
        $SYSTEM_STATUS_FILE => json_encode([
            'auto_detection_active' => true,
            'last_detection_time' => date('H:i:s'),
            'detection_count' => 29,
            'system_uptime' => 99.8,
            'response_time' => 2.3,
            'accuracy' => 96.5,
            'last_updated' => date('Y-m-d H:i:s')
        ], JSON_PRETTY_PRINT),
        $POLICE_ALERTS_FILE => json_encode(['alerts' => [], 'last_alert_id' => 0], JSON_PRETTY_PRINT),
        $SYSTEM_LOGS_FILE => json_encode(['logs' => []], JSON_PRETTY_PRINT)
    ];
    
    // Create all files
    foreach ($mainSystemFiles as $file => $content) {
        if (!file_exists($file)) {
            file_put_contents($file, $content);
        }
    }
    
    foreach ($autoDetectionFiles as $file => $content) {
        if (!file_exists($file)) {
            file_put_contents($file, $content);
        }
    }
}

// ==== MAIN REQUEST HANDLER ====
$requestMethod = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';
$system = isset($_GET['system']) ? $_GET['system'] : 'main'; // 'main' or 'auto'

if ($system === 'auto') {
    // Handle Auto Detection System requests
    handleAutoDetectionRequest($requestMethod, $action);
} else {
    // Handle Main Accident System requests (default)
    handleMainAccidentRequest($requestMethod, $action);
}

// ==== AUTO DETECTION SYSTEM HANDLERS ====
function handleAutoDetectionRequest($method, $action) {
    switch ($action) {
        case 'get_status':
            getAutoSystemStatus();
            break;
        case 'update_status':
            updateAutoSystemStatus();
            break;
        case 'get_detections':
            getAutoDetections();
            break;
        case 'add_detection':
            addAutoDetection();
            break;
        case 'get_sensors':
            getAutoSensorData();
            break;
        case 'update_sensors':
            updateAutoSensorData();
            break;
        case 'get_alerts':
            getAutoAlerts();
            break;
        case 'add_alert':
            addAutoAlert();
            break;
        case 'get_system_info':
            getAutoSystemInfo();
            break;
        case 'control_system':
            controlAutoSystem();
            break;
        case 'get_police_alerts':
            getAutoPoliceAlerts();
            break;
        case 'send_police_alert':
            sendAutoPoliceAlert();
            break;
        case 'get_logs':
            getSystemLogs();
            break;
        default:
            sendResponse([
                'success' => true,
                'message' => 'Auto Detection System API',
                'version' => '3.0',
                'endpoints' => [
                    'GET ?system=auto&action=get_status' => 'Get auto system status',
                    'POST ?system=auto&action=update_status' => 'Update auto system status',
                    'GET ?system=auto&action=get_detections' => 'Get auto detections',
                    'POST ?system=auto&action=add_detection' => 'Add auto detection',
                    'GET ?system=auto&action=get_sensors' => 'Get sensor data',
                    'POST ?system=auto&action=update_sensors' => 'Update sensor data',
                    'GET ?system=auto&action=get_alerts' => 'Get system alerts',
                    'POST ?system=auto&action=add_alert' => 'Add system alert',
                    'GET ?system=auto&action=get_system_info' => 'Get system information',
                    'POST ?system=auto&action=control_system' => 'Control system (start/stop/test)',
                    'GET ?system=auto&action=get_police_alerts' => 'Get police alerts',
                    'POST ?system=auto&action=send_police_alert' => 'Send police alert',
                    'GET ?system=auto&action=get_logs' => 'Get system logs'
                ]
            ]);
    }
}

// ==== AUTO DETECTION SYSTEM FUNCTIONS ====

function getAutoSystemStatus() {
    global $SYSTEM_STATUS_FILE;
    
    $status = json_decode(file_get_contents($SYSTEM_STATUS_FILE), true);
    
    // Calculate uptime
    $lastUpdated = new DateTime($status['last_updated']);
    $now = new DateTime();
    $interval = $lastUpdated->diff($now);
    $uptimeMinutes = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;
    
    // Simulate small variations
    $status['system_uptime'] = 99.7 + (rand(0, 10) / 100);
    $status['response_time'] = 2.1 + (rand(0, 20) / 100);
    $status['accuracy'] = 96.0 + (rand(0, 30) / 100);
    $status['last_updated'] = date('Y-m-d H:i:s');
    
    file_put_contents($SYSTEM_STATUS_FILE, json_encode($status, JSON_PRETTY_PRINT));
    
    sendResponse([
        'success' => true,
        'system' => 'auto_detection',
        'status' => $status,
        'uptime_minutes' => $uptimeMinutes,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

function updateAutoSystemStatus() {
    $input = getRequestData();
    
    if (!isset($input['auto_detection_active'])) {
        sendError('Missing auto_detection_active parameter', 400);
        return;
    }
    
    global $SYSTEM_STATUS_FILE;
    $status = json_decode(file_get_contents($SYSTEM_STATUS_FILE), true);
    
    $status['auto_detection_active'] = (bool)$input['auto_detection_active'];
    $status['last_updated'] = date('Y-m-d H:i:s');
    
    if (isset($input['detection_count'])) {
        $status['detection_count'] = (int)$input['detection_count'];
    }
    
    if (isset($input['last_detection_time'])) {
        $status['last_detection_time'] = $input['last_detection_time'];
    }
    
    file_put_contents($SYSTEM_STATUS_FILE, json_encode($status, JSON_PRETTY_PRINT));
    
    logSystemEvent('SYSTEM_STATUS_CHANGE', 
        'Auto detection ' . ($status['auto_detection_active'] ? 'STARTED' : 'STOPPED'));
    
    sendResponse([
        'success' => true,
        'message' => 'Auto system status updated',
        'status' => $status,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

function getAutoDetections() {
    global $AUTO_DETECTIONS_FILE;
    
    $data = json_decode(file_get_contents($AUTO_DETECTIONS_FILE), true);
    
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    
    $detections = array_slice($data['detections'], $offset, $limit);
    
    sendResponse([
        'success' => true,
        'system' => 'auto_detection',
        'detections' => $detections,
        'total' => count($data['detections']),
        'count' => $data['count'],
        'limit' => $limit,
        'offset' => $offset,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

function addAutoDetection() {
    $input = getRequestData();
    
    $required = ['type', 'confidence', 'location', 'sensor_data'];
    foreach ($required as $field) {
        if (!isset($input[$field])) {
            sendError("Missing required field: $field", 400);
            return;
        }
    }
    
    global $AUTO_DETECTIONS_FILE, $SYSTEM_STATUS_FILE;
    
    $detectionsData = json_decode(file_get_contents($AUTO_DETECTIONS_FILE), true);
    $statusData = json_decode(file_get_contents($SYSTEM_STATUS_FILE), true);
    
    $detectionId = 'AUTO-' . date('Ymd-His') . '-' . uniqid();
    
    $detection = [
        'id' => $detectionId,
        'type' => $input['type'],
        'confidence' => (float)$input['confidence'],
        'location' => $input['location'],
        'latitude' => $input['latitude'] ?? null,
        'longitude' => $input['longitude'] ?? null,
        'sensor_data' => $input['sensor_data'],
        'timestamp' => date('Y-m-d H:i:s'),
        'status' => 'detected',
        'processed' => false,
        'police_alert_sent' => false,
        'hospital_alert_sent' => false
    ];
    
    array_unshift($detectionsData['detections'], $detection);
    $detectionsData['count']++;
    
    $statusData['detection_count']++;
    $statusData['last_detection_time'] = date('H:i:s');
    $statusData['last_updated'] = date('Y-m-d H:i:s');
    
    file_put_contents($AUTO_DETECTIONS_FILE, json_encode($detectionsData, JSON_PRETTY_PRINT));
    file_put_contents($SYSTEM_STATUS_FILE, json_encode($statusData, JSON_PRETTY_PRINT));
    
    logSystemEvent('AUTO_DETECTION', 
        "New {$input['type']} detected with {$input['confidence']}% confidence");
    
    // Also trigger main accident system if confidence is high
    if ($input['confidence'] >= 80) {
        triggerMainAccidentFromAutoDetection($detection);
    }
    
    sendResponse([
        'success' => true,
        'message' => 'Auto detection recorded',
        'detection_id' => $detectionId,
        'detection' => $detection,
        'total_detections' => $detectionsData['count'],
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

function getAutoSensorData() {
    global $SENSOR_DATA_FILE;
    
    $data = json_decode(file_get_contents($SENSOR_DATA_FILE), true);
    
    if (empty($data['sensors']) || strtotime($data['last_updated']) < time() - 60) {
        $data = generateSimulatedSensorData();
    }
    
    sendResponse([
        'success' => true,
        'system' => 'auto_detection',
        'sensors' => $data['sensors'],
        'last_updated' => $data['last_updated'],
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

function updateAutoSensorData() {
    $input = getRequestData();
    
    if (!isset($input['sensors']) || !is_array($input['sensors'])) {
        sendError('Missing or invalid sensors data', 400);
        return;
    }
    
    global $SENSOR_DATA_FILE;
    
    $sensorData = [
        'sensors' => $input['sensors'],
        'last_updated' => date('Y-m-d H:i:s')
    ];
    
    file_put_contents($SENSOR_DATA_FILE, json_encode($sensorData, JSON_PRETTY_PRINT));
    
    sendResponse([
        'success' => true,
        'message' => 'Sensor data updated',
        'sensors' => $input['sensors'],
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

function getAutoAlerts() {
    global $POLICE_ALERTS_FILE;
    
    $data = json_decode(file_get_contents($POLICE_ALERTS_FILE), true);
    
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $type = isset($_GET['type']) ? $_GET['type'] : null;
    
    $alerts = $data['alerts'];
    if ($type) {
        $alerts = array_filter($alerts, function($alert) use ($type) {
            return strpos($alert['type'], $type) !== false;
        });
        $alerts = array_values($alerts);
    }
    
    $alerts = array_slice($alerts, 0, $limit);
    
    sendResponse([
        'success' => true,
        'system' => 'auto_detection',
        'alerts' => $alerts,
        'total' => count($data['alerts']),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

function addAutoAlert() {
    $input = getRequestData();
    
    $required = ['type', 'message'];
    foreach ($required as $field) {
        if (!isset($input[$field])) {
            sendError("Missing required field: $field", 400);
            return;
        }
    }
    
    global $POLICE_ALERTS_FILE;
    
    $data = json_decode(file_get_contents($POLICE_ALERTS_FILE), true);
    
    $alertId = 'AUTO-ALERT-' . (++$data['last_alert_id']);
    
    $alert = [
        'id' => $alertId,
        'type' => $input['type'],
        'message' => $input['message'],
        'priority' => $input['priority'] ?? 'medium',
        'timestamp' => date('Y-m-d H:i:s'),
        'status' => 'pending',
        'acknowledged' => false,
        'source' => $input['source'] ?? 'auto_detection_system'
    ];
    
    array_unshift($data['alerts'], $alert);
    
    if (count($data['alerts']) > 1000) {
        $data['alerts'] = array_slice($data['alerts'], 0, 1000);
    }
    
    file_put_contents($POLICE_ALERTS_FILE, json_encode($data, JSON_PRETTY_PRINT));
    
    sendResponse([
        'success' => true,
        'message' => 'Auto alert added',
        'alert_id' => $alertId,
        'alert' => $alert,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

function getAutoSystemInfo() {
    global $SYSTEM_STATUS_FILE, $AUTO_DETECTIONS_FILE, $POLICE_ALERTS_FILE;
    
    $status = json_decode(file_get_contents($SYSTEM_STATUS_FILE), true);
    $detections = json_decode(file_get_contents($AUTO_DETECTIONS_FILE), true);
    $alerts = json_decode(file_get_contents($POLICE_ALERTS_FILE), true);
    
    $today = date('Y-m-d');
    $todayDetections = array_filter($detections['detections'], function($det) use ($today) {
        return strpos($det['timestamp'], $today) === 0;
    });
    
    $highConfidenceDetections = array_filter($detections['detections'], function($det) {
        return $det['confidence'] >= 90;
    });
    
    $pendingAlerts = array_filter($alerts['alerts'], function($alert) {
        return $alert['status'] === 'pending';
    });
    
    sendResponse([
        'success' => true,
        'system' => 'auto_detection',
        'system_info' => [
            'auto_detection_active' => $status['auto_detection_active'],
            'total_detections' => $detections['count'],
            'detections_today' => count($todayDetections),
            'high_confidence_detections' => count($highConfidenceDetections),
            'total_alerts' => count($alerts['alerts']),
            'pending_alerts' => count($pendingAlerts),
            'system_uptime' => $status['system_uptime'],
            'average_response_time' => $status['response_time'],
            'detection_accuracy' => $status['accuracy'],
            'last_detection' => $status['last_detection_time'],
            'last_updated' => $status['last_updated']
        ],
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

function controlAutoSystem() {
    $input = getRequestData();
    
    if (!isset($input['action'])) {
        sendError('Missing action parameter', 400);
        return;
    }
    
    global $SYSTEM_STATUS_FILE;
    $status = json_decode(file_get_contents($SYSTEM_STATUS_FILE), true);
    
    $action = strtolower($input['action']);
    $message = '';
    
    switch ($action) {
        case 'start':
            $status['auto_detection_active'] = true;
            $message = 'Auto detection system STARTED';
            break;
        case 'stop':
            $status['auto_detection_active'] = false;
            $message = 'Auto detection system STOPPED';
            break;
        case 'test':
            $message = 'System test initiated';
            simulateAutoTestDetection();
            break;
        case 'reset':
            $status['detection_count'] = 0;
            $message = 'Detection counter RESET';
            break;
        case 'simulate':
            $message = 'Manual detection simulation triggered';
            simulateAutoManualDetection();
            break;
        default:
            sendError('Invalid action', 400);
            return;
    }
    
    $status['last_updated'] = date('Y-m-d H:i:s');
    file_put_contents($SYSTEM_STATUS_FILE, json_encode($status, JSON_PRETTY_PRINT));
    
    logSystemEvent('SYSTEM_CONTROL', $message);
    
    sendResponse([
        'success' => true,
        'message' => $message,
        'action' => $action,
        'auto_detection_active' => $status['auto_detection_active'],
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

function getAutoPoliceAlerts() {
    global $POLICE_ALERTS_FILE;
    
    $data = json_decode(file_get_contents($POLICE_ALERTS_FILE), true);
    
    $policeAlerts = array_filter($data['alerts'], function($alert) {
        return stripos($alert['type'], 'police') !== false || 
               stripos($alert['source'], 'police') !== false;
    });
    
    $policeAlerts = array_slice(array_values($policeAlerts), 0, 50);
    
    sendResponse([
        'success' => true,
        'system' => 'auto_detection',
        'police_alerts' => $policeAlerts,
        'total_police_alerts' => count($policeAlerts),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

function sendAutoPoliceAlert() {
    $input = getRequestData();
    
    $required = ['message', 'location', 'severity'];
    foreach ($required as $field) {
        if (!isset($input[$field])) {
            sendError("Missing required field: $field", 400);
            return;
        }
    }
    
    global $POLICE_ALERTS_FILE;
    
    $data = json_decode(file_get_contents($POLICE_ALERTS_FILE), true);
    
    $alertId = 'AUTO-POLICE-' . (++$data['last_alert_id']);
    
    $alert = [
        'id' => $alertId,
        'type' => 'POLICE_EMERGENCY',
        'message' => $input['message'],
        'location' => $input['location'],
        'severity' => $input['severity'],
        'latitude' => $input['latitude'] ?? null,
        'longitude' => $input['longitude'] ?? null,
        'timestamp' => date('Y-m-d H:i:s'),
        'status' => 'sent',
        'acknowledged' => false,
        'source' => 'auto_detection_system',
        'priority' => $input['priority'] ?? 'high',
        'police_station' => $input['police_station'] ?? 'Nearest Station'
    ];
    
    array_unshift($data['alerts'], $alert);
    
    file_put_contents($POLICE_ALERTS_FILE, json_encode($data, JSON_PRETTY_PRINT));
    
    logSystemEvent('POLICE_DISPATCH', 
        "Police alert sent for {$input['severity']} incident at {$input['location']}");
    
    sendResponse([
        'success' => true,
        'message' => 'Auto police alert sent',
        'alert_id' => $alertId,
        'alert' => $alert,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

function getSystemLogs() {
    global $SYSTEM_LOGS_FILE;
    
    if (!file_exists($SYSTEM_LOGS_FILE)) {
        sendResponse([
            'success' => true,
            'logs' => [],
            'total' => 0,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        return;
    }
    
    $data = json_decode(file_get_contents($SYSTEM_LOGS_FILE), true);
    
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
    $logs = array_slice($data['logs'], 0, $limit);
    
    sendResponse([
        'success' => true,
        'system' => 'auto_detection',
        'logs' => $logs,
        'total' => count($data['logs']),
        'limit' => $limit,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

// ==== MAIN ACCIDENT SYSTEM FUNCTIONS ====
function handleMainAccidentRequest($method, $action) {
    if ($method === 'POST') {
        $input = getRequestData();
        $result = processAccidentData($input);
        echo json_encode($result);
    } elseif ($method === 'GET') {
        switch ($action) {
            case 'get_accidents':
                $result = getLogs('accidents');
                break;
            case 'get_alerts':
                $result = getLogs('alerts');
                break;
            case 'get_sms_logs':
                $result = getLogs('sms');
                break;
            case 'get_stats':
                $accidents = getLogs('accidents');
                $alerts = getLogs('alerts');
                $sms = getLogs('sms');
                
                $result = [
                    'success' => true,
                    'system' => 'main',
                    'stats' => [
                        'total_accidents' => $accidents['count'] ?? 0,
                        'total_alerts' => $alerts['count'] ?? 0,
                        'total_sms' => $sms['count'] ?? 0,
                        'last_updated' => date('Y-m-d H:i:s')
                    ]
                ];
                break;
            default:
                $result = [
                    'success' => true,
                    'message' => 'Accident Detection API',
                    'version' => '2.1',
                    'systems' => [
                        'main' => 'Main Accident System',
                        'auto' => 'Auto Detection System'
                    ],
                    'endpoints' => [
                        'POST /' => 'Submit accident data (main system)',
                        'GET ?action=get_accidents' => 'Get accident logs (main)',
                        'GET ?action=get_alerts' => 'Get alert logs (main)',
                        'GET ?action=get_sms_logs' => 'Get SMS logs (main)',
                        'GET ?action=get_stats' => 'Get system statistics (main)',
                        'GET ?system=auto&action=get_status' => 'Get auto system status',
                        'GET ?system=auto&action=get_detections' => 'Get auto detections'
                    ]
                ];
        }
        echo json_encode($result);
    } else {
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
}

// Main accident system functions
function processAccidentData($data) {
    global $ACCIDENT_LOG_FILE, $ALERT_LOG_FILE, $SMS_LOG_FILE;
    
    $response = [
        'success' => false,
        'message' => '',
        'alert_id' => null,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    try {
        $required = ['severity', 'time', 'coordinates', 'location', 'weather'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }
        
        $accidentId = 'ACC-' . date('Ymd-His') . '-' . uniqid();
        
        $accidentRecord = [
            'id' => $accidentId,
            'severity' => $data['severity'],
            'detection_time' => $data['time'],
            'coordinates' => $data['coordinates'],
            'location' => $data['location'],
            'weather' => $data['weather'],
            'hospital_type' => $data['hospital_type'] ?? null,
            'logged_at' => date('Y-m-d H:i:s'),
            'status' => 'detected'
        ];
        
        $accidents = json_decode(file_get_contents($ACCIDENT_LOG_FILE), true);
        $accidents[] = $accidentRecord;
        file_put_contents($ACCIDENT_LOG_FILE, json_encode($accidents, JSON_PRETTY_PRINT));
        
        $alertResults = triggerEmergencyAlerts($accidentRecord);
        
        $alerts = json_decode(file_get_contents($ALERT_LOG_FILE), true);
        $alerts = array_merge($alerts, $alertResults);
        file_put_contents($ALERT_LOG_FILE, json_encode($alerts, JSON_PRETTY_PRINT));
        
        logSMSNotifications($accidentRecord, $alertResults);
        
        $response['success'] = true;
        $response['message'] = 'Accident logged and emergency services alerted';
        $response['alert_id'] = $accidentId;
        $response['alerts_sent'] = count($alertResults);
        
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
    
    return $response;
}

function triggerEmergencyAlerts($accident) {
    $alerts = [];
    $timestamp = date('Y-m-d H:i:s');
    
    $priority = 'medium';
    switch ($accident['severity']) {
        case 'critical': $priority = 'highest'; break;
        case 'serious': $priority = 'high'; break;
        case 'minor': $priority = 'medium'; break;
    }
    
    // Police alert
    $policeAlert = [
        'id' => 'ALERT-' . uniqid(),
        'accident_id' => $accident['id'],
        'service' => 'police',
        'message' => createPoliceAlertMessage($accident),
        'priority' => $priority,
        'status' => 'sent',
        'sent_at' => $timestamp,
        'method' => 'sms_api'
    ];
    $alerts[] = $policeAlert;
    
    // Hospital alert - determine which hospital based on severity
    $hospitalType = 'small';
    $hospitalService = 'local_clinic';
    
    if ($accident['severity'] === 'serious' || $accident['severity'] === 'critical') {
        $hospitalType = 'big';
        $hospitalService = $accident['severity'] === 'critical' ? 'trauma_center' : 'general_hospital';
    }
    
    $hospitalAlert = [
        'id' => 'ALERT-' . uniqid(),
        'accident_id' => $accident['id'],
        'service' => $hospitalService,
        'hospital_type' => $hospitalType,
        'message' => createHospitalAlertMessage($accident),
        'priority' => $priority,
        'status' => 'sent',
        'sent_at' => $timestamp,
        'method' => 'sms_api'
    ];
    $alerts[] = $hospitalAlert;
    
    if ($accident['severity'] === 'critical') {
        $emergencyAlert = [
            'id' => 'ALERT-' . uniqid(),
            'accident_id' => $accident['id'],
            'service' => 'fire_rescue',
            'message' => createEmergencyAlertMessage($accident),
            'priority' => 'highest',
            'status' => 'sent',
            'sent_at' => $timestamp,
            'method' => 'sms_api'
        ];
        $alerts[] = $emergencyAlert;
    }
    
    return $alerts;
}

function createPoliceAlertMessage($accident) {
    $mapLink = "https://www.google.com/maps?q=" . urlencode($accident['coordinates']);
    return sprintf(
        "🚨 ACCIDENT ALERT\nSeverity: %s\nTime: %s\nLocation: %s\nCoordinates: %s\nMap: %s\nWeather: %s\nRespond immediately.",
        strtoupper($accident['severity']),
        $accident['detection_time'],
        $accident['location'],
        $accident['coordinates'],
        $mapLink,
        $accident['weather']
    );
}

function createHospitalAlertMessage($accident) {
    $mapLink = "https://www.google.com/maps?q=" . urlencode($accident['coordinates']);
    
    $hospitalType = $accident['severity'] ?? 'minor';
    $hospitalName = '';
    
    switch($hospitalType) {
        case 'minor': $hospitalName = 'Local Community Clinic'; break;
        case 'serious': $hospitalName = 'City General Hospital (Main)'; break;
        case 'critical': $hospitalName = 'Regional Trauma Center'; break;
        default: $hospitalName = 'Nearest Medical Facility';
    }
    
    return sprintf(
        "🏥 HOSPITAL ALERT - %s\nAccident Severity: %s\nTime: %s\nLocation: %s\nMap: %s\nWeather: %s\nPrepare emergency response.",
        $hospitalName,
        strtoupper($accident['severity']),
        $accident['detection_time'],
        $accident['location'],
        $mapLink,
        $accident['weather']
    );
}

function createEmergencyAlertMessage($accident) {
    $mapLink = "https://www.google.com/maps?q=" . urlencode($accident['coordinates']);
    return sprintf(
        "🚨🚑🚓 CRITICAL ACCIDENT MULTI-AGENCY ALERT\nSeverity: CRITICAL\nTime: %s\nLocation: %s\nCoordinates: %s\nMap: %s\nAll emergency services required.",
        $accident['detection_time'],
        $accident['location'],
        $accident['coordinates'],
        $mapLink
    );
}

function logSMSNotifications($accident, $alerts) {
    global $SMS_LOG_FILE;
    
    $smsLog = json_decode(file_get_contents($SMS_LOG_FILE), true);
    
    foreach ($alerts as $alert) {
        $smsEntry = [
            'id' => 'SMS-' . uniqid(),
            'alert_id' => $alert['id'],
            'accident_id' => $accident['id'],
            'recipient' => $alert['service'],
            'message' => $alert['message'],
            'status' => 'delivered',
            'sent_at' => $alert['sent_at'],
            'simulated' => true
        ];
        $smsLog[] = $smsEntry;
    }
    
    file_put_contents($SMS_LOG_FILE, json_encode($smsLog, JSON_PRETTY_PRINT));
}

function getLogs($type) {
    global $ACCIDENT_LOG_FILE, $ALERT_LOG_FILE, $SMS_LOG_FILE;
    
    switch ($type) {
        case 'accidents': $logFile = $ACCIDENT_LOG_FILE; break;
        case 'alerts': $logFile = $ALERT_LOG_FILE; break;
        case 'sms': $logFile = $SMS_LOG_FILE; break;
        default: return ['error' => 'Invalid log type'];
    }
    
    if (file_exists($logFile)) {
        $logs = json_decode(file_get_contents($logFile), true);
        return ['success' => true, 'data' => $logs, 'count' => count($logs)];
    }
    
    return ['error' => 'Log file not found'];
}

// ==== HELPER FUNCTIONS FOR BOTH SYSTEMS ====

function getRequestData() {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (empty($input)) {
        $input = $_POST;
    }
    
    if (empty($input) && !empty($_GET)) {
        $input = $_GET;
    }
    
    return $input;
}

function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit();
}

function sendError($message, $statusCode = 400) {
    sendResponse([
        'success' => false,
        'error' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ], $statusCode);
}

function generateSimulatedSensorData() {
    global $SENSOR_DATA_FILE;
    
    $sensors = [
        [
            'name' => 'accelerometer',
            'type' => 'motion',
            'value' => (rand(5, 20) / 10) . 'g',
            'status' => 'active',
            'unit' => 'g-force',
            'threshold' => '3.5g'
        ],
        [
            'name' => 'gps_module',
            'type' => 'location',
            'value' => 'Connected',
            'status' => 'active',
            'accuracy' => '±' . (rand(1, 5)) . 'm',
            'satellites' => rand(8, 15)
        ],
        [
            'name' => 'sound_sensor',
            'type' => 'audio',
            'value' => rand(30, 50) . 'dB',
            'status' => 'active',
            'unit' => 'decibels',
            'threshold' => '85dB'
        ],
        [
            'name' => 'camera_ai',
            'type' => 'visual',
            'value' => rand(95, 99) . '%',
            'status' => 'active',
            'unit' => 'confidence'
        ]
    ];
    
    $data = [
        'sensors' => $sensors,
        'last_updated' => date('Y-m-d H:i:s')
    ];
    
    file_put_contents($SENSOR_DATA_FILE, json_encode($data, JSON_PRETTY_PRINT));
    
    return $data;
}

function simulateAutoTestDetection() {
    global $AUTO_DETECTIONS_FILE, $SYSTEM_STATUS_FILE;
    
    $detectionsData = json_decode(file_get_contents($AUTO_DETECTIONS_FILE), true);
    $statusData = json_decode(file_get_contents($SYSTEM_STATUS_FILE), true);
    
    $detectionId = 'TEST-' . date('Ymd-His') . '-' . uniqid();
    
    $detection = [
        'id' => $detectionId,
        'type' => 'TEST_DETECTION',
        'confidence' => 99.5,
        'location' => 'Test Location',
        'latitude' => '18.4514022',
        'longitude' => '83.6639364',
        'sensor_data' => [
            'accelerometer' => '3.8g',
            'sound_sensor' => '87dB',
            'camera_ai' => '99%'
        ],
        'timestamp' => date('Y-m-d H:i:s'),
        'status' => 'test',
        'processed' => true
    ];
    
    array_unshift($detectionsData['detections'], $detection);
    $detectionsData['count']++;
    
    $statusData['detection_count']++;
    $statusData['last_detection_time'] = date('H:i:s');
    $statusData['last_updated'] = date('Y-m-d H:i:s');
    
    file_put_contents($AUTO_DETECTIONS_FILE, json_encode($detectionsData, JSON_PRETTY_PRINT));
    file_put_contents($SYSTEM_STATUS_FILE, json_encode($statusData, JSON_PRETTY_PRINT));
    
    logSystemEvent('SYSTEM_TEST', 'System test completed successfully');
    
    return $detectionId;
}

function simulateAutoManualDetection() {
    global $AUTO_DETECTIONS_FILE, $SYSTEM_STATUS_FILE;
    
    $detectionsData = json_decode(file_get_contents($AUTO_DETECTIONS_FILE), true);
    $statusData = json_decode(file_get_contents($SYSTEM_STATUS_FILE), true);
    
    $detectionId = 'MANUAL-' . date('Ymd-His') . '-' . uniqid();
    
    $types = ['IMPACT', 'COLLISION', 'ROLLOVER'];
    $type = $types[array_rand($types)];
    
    $detection = [
        'id' => $detectionId,
        'type' => $type,
        'confidence' => rand(85, 98) + (rand(0, 99) / 100),
        'location' => 'Simulated Location ' . rand(1, 100),
        'latitude' => (18.4514022 + (rand(-500, 500) / 10000)) . '',
        'longitude' => (83.6639364 + (rand(-500, 500) / 10000)) . '',
        'sensor_data' => [
            'accelerometer' => (rand(35, 80) / 10) . 'g',
            'sound_sensor' => rand(60, 95) . 'dB',
            'camera_ai' => rand(90, 99) . '%'
        ],
        'timestamp' => date('Y-m-d H:i:s'),
        'status' => 'detected',
        'processed' => false
    ];
    
    array_unshift($detectionsData['detections'], $detection);
    $detectionsData['count']++;
    
    $statusData['detection_count']++;
    $statusData['last_detection_time'] = date('H:i:s');
    $statusData['last_updated'] = date('Y-m-d H:i:s');
    
    file_put_contents($AUTO_DETECTIONS_FILE, json_encode($detectionsData, JSON_PRETTY_PRINT));
    file_put_contents($SYSTEM_STATUS_FILE, json_encode($statusData, JSON_PRETTY_PRINT));
    
    logSystemEvent('MANUAL_SIMULATION', "Manual $type detection simulated");
    
    return $detectionId;
}

function logSystemEvent($eventType, $description) {
    global $SYSTEM_LOGS_FILE;
    
    if (!file_exists($SYSTEM_LOGS_FILE)) {
        file_put_contents($SYSTEM_LOGS_FILE, json_encode(['logs' => []], JSON_PRETTY_PRINT));
    }
    
    $data = json_decode(file_get_contents($SYSTEM_LOGS_FILE), true);
    
    $logEntry = [
        'id' => 'LOG-' . date('YmdHis') . '-' . uniqid(),
        'event_type' => $eventType,
        'description' => $description,
        'timestamp' => date('Y-m-d H:i:s'),
        'severity' => 'info'
    ];
    
    array_unshift($data['logs'], $logEntry);
    
    if (count($data['logs']) > 1000) {
        $data['logs'] = array_slice($data['logs'], 0, 1000);
    }
    
    file_put_contents($SYSTEM_LOGS_FILE, json_encode($data, JSON_PRETTY_PRINT));
    
    return $logEntry['id'];
}

function triggerMainAccidentFromAutoDetection($autoDetection) {
    // When auto detection has high confidence, trigger main accident system
    if ($autoDetection['confidence'] >= 80) {
        $severity = 'medium';
        if ($autoDetection['confidence'] >= 90) $severity = 'serious';
        if ($autoDetection['confidence'] >= 95) $severity = 'critical';
        
        $accidentData = [
            'severity' => $severity,
            'time' => $autoDetection['timestamp'],
            'coordinates' => $autoDetection['latitude'] . ',' . $autoDetection['longitude'],
            'location' => $autoDetection['location'],
            'weather' => 'Clear, 25°C',
            'source' => 'auto_detection',
            'auto_detection_id' => $autoDetection['id']
        ];
        
        // Process through main system
        processAccidentData($accidentData);
        
        logSystemEvent('SYSTEM_INTEGRATION', 
            "Auto detection {$autoDetection['id']} triggered main accident system");
    }
}
?>