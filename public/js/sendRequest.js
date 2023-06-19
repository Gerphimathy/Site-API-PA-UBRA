async function sendRequest(data, method, target) {
    return fetch(target, {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    }).then(async (response) => {
        let status = 0;
        status = response.status;

        let in_data = await response.json();
        return {
            status: status,
            data: in_data
        }
    });
}