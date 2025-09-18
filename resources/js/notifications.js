import * as PusherBeams from '@pusher/push-notifications-web';

class NotificationManager {
    constructor() {
        this.beamsClient = null;
        this.isInitialized = false;
        this.initPromise = null;
        this.init();
    }

    async init() {
        if (this.initPromise) {
            return this.initPromise;
        }

        this.initPromise = this._doInit();
        return this.initPromise;
    }

    async _doInit() {
        if ('serviceWorker' in navigator && 'PushManager' in window) {
            try {
                const registration = await this.registerServiceWorker();
                await this.initializePusherBeams(registration);
                this.isInitialized = true;
                console.log('Notification manager initialized successfully');
            } catch (error) {
                console.error('Failed to initialize notifications:', error);
                throw error;
            }
        } else {
            throw new Error('Push notifications are not supported in this browser');
        }
    }

    async registerServiceWorker() {
        // Unregister any existing service workers first
        const registrations = await navigator.serviceWorker.getRegistrations();
        for (let registration of registrations) {
            await registration.unregister();
        }

        // Register the new service worker with cache busting
        const registration = await navigator.serviceWorker.register('/service-worker.js?v=' + Date.now());
        console.log('Service Worker registered:', registration);

        // Wait for the service worker to be ready
        await navigator.serviceWorker.ready;
        console.log('Service Worker is ready');

        return registration;
    }

    async initializePusherBeams(registration) {
        if (!window.pusherBeamsInstanceId) {
            throw new Error('Pusher Beams instance ID not found. Please check your configuration.');
        }

        console.log('Initializing Pusher Beams with instance ID:', window.pusherBeamsInstanceId);

        this.beamsClient = new PusherBeams.Client({
            instanceId: window.pusherBeamsInstanceId,
            serviceWorkerRegistration: registration
        });

        console.log('Pusher Beams initialized successfully');
    }

    async requestPermission() {
        if (!('Notification' in window)) {
            throw new Error('This browser does not support notifications');
        }

        if (Notification.permission === 'granted') {
            return true;
        }

        if (Notification.permission === 'denied') {
            throw new Error('Notifications are blocked');
        }

        const permission = await Notification.requestPermission();
        return permission === 'granted';
    }

    async subscribe(userId) {
        try {
            // Ensure initialization is complete
            await this.init();

            if (!this.isInitialized || !this.beamsClient) {
                throw new Error('Notification manager failed to initialize');
            }

            const hasPermission = await this.requestPermission();
            if (!hasPermission) {
                throw new Error('Notification permission denied');
            }

            console.log('Starting Pusher Beams client...');
            await this.beamsClient.start();
            console.log('Pusher Beams client started successfully');

            console.log('Setting user ID:', userId);

            // Create a TokenProvider for authentication
            const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfTokenElement ? csrfTokenElement.getAttribute('content') : null;
            console.log('CSRF Token Element:', csrfTokenElement);
            console.log('CSRF Token Value:', csrfToken);

            const tokenProvider = new PusherBeams.TokenProvider({
                url: '/api/pusher/beams-auth',
                queryParams: {
                    user_id: userId
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin'
            });

            console.log('Created TokenProvider');

            try {
                await this.beamsClient.setUserId(userId.toString(), tokenProvider);
                console.log('User ID set successfully with TokenProvider');

                // Store subscription in local database
                await this.storeSubscriptionLocally(userId);
            } catch (authError) {
                console.error('SetUserId failed:', authError);
                throw authError;
            }

            console.log('Successfully subscribed to push notifications');
            return true;
        } catch (error) {
            console.error('Failed to subscribe to notifications:', error);
            throw error;
        }
    }

    async unsubscribe() {
        if (!this.beamsClient) {
            return;
        }

        try {
            await this.beamsClient.stop();
            console.log('Successfully unsubscribed from push notifications');
        } catch (error) {
            console.error('Failed to unsubscribe:', error);
            throw error;
        }
    }

    getPermissionStatus() {
        if (!('Notification' in window)) {
            return 'unsupported';
        }
        return Notification.permission;
    }

    async storeSubscriptionLocally(userId) {
        try {
            console.log('Storing subscription locally for user:', userId);

            // Get the device ID from Pusher Beams
            const deviceId = await this.beamsClient.getDeviceId();
            console.log('Device ID:', deviceId);

            // Store in local database
            const response = await fetch('/api/pusher/store-subscription', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    user_id: userId,
                    device_id: deviceId,
                    endpoint: 'pusher-beams', // Placeholder for Pusher Beams
                    public_key: 'pusher-beams',
                    auth_token: 'pusher-beams',
                    content_encoding: 'aesgcm'
                })
            });

            if (response.ok) {
                console.log('Subscription stored locally');
            } else {
                console.error('Failed to store subscription locally:', await response.text());
            }
        } catch (error) {
            console.error('Error storing subscription locally:', error);
        }
    }
}

// Global notification manager instance
window.notificationManager = new NotificationManager();

// Auto-subscribe for authenticated users
document.addEventListener('DOMContentLoaded', async () => {
    const userIdElement = document.querySelector('meta[name="user-id"]');
    if (userIdElement && userIdElement.getAttribute('content')) {
        const userId = parseInt(userIdElement.getAttribute('content'));
        const notificationBtn = document.getElementById('enable-notifications');

        if (notificationBtn) {
            // Check if user is already subscribed
            await checkExistingSubscription(userId, notificationBtn);

            notificationBtn.addEventListener('click', async (e) => {
                e.preventDefault();

                // Show loading state
                const originalText = notificationBtn.textContent;
                notificationBtn.textContent = 'Enabling...';
                notificationBtn.disabled = true;

                try {
                    await window.notificationManager.subscribe(userId);
                    notificationBtn.textContent = 'Notifications Enabled';
                    notificationBtn.classList.add('opacity-50');
                    localStorage.setItem('notifications-enabled', 'true');
                    alert('Push notifications enabled successfully!');
                } catch (error) {
                    console.error('Subscription error:', error);
                    notificationBtn.textContent = originalText;
                    notificationBtn.disabled = false;
                    alert('Failed to enable notifications: ' + error.message);
                }
            });
        }
    }
});

async function checkExistingSubscription(userId, button) {
    try {
        // Check localStorage first
        const isEnabled = localStorage.getItem('notifications-enabled') === 'true';

        // Check if browser permission is granted
        const hasPermission = Notification.permission === 'granted';

        // Check if user has subscription in database
        const response = await fetch('/api/pusher/check-subscription', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (response.ok) {
            const data = await response.json();
            const hasDbSubscription = data.has_subscription;

            // If all conditions are met, auto-enable notifications
            if (isEnabled && hasPermission && hasDbSubscription) {
                console.log('Auto-subscribing user to notifications...');
                try {
                    await window.notificationManager.subscribe(userId);
                    button.textContent = 'Notifications Enabled';
                    button.disabled = true;
                    button.classList.add('opacity-50');
                    console.log('Auto-subscription successful');
                } catch (error) {
                    console.log('Auto-subscription failed, user needs to re-enable:', error);
                    localStorage.removeItem('notifications-enabled');
                }
            }
        }
    } catch (error) {
        console.log('Could not check existing subscription:', error);
    }
}