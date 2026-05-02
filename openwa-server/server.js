const wa = require('@open-wa/wa-automate');
const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
require('dotenv').config();

const app = express();
const PORT = process.env.PORT || 3000;
const API_KEY = process.env.API_KEY || 'change-this-secret-key';

// Middleware
app.use(cors({
    origin: process.env.ALLOWED_ORIGINS?.split(',') || '*'
}));
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Global client variable
let client = null;
let isReady = false;

// API Key validation middleware
const validateApiKey = (req, res, next) => {
    const apiKey = req.headers['x-api-key'] || req.query.api_key;
    
    if (!apiKey || apiKey !== API_KEY) {
        return res.status(401).json({
            success: false,
            error: 'Unauthorized: Invalid API Key'
        });
    }
    
    next();
};

// Check if client is ready
const checkClientReady = (req, res, next) => {
    if (!isReady || !client) {
        return res.status(503).json({
            success: false,
            error: 'WhatsApp client not ready. Please scan QR code first.'
        });
    }
    next();
};

// Start WhatsApp client
wa.create({
    sessionId: process.env.WA_SESSION_NAME || 'hm-tour-session',
    headless: process.env.WA_HEADLESS === 'true',
    qrTimeout: 0,
    authTimeout: 0,
    autoRefresh: process.env.WA_AUTO_REFRESH_QR === 'true',
    cacheEnabled: true,
    useChrome: true,
    killProcessOnBrowserClose: true,
    throwErrorOnTosBlock: false,
    chromiumArgs: [
        '--no-sandbox',
        '--disable-setuid-sandbox',
        '--disable-dev-shm-usage',
        '--disable-accelerated-2d-canvas',
        '--no-first-run',
        '--no-zygote',
        '--disable-gpu'
    ]
}).then(waClient => {
    client = waClient;
    isReady = true;
    
    console.log('✅ WhatsApp client is ready!');
    console.log('📱 Session:', process.env.WA_SESSION_NAME || 'hm-tour-session');
    
    // Log when connection state changes
    client.onStateChanged(state => {
        console.log('🔄 State changed:', state);
        if (state === 'CONFLICT' || state === 'UNLAUNCHED') {
            isReady = false;
        }
    });
    
    // Log incoming messages (optional)
    client.onMessage(message => {
        console.log('📨 Received message from:', message.from);
    });
    
}).catch(err => {
    console.error('❌ Error starting WhatsApp client:', err);
    isReady = false;
});

// ===== API ROUTES =====

// Health check
app.get('/health', (req, res) => {
    res.json({
        success: true,
        status: isReady ? 'ready' : 'not_ready',
        message: isReady ? 'WhatsApp client is ready' : 'WhatsApp client is not ready',
        timestamp: new Date().toISOString()
    });
});

// Get QR Code (for initial setup)
app.get('/qr', validateApiKey, async (req, res) => {
    try {
        if (isReady) {
            return res.json({
                success: true,
                message: 'Already connected. No QR code needed.'
            });
        }
        
        res.json({
            success: false,
            message: 'Please check server console for QR code or wait for connection.'
        });
    } catch (error) {
        res.status(500).json({
            success: false,
            error: error.message
        });
    }
});

// Send text message
app.post('/send-message', validateApiKey, checkClientReady, async (req, res) => {
    try {
        const { phone, message } = req.body;
        
        if (!phone || !message) {
            return res.status(400).json({
                success: false,
                error: 'Phone number and message are required'
            });
        }
        
        // Format phone number (remove spaces, dashes, etc)
        let formattedPhone = phone.replace(/[^0-9]/g, '');
        
        // Add country code if not present
        if (!formattedPhone.startsWith('62')) {
            if (formattedPhone.startsWith('0')) {
                formattedPhone = '62' + formattedPhone.substring(1);
            } else {
                formattedPhone = '62' + formattedPhone;
            }
        }
        
        // Add @c.us suffix for WhatsApp
        const chatId = formattedPhone + '@c.us';
        
        console.log('📤 Sending message to:', chatId);
        
        // Try to send message directly (OpenWA free version limitation workaround)
        // If contact is not saved, this will fail with "Not a contact" error
        // The Laravel service will handle fallback to wa.me link
        const result = await client.sendText(chatId, message);
        
        console.log('✅ Message sent successfully to:', formattedPhone);
        
        res.json({
            success: true,
            message: 'Message sent successfully',
            phone: formattedPhone,
            messageId: result.id
        });
        
    } catch (error) {
        console.error('❌ Error sending message:', error);
        res.status(500).json({
            success: false,
            error: error.message
        });
    }
});

// Send message with media (image, PDF, etc)
app.post('/send-media', validateApiKey, checkClientReady, async (req, res) => {
    try {
        const { phone, mediaUrl, caption, filename } = req.body;
        
        if (!phone || !mediaUrl) {
            return res.status(400).json({
                success: false,
                error: 'Phone number and media URL are required'
            });
        }
        
        // Format phone number
        let formattedPhone = phone.replace(/[^0-9]/g, '');
        if (!formattedPhone.startsWith('62')) {
            if (formattedPhone.startsWith('0')) {
                formattedPhone = '62' + formattedPhone.substring(1);
            } else {
                formattedPhone = '62' + formattedPhone;
            }
        }
        
        const chatId = formattedPhone + '@c.us';
        
        console.log('📤 Sending media to:', chatId);
        
        // Send media
        const result = await client.sendFile(chatId, mediaUrl, filename || 'file', caption || '');
        
        console.log('✅ Media sent successfully to:', formattedPhone);
        
        res.json({
            success: true,
            message: 'Media sent successfully',
            phone: formattedPhone,
            messageId: result.id
        });
        
    } catch (error) {
        console.error('❌ Error sending media:', error);
        res.status(500).json({
            success: false,
            error: error.message
        });
    }
});

// Get connection status
app.get('/status', validateApiKey, async (req, res) => {
    try {
        if (!isReady || !client) {
            return res.json({
                success: true,
                connected: false,
                message: 'Client not ready'
            });
        }
        
        // Simple status check - just return if client is ready
        res.json({
            success: true,
            connected: true,
            state: 'CONNECTED',
            timestamp: new Date().toISOString()
        });
        
    } catch (error) {
        console.error('❌ Error getting status:', error);
        res.status(500).json({
            success: false,
            error: error.message
        });
    }
});

// Logout and kill session
app.post('/logout', validateApiKey, async (req, res) => {
    try {
        if (client) {
            await client.kill();
            isReady = false;
            client = null;
        }
        
        res.json({
            success: true,
            message: 'Logged out successfully'
        });
        
    } catch (error) {
        res.status(500).json({
            success: false,
            error: error.message
        });
    }
});

// Start Express server
app.listen(PORT, () => {
    console.log('🚀 OpenWA Server started');
    console.log('📡 Server running on port:', PORT);
    console.log('🔑 API Key:', API_KEY);
    console.log('');
    console.log('⏳ Waiting for WhatsApp connection...');
    console.log('📱 Please scan QR code in the browser window or check console');
    console.log('');
    console.log('API Endpoints:');
    console.log('  GET  /health          - Health check');
    console.log('  GET  /status          - Connection status');
    console.log('  POST /send-message    - Send text message');
    console.log('  POST /send-media      - Send media (image/PDF)');
    console.log('  POST /logout          - Logout and kill session');
    console.log('');
});

// Graceful shutdown
process.on('SIGINT', async () => {
    console.log('\n🛑 Shutting down gracefully...');
    if (client) {
        await client.kill();
    }
    process.exit(0);
});
