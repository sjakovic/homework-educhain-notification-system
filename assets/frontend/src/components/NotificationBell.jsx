import React, { useEffect, useState } from 'react';

export default function NotificationBell() {
    const [notifications, setNotifications] = useState([]);
    const [dropdownOpen, setDropdownOpen] = useState(false);

    useEffect(() => {
        loadNotifications();
    }, []);

    async function loadNotifications() {
        const res = await fetch('/api/notifications');
        const data = await res.json();
        setNotifications(data);
    }

    async function markAsRead(id) {
        await fetch(`/api/notifications/${id}/read`, { method: 'POST' });
        await loadNotifications();
    }

    const unreadCount = notifications.filter(n => !n.readAt).length;

    return (
        <div style={{ position: 'relative' }}>
            <button onClick={() => setDropdownOpen(!dropdownOpen)}>
                🔔 {unreadCount > 0 && <span>({unreadCount})</span>}
            </button>

            {dropdownOpen && (
                <div style={{
                    position: 'absolute',
                    top: '100%',
                    right: 0,
                    backgroundColor: 'white',
                    border: '1px solid #ccc',
                    padding: '10px',
                    width: '300px',
                    zIndex: 10
                }}>
                    <strong>Notifications</strong>
                    <ul style={{ listStyle: 'none', padding: 0 }}>
                        {notifications.map(n => (
                            <li
                                key={n.id}
                                onClick={() => markAsRead(n.id)}
                                style={{
                                    backgroundColor: n.readAt ? '#f4f4f4' : '#e0f7fa',
                                    padding: '6px 8px',
                                    marginBottom: '4px',
                                    cursor: 'pointer'
                                }}
                            >
                                {n.message}
                                <br />
                                <small>{new Date(n.createdAt).toLocaleString()}</small>
                            </li>
                        ))}
                    </ul>
                </div>
            )}
        </div>
    );
}
