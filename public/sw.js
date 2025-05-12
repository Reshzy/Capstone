// Service Worker for WebPush notifications

// Install event
self.addEventListener('install', (event) => {
    console.log('Service Worker installing');
    self.skipWaiting();
});

// Activate event
self.addEventListener('activate', (event) => {
    console.log('Service Worker activating');
    return self.clients.claim();
});

// Push event handler
self.addEventListener('push', (event) => {
    if (!event.data) return;
    
    try {
        const notification = event.data.json();
        
        const options = {
            body: notification.body || 'New notification',
            icon: notification.icon || '/images/notification-icon.png',
            data: notification.data || {},
            actions: notification.actions || [],
        };
        
        event.waitUntil(
            self.registration.showNotification(notification.title || 'SVP System', options)
        );
    } catch (error) {
        console.error('Error showing notification:', error);
    }
});

// Notification click event
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    
    // This looks to see if the current is already open and
    // focuses if it is
    event.waitUntil(
        self.clients.matchAll({
            type: 'window'
        }).then((clientList) => {
            const url = event.notification.data.url || '/';
            
            for (const client of clientList) {
                if (client.url === url && 'focus' in client) {
                    return client.focus();
                }
            }
            
            return self.clients.openWindow(url);
        })
    );
}); 