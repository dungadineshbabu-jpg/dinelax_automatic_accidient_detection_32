document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded, initializing system...');
    
    // Initialize variables
    let accidentCount = 0;
    let alertLogs = [];
    let currentStatus = 'serious';
    let isSystemActive = true;
    
    // AUTO DETECTION SYSTEM VARIABLES
    let isAutoDetectionActive = true;
    let autoDetectionCount = 29;
    let lastAutoDetectionTime = "10:13:54 PM";
    let autoDetectionInterval = null;
    
    // DOM Elements - Main System
    const accidentStatusEl = document.getElementById('accident-status');
    const accidentTimeEl = document.getElementById('accident-time');
    const policeStatusEl = document.getElementById('police-status');
    const hospitalStatusEl = document.getElementById('hospital-status');
    const hospitalTypeEl = document.getElementById('hospital-type');
    const hospitalTypeDetailEl = document.getElementById('hospital-type-detail');
    const hospitalTypeBoxEl = document.getElementById('hospital-type-box');
    const mapLinkEl = document.getElementById('google-maps-link');
    const mapCoordinatesEl = document.getElementById('map-coordinates');
    
    const monitoringStatusEl = document.getElementById('monitoring-status');
    const policeMonitorEl = document.getElementById('police-monitor');
    const hospitalMonitorEl = document.getElementById('hospital-monitor');
    const severityLevelEl = document.getElementById('severity-level');
    const weatherConditionEl = document.getElementById('weather-condition');
    const locationLinkEl = document.getElementById('location-link');
    const currentTimeEl = document.getElementById('current-time');
    const alertLogContainer = document.getElementById('alert-log-container');
    
    // AUTO DETECTION DOM ELEMENTS
    const autoSystemStatusEl = document.getElementById('auto-system-status');
    const lastAutoDetectionEl = document.getElementById('last-auto-detection');
    const totalAutoDetectionsEl = document.getElementById('total-auto-detections');
    const startAutoBtn = document.getElementById('start-auto-detection');
    const stopAutoBtn = document.getElementById('stop-auto-detection');
    const testAutoBtn = document.getElementById('test-auto-system');
    
    // ADDED: Auto System Control Buttons
    const refreshAutoBtn = document.getElementById('refresh-auto-data');
    const resetAutoBtn = document.getElementById('reset-auto-system');
    const viewAutoLogsBtn = document.getElementById('view-auto-logs');
    
    // Sensor elements
    const accelValueEl = document.getElementById('accel-value');
    const gpsValueEl = document.getElementById('gps-value');
    const soundValueEl = document.getElementById('sound-value');
    const cameraValueEl = document.getElementById('camera-value');
    
    // Toast notification
    const toast = document.getElementById('notification-toast');
    const toastMessage = document.getElementById('toast-message');
    
    // Initialize system
    initSystem();
    
    function initSystem() {
        console.log('Initializing system...');
        
        // Set initial time
        updateCurrentTime();
        setInterval(updateCurrentTime, 1000);
        
        // Load existing logs from localStorage
        loadLogsFromStorage();
        
        // Set up all event listeners
        setupAllEventListeners();
        
        // Initialize auto detection system
        initAutoDetectionSystem();
        
        // Show initial system notification
        setTimeout(() => {
            showNotification('🚨 Automatic Accident Detection System Activated', 'success');
        }, 1000);
        
        console.log('System initialization complete');
    }
    
    function setupAllEventListeners() {
        console.log('Setting up ALL event listeners...');
        
        // Simulation buttons
        const simulateMinorBtn = document.getElementById('simulate-minor');
        const simulateSeriousBtn = document.getElementById('simulate-serious');
        const simulateCriticalBtn = document.getElementById('simulate-critical');
        const simulateRandomBtn = document.getElementById('simulate-random');
        const triggerManualBtn = document.getElementById('trigger-manual');
        
        if (simulateMinorBtn) {
            simulateMinorBtn.addEventListener('click', () => {
                console.log('Minor accident simulation clicked');
                simulateAccident('minor');
            });
        }
        
        if (simulateSeriousBtn) {
            simulateSeriousBtn.addEventListener('click', () => {
                console.log('Serious accident simulation clicked');
                simulateAccident('serious');
            });
        }
        
        if (simulateCriticalBtn) {
            simulateCriticalBtn.addEventListener('click', () => {
                console.log('Critical accident simulation clicked');
                simulateAccident('critical');
            });
        }
        
        if (simulateRandomBtn) {
            simulateRandomBtn.addEventListener('click', () => {
                console.log('Random accident simulation clicked');
                generateRandomAccident();
            });
        }
        
        if (triggerManualBtn) {
            triggerManualBtn.addEventListener('click', () => {
                console.log('Manual detection clicked');
                triggerManualDetection();
            });
        }
        
        // System control buttons
        const resetSystemBtn = document.getElementById('reset-system');
        const refreshDataBtn = document.getElementById('refresh-data');
        const viewLogsBtn = document.getElementById('view-logs');
        
        if (resetSystemBtn) {
            console.log('Found reset system button, adding listener...');
            resetSystemBtn.addEventListener('click', function() {
                console.log('Reset system button CLICKED!');
                resetSystem();
            });
        }
        
        if (refreshDataBtn) {
            console.log('Found refresh data button, adding listener...');
            refreshDataBtn.addEventListener('click', function() {
                console.log('Refresh data button CLICKED!');
                refreshData();
            });
        }
        
        if (viewLogsBtn) {
            console.log('Found view logs button, adding listener...');
            viewLogsBtn.addEventListener('click', function() {
                console.log('View logs button CLICKED!');
                viewAllLogs();
            });
        }
        
        // Map link click
        if (mapLinkEl) {
            mapLinkEl.addEventListener('click', function(e) {
                showNotification('Opening Google Maps with accident location...', 'info');
            });
        }
        
        if (locationLinkEl) {
            locationLinkEl.addEventListener('click', function(e) {
                showNotification('Opening location on map...', 'info');
            });
        }
        
        console.log('Event listeners setup complete');
    }
    
    // AUTO DETECTION SYSTEM FUNCTIONS
    function initAutoDetectionSystem() {
        console.log('Initializing Auto Detection System...');
        
        // Set initial values
        updateAutoDetectionDisplay();
        
        // Set up event listeners for auto detection controls
        setupAutoDetectionListeners();
        
        // Set up auto system control buttons
        setupAutoSystemControls();
        
        // Start auto detection simulation if active
        if (isAutoDetectionActive) {
            startAutoDetectionSimulation();
        }
        
        // Start sensor simulation
        startSensorSimulation();
        
        console.log('Auto Detection System initialized');
    }
    
    function setupAutoDetectionListeners() {
        console.log('Setting up auto detection listeners...');
        
        // Start Auto Detection
        if (startAutoBtn) {
            startAutoBtn.addEventListener('click', function() {
                console.log('Start Auto Detection clicked');
                if (!isAutoDetectionActive) {
                    isAutoDetectionActive = true;
                    if (autoSystemStatusEl) {
                        autoSystemStatusEl.textContent = 'ACTIVE';
                        autoSystemStatusEl.classList.remove('inactive');
                        autoSystemStatusEl.classList.add('active');
                    }
                    startAutoDetectionSimulation();
                    showNotification('🚀 Auto Detection System STARTED', 'success');
                    addLogEntry(new Date().toTimeString().split(' ')[0], 'Auto System', 'Auto detection started');
                } else {
                    showNotification('Auto Detection is already active', 'info');
                }
                updateAutoDetectionDisplay();
            });
        }
        
        // Stop Auto Detection
        if (stopAutoBtn) {
            stopAutoBtn.addEventListener('click', function() {
                console.log('Stop Auto Detection clicked');
                if (isAutoDetectionActive) {
                    isAutoDetectionActive = false;
                    if (autoSystemStatusEl) {
                        autoSystemStatusEl.textContent = 'INACTIVE';
                        autoSystemStatusEl.classList.remove('active');
                        autoSystemStatusEl.classList.add('inactive');
                    }
                    stopAutoDetectionSimulation();
                    showNotification('🛑 Auto Detection System STOPPED', 'warning');
                    addLogEntry(new Date().toTimeString().split(' ')[0], 'Auto System', 'Auto detection stopped');
                } else {
                    showNotification('Auto Detection is already stopped', 'info');
                }
                updateAutoDetectionDisplay();
            });
        }
        
        // Test Auto System
        if (testAutoBtn) {
            testAutoBtn.addEventListener('click', function() {
                console.log('Test Auto System clicked');
                if (!isAutoDetectionActive) {
                    showNotification('Please start Auto Detection first', 'warning');
                    return;
                }
                
                showNotification('🔧 Testing Auto Detection System...', 'info');
                
                // Simulate test sequence
                simulateTestSequence();
            });
        }
        
        console.log('Auto detection listeners setup complete');
    }
    
    // ADDED: Setup Auto System Control Buttons
    function setupAutoSystemControls() {
        console.log('Setting up auto system control buttons...');
        
        if (refreshAutoBtn) {
            refreshAutoBtn.addEventListener('click', function() {
                console.log('Refresh Auto Data clicked');
                refreshAutoData();
            });
        }
        
        if (resetAutoBtn) {
            resetAutoBtn.addEventListener('click', function() {
                console.log('Reset Auto System clicked');
                resetAutoSystem();
            });
        }
        
        if (viewAutoLogsBtn) {
            viewAutoLogsBtn.addEventListener('click', function() {
                console.log('View Auto Logs clicked');
                viewAutoLogs();
            });
        }
        
        console.log('Auto system control buttons setup complete');
    }
    
    // ADDED: Refresh Auto Data Function
    function refreshAutoData() {
        console.log('🔄 Refreshing auto data...');
        showNotification('Refreshing auto detection data...', 'info');
        
        // Update weather
        const weatherConditions = ['Clear', 'Cloudy', 'Rainy', 'Foggy', 'Windy'];
        const randomWeather = weatherConditions[Math.floor(Math.random() * weatherConditions.length)];
        const temp = Math.floor(Math.random() * 15) + 20;
        if (weatherConditionEl) weatherConditionEl.textContent = `${randomWeather}, ${temp}°C`;
        
        // Update time
        updateCurrentTime();
        
        // Update auto stats
        if (lastAutoDetectionEl) lastAutoDetectionEl.textContent = getCurrentTimeFormatted();
        if (totalAutoDetectionsEl) totalAutoDetectionsEl.textContent = 'A' + autoDetectionCount;
        
        // Update sensors
        if (accelValueEl) {
            const gForces = ['1.2g', '1.5g', '0.8g', '1.1g'];
            accelValueEl.textContent = gForces[Math.floor(Math.random() * gForces.length)];
        }
        if (gpsValueEl) {
            const gpsStatus = ['Tracking location', 'GPS active', 'Location locked'];
            gpsValueEl.textContent = gpsStatus[Math.floor(Math.random() * gpsStatus.length)];
        }
        if (soundValueEl) {
            const soundLevels = ['35dB', '42dB', '28dB', '31dB'];
            soundValueEl.textContent = 'Sound level: ' + soundLevels[Math.floor(Math.random() * soundLevels.length)];
        }
        if (cameraValueEl) {
            const cameraStatus = ['Visual analysis', 'AI processing', 'Camera ready'];
            cameraValueEl.textContent = cameraStatus[Math.floor(Math.random() * cameraStatus.length)];
        }
        
        addLogEntry(new Date().toTimeString().split(' ')[0], 'Auto System', 'Auto data refreshed');
        showNotification('✅ Auto data refreshed', 'success');
    }
    
    // ADDED: Reset Auto System Function
    function resetAutoSystem() {
        console.log('🔄 Resetting auto system...');
        
        if (confirm('Reset auto detection system?')) {
            autoDetectionCount = 29;
            lastAutoDetectionTime = "10:13:54 PM";
            isAutoDetectionActive = true;
            
            if (totalAutoDetectionsEl) totalAutoDetectionsEl.textContent = 'A29';
            if (lastAutoDetectionEl) lastAutoDetectionEl.textContent = '10:13:54 PM';
            if (autoSystemStatusEl) {
                autoSystemStatusEl.textContent = 'ACTIVE';
                autoSystemStatusEl.className = 'auto-status active';
            }
            
            // Reset sensors
            if (accelValueEl) accelValueEl.textContent = 'Detecting G-forces';
            if (gpsValueEl) gpsValueEl.textContent = 'Tracking location';
            if (soundValueEl) soundValueEl.textContent = 'Monitoring impact sounds';
            if (cameraValueEl) cameraValueEl.textContent = 'Visual analysis';
            
            addLogEntry(new Date().toTimeString().split(' ')[0], 'Auto System', 'Auto system reset');
            showNotification('✅ Auto system reset', 'success');
        }
    }
    
    // ADDED: View Auto Logs Function
    function viewAutoLogs() {
        console.log('📋 Viewing auto logs...');
        
        const autoLogs = [
            { time: getCurrentTimeFormatted(), event: 'Auto detection ACTIVE', status: 'success' },
            { time: '09:45:22 AM', event: 'Critical accident detected', status: 'warning' },
            { time: '09:15:44 AM', event: 'Serious accident detected', status: 'warning' },
            { time: '08:30:12 AM', event: 'System test passed', status: 'success' },
            { time: '07:55:33 AM', event: 'Auto detection STARTED', status: 'info' }
        ];
        
        let logMessage = '🤖 AUTO DETECTION LOGS:\n\n';
        autoLogs.forEach(log => {
            logMessage += `${log.time} - ${log.event}\n`;
        });
        logMessage += `\nTotal Auto Detections: A${autoDetectionCount}`;
        logMessage += `\nCurrent Status: ${isAutoDetectionActive ? 'ACTIVE' : 'INACTIVE'}`;
        
        alert(logMessage);
        showNotification('📋 Auto logs displayed', 'info');
    }
    
    function updateAutoDetectionDisplay() {
        console.log('Updating auto detection display...');
        
        if (lastAutoDetectionEl) {
            lastAutoDetectionEl.textContent = lastAutoDetectionTime;
        }
        
        if (totalAutoDetectionsEl) {
            totalAutoDetectionsEl.textContent = 'A' + autoDetectionCount;
        }
        
        // Update button states
        if (startAutoBtn) {
            startAutoBtn.disabled = isAutoDetectionActive;
        }
        
        if (stopAutoBtn) {
            stopAutoBtn.disabled = !isAutoDetectionActive;
        }
        
        if (testAutoBtn) {
            testAutoBtn.disabled = !isAutoDetectionActive;
        }
    }
    
    function startAutoDetectionSimulation() {
        console.log('Starting auto detection simulation...');
        
        // Clear any existing interval
        if (autoDetectionInterval) {
            clearInterval(autoDetectionInterval);
            autoDetectionInterval = null;
        }
        
        // Start new simulation (every 30-60 seconds)
        autoDetectionInterval = setInterval(() => {
            if (isAutoDetectionActive && Math.random() > 0.7) {
                simulateAutoAccidentDetection();
            }
        }, 30000);
        
        console.log('Auto detection simulation started');
    }
    
    function stopAutoDetectionSimulation() {
        console.log('Stopping auto detection simulation...');
        
        if (autoDetectionInterval) {
            clearInterval(autoDetectionInterval);
            autoDetectionInterval = null;
            console.log('Auto detection simulation stopped');
        }
    }
    
    function simulateAutoAccidentDetection() {
        console.log('Simulating auto accident detection...');
        
        // Only simulate if system is active
        if (!isAutoDetectionActive) return;
        
        // Generate random accident severity
        const rand = Math.random();
        let severity;
        if (rand < 0.7) severity = 'minor';
        else if (rand < 0.9) severity = 'serious';
        else severity = 'critical';
        
        console.log('Auto detecting:', severity, 'accident');
        
        // Update detection count and time
        autoDetectionCount++;
        lastAutoDetectionTime = getCurrentTimeFormatted();
        
        // Update display
        updateAutoDetectionDisplay();
        
        // Update sensor values to show detection
        updateSensorsForDetection();
        
        // Add to log
        const timeStr = new Date().toTimeString().split(' ')[0];
        addLogEntry(timeStr, 'Auto Detect', `${severity.toUpperCase()} accident detected`);
        
        // Show notification
        showNotification(`🤖 Auto Detection: ${severity.toUpperCase()} accident detected`, 'warning');
        
        // Simulate the actual accident after a short delay
        setTimeout(() => {
            simulateAccident(severity);
        }, 1000);
    }
    
    function simulateTestSequence() {
        console.log('Starting test sequence...');
        
        // Step 1: Check sensors
        if (accelValueEl) {
            accelValueEl.textContent = 'Testing...';
            accelValueEl.classList.add('alert');
        }
        if (gpsValueEl) {
            gpsValueEl.textContent = 'Testing...';
            gpsValueEl.classList.add('alert');
        }
        if (soundValueEl) {
            soundValueEl.textContent = 'Testing...';
            soundValueEl.classList.add('alert');
        }
        if (cameraValueEl) {
            cameraValueEl.textContent = 'Testing...';
            cameraValueEl.classList.add('alert');
        }
        
        setTimeout(() => {
            // Step 2: Simulate test detection
            if (accelValueEl) {
                accelValueEl.textContent = 'High G-force detected';
            }
            
            setTimeout(() => {
                // Step 3: Complete test
                if (accelValueEl) {
                    accelValueEl.textContent = 'Detecting G-forces';
                    accelValueEl.classList.remove('alert');
                }
                if (gpsValueEl) {
                    gpsValueEl.textContent = 'Tracking location';
                    gpsValueEl.classList.remove('alert');
                }
                if (soundValueEl) {
                    soundValueEl.textContent = 'Monitoring impact sounds';
                    soundValueEl.classList.remove('alert');
                }
                if (cameraValueEl) {
                    cameraValueEl.textContent = 'Visual analysis';
                    cameraValueEl.classList.remove('alert');
                }
                
                // Update detection count for test
                autoDetectionCount++;
                lastAutoDetectionTime = getCurrentTimeFormatted();
                updateAutoDetectionDisplay();
                
                // Add log entry
                const timeStr = new Date().toTimeString().split(' ')[0];
                addLogEntry(timeStr, 'Auto System', 'System test completed successfully');
                
                showNotification('✅ Auto System Test COMPLETED', 'success');
                
                console.log('Test sequence completed');
            }, 1000);
        }, 1500);
    }
    
    function startSensorSimulation() {
        console.log('Starting sensor simulation...');
        
        // Simulate changing sensor values every 3-5 seconds
        setInterval(() => {
            if (isAutoDetectionActive) {
                // Normal sensor fluctuations
                const sensorStates = {
                    accel: ['Detecting G-forces', 'Monitoring acceleration', 'G-force normal'],
                    gps: ['Tracking location', 'GPS active', 'Location locked'],
                    sound: ['Monitoring impact sounds', 'Sound levels normal', 'Listening'],
                    camera: ['Visual analysis', 'AI processing', 'Camera ready']
                };
                
                // Randomly update sensors occasionally
                if (Math.random() > 0.8) {
                    const accelIndex = Math.floor(Math.random() * sensorStates.accel.length);
                    const gpsIndex = Math.floor(Math.random() * sensorStates.gps.length);
                    const soundIndex = Math.floor(Math.random() * sensorStates.sound.length);
                    const cameraIndex = Math.floor(Math.random() * sensorStates.camera.length);
                    
                    if (accelValueEl) {
                        accelValueEl.textContent = sensorStates.accel[accelIndex];
                    }
                    if (gpsValueEl) {
                        gpsValueEl.textContent = sensorStates.gps[gpsIndex];
                    }
                    if (soundValueEl) {
                        soundValueEl.textContent = sensorStates.sound[soundIndex];
                    }
                    if (cameraValueEl) {
                        cameraValueEl.textContent = sensorStates.camera[cameraIndex];
                    }
                }
            }
        }, 5000);
        
        console.log('Sensor simulation started');
    }
    
    function updateSensorsForDetection() {
        console.log('Updating sensors for detection...');
        
        // Set all sensors to alert state
        if (accelValueEl) {
            accelValueEl.textContent = 'HIGH G-FORCE DETECTED';
            accelValueEl.classList.add('alert');
        }
        if (gpsValueEl) {
            gpsValueEl.textContent = 'LOCATION TRACKING ACTIVE';
            gpsValueEl.classList.add('alert');
        }
        if (soundValueEl) {
            soundValueEl.textContent = 'IMPACT SOUND DETECTED';
            soundValueEl.classList.add('alert');
        }
        if (cameraValueEl) {
            cameraValueEl.textContent = 'VISUAL IMPACT CONFIRMED';
            cameraValueEl.classList.add('alert');
        }
        
        // Return to normal after 3 seconds
        setTimeout(() => {
            if (isAutoDetectionActive) {
                if (accelValueEl) {
                    accelValueEl.textContent = 'Detecting G-forces';
                    accelValueEl.classList.remove('alert');
                }
                if (gpsValueEl) {
                    gpsValueEl.textContent = 'Tracking location';
                    gpsValueEl.classList.remove('alert');
                }
                if (soundValueEl) {
                    soundValueEl.textContent = 'Monitoring impact sounds';
                    soundValueEl.classList.remove('alert');
                }
                if (cameraValueEl) {
                    cameraValueEl.textContent = 'Visual analysis';
                    cameraValueEl.classList.remove('alert');
                }
                
                console.log('Sensors returned to normal');
            }
        }, 3000);
    }
    
    // MAIN SYSTEM FUNCTIONS
    function updateCurrentTime() {
        const now = new Date();
        const dateStr = now.toISOString().split('T')[0];
        const timeStr = now.toTimeString().split(' ')[0];
        const fullDateTime = `${dateStr} ${timeStr}`;
        
        // Update all time displays
        if (accidentTimeEl) accidentTimeEl.textContent = fullDateTime;
        if (currentTimeEl) currentTimeEl.textContent = timeStr;
    }
    
    function generateRandomAccident() {
        accidentCount++;
        const severities = ['minor', 'serious', 'critical'];
        const randomSeverity = severities[Math.floor(Math.random() * severities.length)];
        
        simulateAccident(randomSeverity);
    }
    
    function simulateAccident(severity) {
        if (!isSystemActive) return;
        
        currentStatus = severity;
        accidentCount++;
        
        // Generate accident data
        const accidentData = generateAccidentData(severity);
        
        // Update left panel
        updateDetectionPanel(accidentData);
        
        // Update right panel
        updateMonitoringPanel(accidentData);
        
        // Trigger emergency alerts
        triggerEmergencyAlerts(accidentData);
        
        // Log the accident
        logAccident(accidentData);
        
        // Show notification
        showNotification(`🚨 ${severity.toUpperCase()} ACCIDENT DETECTED! Emergency services alerted.`, 'warning');
    }
    
    function generateAccidentData(severity) {
        const now = new Date();
        const dateStr = now.toISOString().split('T')[0];
        const timeStr = now.toTimeString().split(' ')[0];
        
        // Generate random coordinates
        const baseLat = 18.4514022;
        const baseLng = 83.6639364;
        const lat = (baseLat + (Math.random() * 0.01 - 0.005)).toFixed(7);
        const lng = (baseLng + (Math.random() * 0.01 - 0.005)).toFixed(7);
        
        // Weather conditions
        const weatherConditions = ['Clear', 'Cloudy', 'Rainy', 'Foggy', 'Windy'];
        const randomWeather = weatherConditions[Math.floor(Math.random() * weatherConditions.length)];
        const temp = Math.floor(Math.random() * 15) + 20;
        
        // Location names
        const locations = [
            "Main Highway, Sector 5",
            "Downtown Crossing",
            "Bridge Approach Road",
            "Industrial Area, Zone 3",
            "Residential District, Block B"
        ];
        const randomLocation = locations[Math.floor(Math.random() * locations.length)];
        
        // Severity-specific data with HOSPITAL LOGIC
        let statusText, policeStatus, hospitalStatus, survivalProb, hospitalType;
        
        switch(severity) {
            case 'minor':
                statusText = 'Minor Accident Detected';
                policeStatus = 'Police Notified (Low Priority)';
                hospitalStatus = 'Local Clinic Alerted (SMS)';
                hospitalType = 'small';
                survivalProb = '95%';
                break;
            case 'serious':
                statusText = 'Serious Accident Detected Automatically';
                policeStatus = 'Police Station Notified (SMS)';
                hospitalStatus = 'City General Hospital Alerted (SMS)';
                hospitalType = 'big';
                survivalProb = '75%';
                break;
            case 'critical':
                statusText = 'CRITICAL ACCIDENT DETECTED!';
                policeStatus = 'POLICE ALERT - HIGH PRIORITY';
                hospitalStatus = 'TRAUMA CENTER EMERGENCY TEAM DISPATCHED';
                hospitalType = 'trauma_center';
                survivalProb = '45%';
                break;
        }
        
        return {
            severity: severity,
            status: statusText,
            time: `${dateStr} ${timeStr}`,
            policeStatus: policeStatus,
            hospitalStatus: hospitalStatus,
            hospitalType: hospitalType,
            coordinates: `${lat},${lng}`,
            mapUrl: `https://www.google.com/maps?q=${lat},${lng}`,
            location: randomLocation,
            weather: `${randomWeather}, ${temp}°C`,
            survivalProbability: survivalProb,
            timestamp: now.getTime()
        };
    }
    
    function updateDetectionPanel(data) {
        if (accidentStatusEl) accidentStatusEl.textContent = data.status;
        if (accidentTimeEl) accidentTimeEl.textContent = data.time;
        if (policeStatusEl) policeStatusEl.textContent = data.policeStatus;
        if (hospitalStatusEl) hospitalStatusEl.textContent = data.hospitalStatus;
        if (mapCoordinatesEl) mapCoordinatesEl.textContent = data.mapUrl;
        if (mapLinkEl) mapLinkEl.href = data.mapUrl;
        
        // Update hospital type display
        updateHospitalTypeDisplay(data.severity, data.hospitalType);
        
        // Update status color
        if (accidentStatusEl) {
            accidentStatusEl.className = 'status-value';
            switch(data.severity) {
                case 'minor':
                    accidentStatusEl.classList.add('minor');
                    accidentStatusEl.style.color = '#27ae60';
                    break;
                case 'serious':
                    accidentStatusEl.classList.add('serious');
                    accidentStatusEl.style.color = '#e74c3c';
                    break;
                case 'critical':
                    accidentStatusEl.classList.add('critical');
                    accidentStatusEl.style.color = '#c0392b';
                    accidentStatusEl.style.animation = 'pulse 1s infinite';
                    break;
            }
        }
    }
    
    function updateHospitalTypeDisplay(severity, hospitalType) {
        let hospitalText = '';
        let hospitalClass = '';
        let detailClass = '';
        
        switch(severity) {
            case 'minor':
                hospitalText = 'Local Clinic / Small Hospital';
                hospitalClass = 'small-hospital';
                detailClass = 'small';
                break;
            case 'serious':
                hospitalText = 'City General Hospital (Big Hospital)';
                hospitalClass = 'big-hospital';
                detailClass = 'big';
                break;
            case 'critical':
                hospitalText = 'Trauma Center & Specialized Care';
                hospitalClass = 'trauma-center';
                detailClass = 'critical';
                break;
        }
        
        if (hospitalTypeEl) {
            hospitalTypeEl.textContent = hospitalText;
            hospitalTypeEl.className = `hospital-type-value ${hospitalClass}`;
        }
        
        if (hospitalTypeDetailEl) {
            hospitalTypeDetailEl.textContent = hospitalText.split('(')[0].trim();
            hospitalTypeDetailEl.className = detailClass;
        }
        
        // Show/hide hospital type box
        if (hospitalTypeBoxEl) {
            hospitalTypeBoxEl.style.display = 'block';
        }
    }
    
    function updateMonitoringPanel(data) {
        if (monitoringStatusEl) {
            monitoringStatusEl.textContent = data.severity.toUpperCase() + ' ACCIDENT DETECTED';
        }
        if (policeMonitorEl) policeMonitorEl.textContent = data.policeStatus;
        if (hospitalMonitorEl) hospitalMonitorEl.textContent = data.hospitalStatus;
        if (severityLevelEl) severityLevelEl.textContent = data.severity.toUpperCase();
        if (weatherConditionEl) weatherConditionEl.textContent = data.weather;
        if (locationLinkEl) locationLinkEl.href = data.mapUrl;
        
        // Update hospital type in monitoring panel
        updateHospitalTypeDisplay(data.severity, data.hospitalType);
        
        // Update monitoring status color
        if (monitoringStatusEl) {
            switch(data.severity) {
                case 'minor':
                    monitoringStatusEl.style.color = '#27ae60';
                    break;
                case 'serious':
                    monitoringStatusEl.style.color = '#e74c3c';
                    break;
                case 'critical':
                    monitoringStatusEl.style.color = '#c0392b';
                    break;
            }
        }
    }
    
    function triggerEmergencyAlerts(data) {
        // Simulate backend API call to PHP
        sendAlertToBackend(data);
        
        // Add to alert log
        const now = new Date();
        const timeStr = now.toTimeString().split(' ')[0];
        
        // Police alert log
        addLogEntry(timeStr, 'Police', `Alert sent for ${data.severity} accident`);
        
        // Hospital alert log with type
        setTimeout(() => {
            let hospitalMsg = '';
            switch(data.severity) {
                case 'minor':
                    hospitalMsg = `Local clinic alerted for minor accident at ${data.location}`;
                    break;
                case 'serious':
                    hospitalMsg = `City General Hospital emergency team dispatched to ${data.location}`;
                    break;
                case 'critical':
                    hospitalMsg = `Trauma center mobilized for critical accident at ${data.location}`;
                    break;
            }
            addLogEntry(timeStr, 'Hospital', hospitalMsg, data.hospitalType);
        }, 500);
        
        // Update alert count
        updateAlertCount();
    }
    
    function sendAlertToBackend(data) {
        // Create form data
        const formData = new FormData();
        formData.append('severity', data.severity);
        formData.append('time', data.time);
        formData.append('coordinates', data.coordinates);
        formData.append('location', data.location);
        formData.append('weather', data.weather);
        formData.append('hospital_type', data.hospitalType);
        
        // Send to PHP backend
        fetch('process.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            console.log('Backend response:', result);
            if (result.success) {
                addLogEntry(new Date().toTimeString().split(' ')[0], 'System', 'Alert logged to backend');
            }
        })
        .catch(error => {
            console.error('Error sending alert:', error);
            addLogEntry(new Date().toTimeString().split(' ')[0], 'System', 'Backend connection failed');
        });
    }
    
    function addLogEntry(time, service, message, hospitalType) {
        if (!alertLogContainer) return;
        
        const logEntry = document.createElement('div');
        logEntry.className = 'log-entry';
        
        // Add hospital type indicator if present
        let hospitalIndicator = '';
        if (hospitalType) {
            hospitalIndicator = `<span class="hospital-indicator ${hospitalType}">${hospitalType.toUpperCase()}</span>`;
        }
        
        logEntry.innerHTML = `
            <span class="log-time">${time}</span>
            <span class="log-service">${service} ${hospitalIndicator}</span>
            <span class="log-message">${message}</span>
        `;
        
        // Add to beginning of log container
        alertLogContainer.insertBefore(logEntry, alertLogContainer.firstChild);
        
        // Keep only last 10 logs visible
        if (alertLogContainer.children.length > 10) {
            alertLogContainer.removeChild(alertLogContainer.lastChild);
        }
        
        // Save to localStorage
        alertLogs.unshift({time, service, message, hospitalType});
        saveLogsToStorage();
    }
    
    function logAccident(data) {
        const log = {
            id: Date.now(),
            ...data,
            loggedAt: new Date().toISOString()
        };
        
        // Save to localStorage
        const accidents = JSON.parse(localStorage.getItem('accidentLogs') || '[]');
        accidents.unshift(log);
        localStorage.setItem('accidentLogs', JSON.stringify(accidents));
    }
    
    function loadLogsFromStorage() {
        // Load alert logs
        const savedLogs = JSON.parse(localStorage.getItem('alertLogs') || '[]');
        alertLogs = savedLogs.slice(0, 10);
        
        // Display loaded logs
        if (alertLogContainer) {
            alertLogContainer.innerHTML = '';
            alertLogs.forEach(log => {
                const logEntry = document.createElement('div');
                logEntry.className = 'log-entry';
                
                let hospitalIndicator = '';
                if (log.hospitalType) {
                    hospitalIndicator = `<span class="hospital-indicator ${log.hospitalType}">${log.hospitalType.toUpperCase()}</span>`;
                }
                
                logEntry.innerHTML = `
                    <span class="log-time">${log.time}</span>
                    <span class="log-service">${log.service} ${hospitalIndicator}</span>
                    <span class="log-message">${log.message}</span>
                `;
                alertLogContainer.appendChild(logEntry);
            });
        }
        
        // Load accident count
        accidentCount = JSON.parse(localStorage.getItem('accidentCount') || '0');
    }
    
    function saveLogsToStorage() {
        localStorage.setItem('alertLogs', JSON.stringify(alertLogs.slice(0, 50)));
        localStorage.setItem('accidentCount', accidentCount);
    }
    
    function updateAlertCount() {
        console.log(`Total accidents detected: ${accidentCount}`);
    }
    
    function triggerManualDetection() {
        if (!isSystemActive) {
            showNotification('System is inactive. Please reset system.', 'error');
            return;
        }
        
        showNotification('Manual detection triggered. Scanning for accidents...', 'info');
        
        // Simulate scanning delay
        setTimeout(() => {
            const severities = ['minor', 'serious', 'critical'];
            const randomSeverity = severities[Math.floor(Math.random() * severities.length)];
            simulateAccident(randomSeverity);
        }, 2000);
    }
    
    // RESET SYSTEM FUNCTION
    function resetSystem() {
        console.log('resetSystem() function called');
        
        if (confirm('Are you sure you want to reset the system? All current data will be cleared.')) {
            isSystemActive = false;
            
            // Reset all displays
            if (accidentStatusEl) {
                accidentStatusEl.textContent = 'No Accident Detected';
                accidentStatusEl.style.color = '#27ae60';
                accidentStatusEl.className = 'status-value';
                accidentStatusEl.style.animation = 'none';
            }
            
            if (policeStatusEl) policeStatusEl.textContent = 'Standby';
            if (hospitalStatusEl) hospitalStatusEl.textContent = 'Standby';
            
            // Reset hospital type display
            if (hospitalTypeEl) {
                hospitalTypeEl.textContent = 'No hospital assigned';
                hospitalTypeEl.className = 'hospital-type-value';
            }
            if (hospitalTypeDetailEl) {
                hospitalTypeDetailEl.textContent = 'None';
                hospitalTypeDetailEl.className = '';
            }
            if (hospitalTypeBoxEl) hospitalTypeBoxEl.style.display = 'none';
            
            if (monitoringStatusEl) {
                monitoringStatusEl.textContent = 'WAITING';
                monitoringStatusEl.style.color = '#0984e3';
            }
            if (policeMonitorEl) policeMonitorEl.textContent = 'Not Notified';
            if (hospitalMonitorEl) hospitalMonitorEl.textContent = 'Not Notified';
            if (severityLevelEl) severityLevelEl.textContent = 'None';
            if (weatherConditionEl) weatherConditionEl.textContent = 'Monitoring...';
            
            // Reset auto detection system
            isAutoDetectionActive = true;
            autoDetectionCount = 29;
            lastAutoDetectionTime = "10:13:54 PM";
            updateAutoDetectionDisplay();
            
            if (autoSystemStatusEl) {
                autoSystemStatusEl.textContent = 'ACTIVE';
                autoSystemStatusEl.classList.remove('inactive');
                autoSystemStatusEl.classList.add('active');
            }
            
            // Reset sensors
            if (accelValueEl) {
                accelValueEl.textContent = 'Detecting G-forces';
                accelValueEl.classList.remove('alert');
            }
            if (gpsValueEl) {
                gpsValueEl.textContent = 'Tracking location';
                gpsValueEl.classList.remove('alert');
            }
            if (soundValueEl) {
                soundValueEl.textContent = 'Monitoring impact sounds';
                soundValueEl.classList.remove('alert');
            }
            if (cameraValueEl) {
                cameraValueEl.textContent = 'Visual analysis';
                cameraValueEl.classList.remove('alert');
            }
            
            // Clear logs and add initial ones
            if (alertLogContainer) {
                alertLogContainer.innerHTML = '';
                const initialLogs = [
                    {time: '15:44:44', service: 'Police', message: 'Alert sent for serious accident', hospitalType: null},
                    {time: '15:44:45', service: 'Hospital', message: 'City General Hospital emergency team dispatched', hospitalType: 'big'}
                ];
                
                initialLogs.forEach(log => {
                    const logEntry = document.createElement('div');
                    logEntry.className = 'log-entry';
                    
                    let hospitalIndicator = '';
                    if (log.hospitalType) {
                        hospitalIndicator = `<span class="hospital-indicator ${log.hospitalType}">${log.hospitalType.toUpperCase()}</span>`;
                    }
                    
                    logEntry.innerHTML = `
                        <span class="log-time">${log.time}</span>
                        <span class="log-service">${log.service} ${hospitalIndicator}</span>
                        <span class="log-message">${log.message}</span>
                    `;
                    alertLogContainer.appendChild(logEntry);
                });
            }
            
            alertLogs = [];
            localStorage.removeItem('alertLogs');
            localStorage.removeItem('accidentLogs');
            
            // Restart auto detection
            if (isAutoDetectionActive) {
                startAutoDetectionSimulation();
            }
            
            // Add default log entry
            addLogEntry(new Date().toTimeString().split(' ')[0], 'System', 'System reset completed');
            
            showNotification('✅ System has been reset to initial state.', 'success');
            
            // Reactivate system
            setTimeout(() => {
                isSystemActive = true;
                showNotification('System reactivated. Ready for detection.', 'success');
            }, 3000);
        }
    }
    
    // REFRESH DATA FUNCTION
    function refreshData() {
        console.log('refreshData() function called');
        
        // Simulate data refresh
        showNotification('🔄 Refreshing sensor data and system status...', 'info');
        
        // Update weather
        const weatherConditions = ['Clear', 'Cloudy', 'Rainy', 'Foggy', 'Windy'];
        const randomWeather = weatherConditions[Math.floor(Math.random() * weatherConditions.length)];
        const temp = Math.floor(Math.random() * 15) + 20;
        if (weatherConditionEl) weatherConditionEl.textContent = `${randomWeather}, ${temp}°C`;
        
        // Update time
        updateCurrentTime();
        
        // Update sensor data
        if (isAutoDetectionActive) {
            if (accelValueEl) {
                const gForces = ['1.2g', '1.5g', '0.8g', '1.1g'];
                accelValueEl.textContent = gForces[Math.floor(Math.random() * gForces.length)];
            }
            if (gpsValueEl) {
                const gpsStatus = ['Tracking location', 'GPS active', 'Location locked'];
                gpsValueEl.textContent = gpsStatus[Math.floor(Math.random() * gpsStatus.length)];
            }
            if (soundValueEl) {
                const soundLevels = ['35dB', '42dB', '28dB', '31dB'];
                soundValueEl.textContent = 'Sound level: ' + soundLevels[Math.floor(Math.random() * soundLevels.length)];
            }
            if (cameraValueEl) {
                const cameraStatus = ['Visual analysis', 'AI processing', 'Camera ready'];
                cameraValueEl.textContent = cameraStatus[Math.floor(Math.random() * cameraStatus.length)];
            }
        }
        
        // Add log entry
        addLogEntry(new Date().toTimeString().split(' ')[0], 'System', 'Data refresh completed');
        
        setTimeout(() => {
            showNotification('✅ System data refreshed successfully.', 'success');
        }, 1000);
    }
    
    // VIEW ALL LOGS FUNCTION
    function viewAllLogs() {
        console.log('viewAllLogs() function called');
        
        showNotification('📋 Opening complete log history...', 'info');
        
        // Get all logs
        const allAccidents = JSON.parse(localStorage.getItem('accidentLogs') || '[]');
        const allAlerts = JSON.parse(localStorage.getItem('alertLogs') || '[]');
        
        console.log('All Accident Logs:', allAccidents);
        console.log('All Alert Logs:', allAlerts);
        
        // Create simple alert with log info
        const totalAccidents = allAccidents.length;
        const totalAlerts = allAlerts.length;
        
        const logMessage = `📊 LOGS SUMMARY:\n\n` +
                          `Total Accidents: ${totalAccidents}\n` +
                          `Total Alerts: ${totalAlerts}\n\n` +
                          `Check browser console for detailed logs.\n` +
                          `(Press F12 → Console tab)`;
        
        alert(logMessage);
        
        // Show notification
        setTimeout(() => {
            showNotification(`✅ Loaded ${totalAccidents} accidents and ${totalAlerts} alerts`, 'success');
        }, 500);
    }
    
    function showNotification(message, type) {
        if (!toast || !toastMessage) return;
        
        toastMessage.textContent = message;
        
        // Set color based on type
        switch(type) {
            case 'success':
                toast.style.background = 'linear-gradient(135deg, #27ae60, #219653)';
                break;
            case 'warning':
                toast.style.background = 'linear-gradient(135deg, #e74c3c, #c0392b)';
                break;
            case 'error':
                toast.style.background = 'linear-gradient(135deg, #e74c3c, #c0392b)';
                break;
            case 'info':
                toast.style.background = 'linear-gradient(135deg, #3498db, #2980b9)';
                break;
        }
        
        // Show toast
        toast.style.display = 'block';
        
        // Auto hide after 5 seconds
        setTimeout(() => {
            if (toast) {
                toast.style.display = 'none';
            }
        }, 5000);
    }
    
    function getCurrentTimeFormatted() {
        const now = new Date();
        return now.toLocaleTimeString('en-US', { 
            hour12: true, 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit' 
        });
    }
    
    // Debug function
    function debugButtons() {
        console.log('=== BUTTON STATUS ===');
        console.log('Reset button:', document.getElementById('reset-system') ? 'FOUND ✓' : 'MISSING ✗');
        console.log('Refresh button:', document.getElementById('refresh-data') ? 'FOUND ✓' : 'MISSING ✗');
        console.log('View Logs button:', document.getElementById('view-logs') ? 'FOUND ✓' : 'MISSING ✗');
        console.log('Auto Start button:', document.getElementById('start-auto-detection') ? 'FOUND ✓' : 'MISSING ✗');
        console.log('Auto Stop button:', document.getElementById('stop-auto-detection') ? 'FOUND ✓' : 'MISSING ✗');
        console.log('Auto Test button:', document.getElementById('test-auto-system') ? 'FOUND ✓' : 'MISSING ✗');
        console.log('Refresh Auto button:', document.getElementById('refresh-auto-data') ? 'FOUND ✓' : 'MISSING ✗');
        console.log('Reset Auto button:', document.getElementById('reset-auto-system') ? 'FOUND ✓' : 'MISSING ✗');
        console.log('View Auto Logs button:', document.getElementById('view-auto-logs') ? 'FOUND ✓' : 'MISSING ✗');
        console.log('=== END DEBUG ===');
    }
    
    // Run debug
    setTimeout(debugButtons, 2000);
});