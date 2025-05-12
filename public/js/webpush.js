// Register the service worker for WebPush
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js')
        .then((registration) => {
            console.log('Service Worker registered with scope:', registration.scope);
        }).catch((error) => {
            console.error('Service Worker registration failed:', error);
        });
}

// Function to request notification permission
function requestNotificationPermission() {
    return new Promise((resolve, reject) => {
        if (!('Notification' in window)) {
            reject("This browser does not support desktop notification");
        }
        
        if (Notification.permission === 'granted') {
            resolve(true);
        } else if (Notification.permission !== 'denied') {
            Notification.requestPermission().then(permission => {
                resolve(permission === 'granted');
            });
        } else {
            reject("Notifications are denied");
        }
    });
}

// Ask for permission when page loads
document.addEventListener('DOMContentLoaded', function() {
    requestNotificationPermission()
        .then(granted => {
            if (granted) {
                console.log('Notification permission granted');
                // Here you would subscribe the user to push notifications
            } else {
                console.log('Notification permission not granted');
            }
        })
        .catch(error => {
            console.error('Error requesting notification permission:', error);
        });
}); 