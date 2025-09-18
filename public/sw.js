// Pusher Beams Service Worker
console.log('Service Worker loading...');

self.addEventListener('push', function(event) {
    console.log('Push event received:', event);

    if (event.data) {
        try {
            const data = event.data.json();
            console.log('Push data:', data);

            // Handle Pusher Beams notification format
            let title = 'Notification';
            let options = {
                body: 'New notification',
                icon: '/favicon.ico',
                badge: '/favicon.ico',
                vibrate: [100, 50, 100],
                data: {
                    dateOfArrival: Date.now(),
                    url: '/'
                }
            };

            // Check if it's a Pusher Beams format
            if (data.notification) {
                title = data.notification.title || title;
                options.body = data.notification.body || options.body;
                options.icon = data.notification.icon || options.icon;
                if (data.data) {
                    options.data = { ...options.data, ...data.data };
                }
            } else {
                // Direct format
                title = data.title || title;
                options.body = data.body || options.body;
                options.icon = data.icon || options.icon;
            }

            event.waitUntil(
                self.registration.showNotification(title, options)
            );
        } catch (error) {
            console.error('Error processing push event:', error);
            // Fallback notification
            event.waitUntil(
                self.registration.showNotification('Notification', {
                    body: 'You have a new notification',
                    icon: '/favicon.ico'
                })
            );
        }
    }
});

self.addEventListener('notificationclick', function(event) {
    console.log('Notification clicked:', event);
    event.notification.close();

    const url = event.notification.data?.url || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window' }).then(function(clientList) {
            // Check if there's already a window/tab open
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url === url && 'focus' in client) {
                    return client.focus();
                }
            }
            // If no window/tab is open, open a new one
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});

self.addEventListener('notificationclose', function(event) {
    console.log('Notification closed:', event);
});

// Install event
self.addEventListener('install', function(event) {
    console.log('Service Worker installing...');
    self.skipWaiting();
});

// Activate event
self.addEventListener('activate', function(event) {
    console.log('Service Worker activating...');
    event.waitUntil(self.clients.claim());
});