import React from 'react';
import NotificationBell from './components/NotificationBell';
import NotificationPreferences from './components/NotificationPreferences';

function App() {
    return (
        <div style={{ padding: '2rem' }}>
            <h1>Educhain Portal</h1>

            <div style={{ marginBottom: '2rem' }}>
                <NotificationBell />
            </div>

            <NotificationPreferences />
        </div>
    );
}

export default App;
