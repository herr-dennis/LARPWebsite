/**
 * Fügt eine Story per API ein.
 *
 * @param {Object} data - JSON-Daten mit z. B. kategorie, text, title
 * @param {string} apiUrl - API-Endpunkt-Erweiterung
 * @returns {Promise<Object|null>}
 */
async function insertStory(data, apiUrl) {
    if (!data) {
        return null;
    }

    try {
        const response = await fetch(`/api/story/${apiUrl}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (!response.ok) {
            return result;
        }

        return result;
    } catch (error) {
        console.error('Fehler beim Einfügen der Story:', error);
        return {
            error: true,
            message: 'Netzwerkfehler oder Server nicht erreichbar'
        };
    }
}

export { insertStory };
