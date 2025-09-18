// Pusher Beams Service Worker
self.addEventListener('push', function(event) {

    if (event.data) {
        try {
            const data = event.data.json();

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

    event.notification.close();

    // Handle different actions
    if (event.action === 'dismiss') {
        // Just close the notification, do nothing else
        return;
    }

    // For 'view-logs' action or clicking the notification body
    const url = event.notification.data?.url || '/';
    const logId = event.notification.data?.log_id;

    // If we have a specific log ID, we could navigate to a filtered view
    const targetUrl = logId ? `${url}?log_id=${logId}` : url;

    event.waitUntil(
        clients.matchAll({ type: 'window' }).then(function(clientList) {
            // Check if there's already a window/tab open with our app
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url.includes(new URL(url).hostname) && 'focus' in client) {
                    // Focus existing window and navigate to the target URL
                    client.focus();
                    if ('navigate' in client) {
                        return client.navigate(targetUrl);
                    } else {
                        // Fallback: post message to the client to navigate
                        client.postMessage({
                            type: 'NAVIGATE',
                            url: targetUrl,
                            logData: event.notification.data
                        });
                        return;
                    }
                }
            }
            // If no window/tab is open, open a new one
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});

self.addEventListener('notificationclose', function(event) {
    // Notification closed
});

// Install event
self.addEventListener('install', function(event) {
    self.skipWaiting();
});

// Activate event
self.addEventListener('activate', function(event) {
    event.waitUntil(self.clients.claim());
});