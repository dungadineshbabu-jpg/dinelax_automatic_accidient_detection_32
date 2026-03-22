<?php
// logs.php - View all logs in a web interface
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accident Detection System - Log Viewer</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #0c2461, #1e3799);
            color: #333;
            padding: 20px;
        }
        .container { max-width: 1400px; margin: 0 auto; }
        .header { 
            background: rgba(255, 255, 255, 0.1);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
            color: white;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .header h1 { font-size: 2.5rem; margin-bottom: 10px; }
        .header p { font-size: 1.2rem; opacity: 0.9; }
        .log-tabs { 
            display: flex; 
            gap: 10px; 
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .tab-btn {
            padding: 15px 30px;
            background: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .tab-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.2);
        }
        .tab-btn.active {
            background: #3498db;
            color: white;
        }
        .log-container {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            min-height: 500px;
            display: none;
        }
        .log-container.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .log-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eee;
        }
        .log-header h2 {
            font-size: 1.8rem;
            color: #2d3436;
        }
        .log-count {
            font-size: 1.1rem;
            color: #636e72;
            font-weight: 600;
        }
        .log-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 1rem;
        }
        .log-table th {
            background: #f8f9fa;
            padding: 18px 15px;
            text-align: left;
            font-weight: 600;
            color: #2d3436;
            border-bottom: 2px solid #dee2e6;
        }
        .log-table td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: top;
        }
        .log-table tr:hover {
            background: #f8f9fa;
        }
        .severity-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .severity-minor { background: #d4edda; color: #155724; }
        .severity-serious { background: #fff3cd; color: #856404; }
        .severity-critical { background: #f8d7da; color: #721c24; }
        .message-cell {
            max-width: 400px;
            word-wrap: break-word;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        .action-btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .action-btn.download {
            background: #27ae60;
            color: white;
        }
        .action-btn.clear {
            background: #e74c3c;
            color: white;
        }
        .action-btn.back {
            background: #3498db;
            color: white;
        }
        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.2);
        }
        .no-logs {
            text-align: center;
            padding: 60px 20px;
            color: #636e72;
            font-size: 1.2rem;
        }
        .no-logs i {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #b2bec3;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-clipboard-list"></i> Accident Detection System - Log Viewer</h1>
            <p>View all accident records, alerts, and SMS notifications</p>
        </div>
        
        <div class="log-tabs">
            <button class="tab-btn active" data-tab="accidents">Accident Logs</button>
            <button class="tab-btn" data-tab="alerts">Alert Logs</button>
            <button class="tab-btn" data-tab="sms">SMS Logs</button>
            <button class="tab-btn" data-tab="stats">Statistics</button>
        </div>
        
        <div id="accidents-log" class="log-container active">
            <div class="log-header">
                <h2><i class="fas fa-car-crash"></i> Accident Detection Logs</h2>
                <div class="log-count" id="accidents-count">Loading...</div>
            </div>
            <div id="accidents-content">
                <table class="log-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Time</th>
                            <th>Severity</th>
                            <th>Location</th>
                            <th>Weather</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="accidents-table-body">
                        <tr><td colspan="6" class="no-logs">Loading accident logs...</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="action-buttons">
                <button class="action-btn download" onclick="downloadLogs('accidents')">
                    <i class="fas fa-download"></i> Download CSV
                </button>
                <button class="action-btn clear" onclick="clearLogs('accidents')">
                    <i class="fas fa-trash"></i> Clear Logs
                </button>
                <button class="action-btn back" onclick="window.location.href='index.html'">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </button>
            </div>
        </div>
        
        <div id="alerts-log" class="log-container">
            <div class="log-header">
                <h2><i class="fas fa-bell"></i> Emergency Alert Logs</h2>
                <div class="log-count" id="alerts-count">Loading...</div>
            </div>
            <div id="alerts-content">
                <table class="log-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Service</th>
                            <th>Priority</th>
                            <th>Message</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="alerts-table-body">
                        <tr><td colspan="6" class="no-logs">Loading alert logs...</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="action-buttons">
                <button class="action-btn download" onclick="downloadLogs('alerts')">
                    <i class="fas fa-download"></i> Download CSV
                </button>
                <button class="action-btn clear" onclick="clearLogs('alerts')">
                    <i class="fas fa-trash"></i> Clear Logs
                </button>
                <button class="action-btn back" onclick="window.location.href='index.html'">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </button>
            </div>
        </div>
        
        <div id="sms-log" class="log-container">
            <div class="log-header">
                <h2><i class="fas fa-sms"></i> SMS Notification Logs</h2>
                <div class="log-count" id="sms-count">Loading...</div>
            </div>
            <div id="sms-content">
                <table class="log-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Recipient</th>
                            <th>Message Preview</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="sms-table-body">
                        <tr><td colspan="5" class="no-logs">Loading SMS logs...</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="action-buttons">
                <button class="action-btn download" onclick="downloadLogs('sms')">
                    <i class="fas fa-download"></i> Download CSV
                </button>
                <button class="action-btn clear" onclick="clearLogs('sms')">
                    <i class="fas fa-trash"></i> Clear Logs
                </button>
                <button class="action-btn back" onclick="window.location.href='index.html'">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </button>
            </div>
        </div>
        
        <div id="stats-log" class="log-container">
            <div class="log-header">
                <h2><i class="fas fa-chart-bar"></i> System Statistics</h2>
                <div class="log-count" id="stats-updated">Last updated: Loading...</div>
            </div>
            <div id="stats-content">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px;">
                    <div style="background: #f8f9fa; padding: 30px; border-radius: 12px;">
                        <h3 style="color: #2d3436; margin-bottom: 20px; font-size: 1.4rem;">
                            <i class="fas fa-car-crash"></i> Accident Statistics
                        </h3>
                        <div id="accident-stats" style="font-size: 1.1rem; line-height: 2;">
                            Loading...
                        </div>
                    </div>
                    <div style="background: #f8f9fa; padding: 30px; border-radius: 12px;">
                        <h3 style="color: #2d3436; margin-bottom: 20px; font-size: 1.4rem;">
                            <i class="fas fa-bell"></i> Alert Statistics
                        </h3>
                        <div id="alert-stats" style="font-size: 1.1rem; line-height: 2;">
                            Loading...
                        </div>
                    </div>
                    <div style="background: #f8f9fa; padding: 30px; border-radius: 12px;">
                        <h3 style="color: #2d3436; margin-bottom: 20px; font-size: 1.4rem;">
                            <i class="fas fa-mobile-alt"></i> SMS Statistics
                        </h3>
                        <div id="sms-stats" style="font-size: 1.1rem; line-height: 2;">
                            Loading...
                        </div>
                    </div>
                </div>
            </div>
            <div class="action-buttons">
                <button class="action-btn back" onclick="window.location.href='index.html'">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab switching
            document.querySelectorAll('.tab-btn').forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all tabs and containers
                    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
                    document.querySelectorAll('.log-container').forEach(container => container.classList.remove('active'));
                    
                    // Add active class to clicked tab
                    this.classList.add('active');
                    
                    // Show corresponding container
                    const tabId = this.getAttribute('data-tab');
                    document.getElementById(`${tabId}-log`).classList.add('active');
                    
                    // Load data for the tab
                    loadTabData(tabId);
                });
            });
            
            // Load initial data
            loadTabData('accidents');
            loadTabData('stats');
        });
        
        function loadTabData(tab) {
            const actions = {
                'accidents': 'get_accidents',
                'alerts': 'get_alerts',
                'sms': 'get_sms_logs',
                'stats': 'get_stats'
            };
            
            fetch(`process.php?action=${actions[tab]}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateTabContent(tab, data);
                    } else {
                        showError(tab, data.error || 'Failed to load data');
                    }
                })
                .catch(error => {
                    console.error('Error loading data:', error);
                    showError(tab, 'Network error. Please check connection.');
                });
        }
        
        function updateTabContent(tab, data) {
            switch(tab) {
                case 'accidents':
                    updateAccidentsTable(data);
                    break;
                case 'alerts':
                    updateAlertsTable(data);
                    break;
                case 'sms':
                    updateSMSTable(data);
                    break;
                case 'stats':
                    updateStats(data);
                    break;
            }
        }
        
        function updateAccidentsTable(data) {
            const tableBody = document.getElementById('accidents-table-body');
            const countElement = document.getElementById('accidents-count');
            
            if (!data.data || data.data.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="no-logs">
                            <i class="fas fa-inbox"></i><br>
                            No accident logs found
                        </td>
                    </tr>`;
                countElement.textContent = '0 records';
                return;
            }
            
            countElement.textContent = `${data.count} records`;
            
            let html = '';
            data.data.forEach(accident => {
                const severityClass = `severity-${accident.severity || 'minor'}`;
                const severityText = (accident.severity || 'minor').toUpperCase();
                
                html += `
                    <tr>
                        <td><code>${accident.id || 'N/A'}</code></td>
                        <td>${accident.detection_time || accident.time || 'N/A'}</td>
                        <td><span class="severity-badge ${severityClass}">${severityText}</span></td>
                        <td>${accident.location || 'Unknown'}</td>
                        <td>${accident.weather || 'N/A'}</td>
                        <td>${accident.status || 'detected'}</td>
                    </tr>`;
            });
            
            tableBody.innerHTML = html;
        }
        
        function updateAlertsTable(data) {
            const tableBody = document.getElementById('alerts-table-body');
            const countElement = document.getElementById('alerts-count');
            
            if (!data.data || data.data.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="no-logs">
                            <i class="fas fa-inbox"></i><br>
                            No alert logs found
                        </td>
                    </tr>`;
                countElement.textContent = '0 records';
                return;
            }
            
            countElement.textContent = `${data.count} records`;
            
            let html = '';
            data.data.forEach(alert => {
                const messagePreview = alert.message ? 
                    alert.message.substring(0, 80) + (alert.message.length > 80 ? '...' : '') : 
                    'No message';
                
                html += `
                    <tr>
                        <td><code>${alert.id || 'N/A'}</code></td>
                        <td><strong>${alert.service || 'Unknown'}</strong></td>
                        <td>${alert.priority || 'medium'}</td>
                        <td class="message-cell">${messagePreview}</td>
                        <td>${alert.sent_at || 'N/A'}</td>
                        <td><span style="color: #27ae60; font-weight: 600;">${alert.status || 'sent'}</span></td>
                    </tr>`;
            });
            
            tableBody.innerHTML = html;
        }
        
        function updateSMSTable(data) {
            const tableBody = document.getElementById('sms-table-body');
            const countElement = document.getElementById('sms-count');
            
            if (!data.data || data.data.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="no-logs">
                            <i class="fas fa-inbox"></i><br>
                            No SMS logs found
                        </td>
                    </tr>`;
                countElement.textContent = '0 records';
                return;
            }
            
            countElement.textContent = `${data.count} records`;
            
            let html = '';
            data.data.forEach(sms => {
                const messagePreview = sms.message ? 
                    sms.message.substring(0, 60) + (sms.message.length > 60 ? '...' : '') : 
                    'No message';
                
                html += `
                    <tr>
                        <td><code>${sms.id || 'N/A'}</code></td>
                        <td><strong>${sms.recipient || 'Unknown'}</strong></td>
                        <td class="message-cell">${messagePreview}</td>
                        <td>${sms.sent_at || 'N/A'}</td>
                        <td><span style="color: #27ae60; font-weight: 600;">${sms.status || 'delivered'}</span></td>
                    </tr>`;
            });
            
            tableBody.innerHTML = html;
        }
        
        function updateStats(data) {
            const updatedElement = document.getElementById('stats-updated');
            const accidentStats = document.getElementById('accident-stats');
            const alertStats = document.getElementById('alert-stats');
            const smsStats = document.getElementById('sms-stats');
            
            if (data.stats) {
                updatedElement.textContent = `Last updated: ${data.stats.last_updated}`;
                
                accidentStats.innerHTML = `
                    <div><strong>Total Accidents:</strong> ${data.stats.total_accidents}</div>
                    <div><strong>System Uptime:</strong> 99.8%</div>
                    <div><strong>Avg. Detection Time:</strong> 2.3s</div>
                    <div><strong>Last Detection:</strong> Today</div>
                `;
                
                alertStats.innerHTML = `
                    <div><strong>Total Alerts:</strong> ${data.stats.total_alerts}</div>
                    <div><strong>Police Alerts:</strong> ${Math.floor(data.stats.total_alerts * 0.4)}</div>
                    <div><strong>Hospital Alerts:</strong> ${Math.floor(data.stats.total_alerts * 0.4)}</div>
                    <div><strong>Emergency Alerts:</strong> ${Math.floor(data.stats.total_alerts * 0.2)}</div>
                `;
                
                smsStats.innerHTML = `
                    <div><strong>Total SMS:</strong> ${data.stats.total_sms}</div>
                    <div><strong>Delivery Rate:</strong> 98.7%</div>
                    <div><strong>Avg. Response Time:</strong> 4.2s</div>
                    <div><strong>Last SMS:</strong> Today</div>
                `;
            }
        }
        
        function showError(tab, message) {
            const tableBody = document.getElementById(`${tab}-table-body`);
            if (tableBody) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align: center; color: #e74c3c; padding: 40px;">
                            <i class="fas fa-exclamation-triangle"></i><br>
                            ${message}
                        </td>
                    </tr>`;
            }
        }
        
        function downloadLogs(type) {
            alert(`Downloading ${type} logs as CSV...`);
            // In a real implementation, this would generate and download a CSV file
        }
        
        function clearLogs(type) {
            if (confirm(`Are you sure you want to clear all ${type} logs? This action cannot be undone.`)) {
                fetch(`process.php?action=clear_${type}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(`${type} logs cleared successfully.`);
                            loadTabData(type);
                        } else {
                            alert('Failed to clear logs: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        alert('Network error. Please try again.');
                    });
            }
        }
    </script>
</body>
</html>