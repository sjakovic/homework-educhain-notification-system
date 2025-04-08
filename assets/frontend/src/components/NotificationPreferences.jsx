import React, { useEffect, useState } from 'react';

const FREQUENCIES = ['immediate', 'daily', 'weekly'];

export default function NotificationPreferences() {
    const [preferences, setPreferences] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadPreferences();
    }, []);

    async function loadPreferences() {
        const res = await fetch('/api/preferences');
        const data = await res.json();
        setPreferences(data);
        setLoading(false);
    }

    function handleChange(id, field, value) {
        setPreferences((prev) =>
            prev.map((p) =>
                p.id === id ? { ...p, [field]: value } : p
            )
        );
    }

    async function save() {
        await fetch('/api/preferences', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(preferences),
        });
        alert('Preferences saved!');
    }

    if (loading) return <p>Loading...</p>;

    return (
        <div style={{ padding: '1rem' }}>
            <h2>Notification Preferences</h2>
            <table>
                <thead>
                <tr>
                    <th>Type</th>
                    <th>Channel</th>
                    <th>Enabled</th>
                    <th>Frequency</th>
                </tr>
                </thead>
                <tbody>
                {preferences.map((p) => (
                    <tr key={`${p.type}-${p.channel}`}>
                        <td>{p.type}</td>
                        <td>{p.channel}</td>
                        <td>
                            <input
                                type="checkbox"
                                checked={p.enabled}
                                onChange={(e) => handleChange(p.id, 'enabled', e.target.checked)}
                            />
                        </td>
                        <td>
                            <select
                                value={p.frequency}
                                onChange={(e) => handleChange(p.id, 'frequency', e.target.value)}
                            >
                                {FREQUENCIES.map((f) => (
                                    <option key={f} value={f}>{f}</option>
                                ))}
                            </select>
                        </td>
                    </tr>
                ))}
                </tbody>
            </table>
            <button onClick={save} style={{ marginTop: '1rem' }}>Save Preferences</button>
        </div>
    );
}
