const id_refresh = document.querySelector('#id_code_refresh');
const id_code = document.querySelector('#id_code');
const id_reveal = document.querySelector('#id_code_reveal');
const id_copy = document.querySelector('#id_code_copy');

const username = document.querySelector('#username');
const username_refresh = document.querySelector('#username_refresh');

username_refresh.addEventListener('click', async (e) => {
    e.preventDefault();
    let data = {
        token: token,
        key: "username",
        value: username.value
    }
    sendRequest(data, 'PATCH', '/login').then((response) => {
        switch (response.status) {
            case 200:
                username.value = response.data.value;
                break;
            case 403:
                window.alert("Invalid token!");
                break;
            default:
                window.alert("Something went wrong!");
                break;
        }
    });
});

id_copy.addEventListener('click', async (e) => {
    e.preventDefault();
    id_code.select();
    id_code.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(id_code.value).then(() => {
        window.alert("Copied to clipboard!");
    });
});

id_reveal.addEventListener('mousedown', async (e) => {
    e.preventDefault();
    id_code.type = 'text';
});

id_reveal.addEventListener('mouseup', async (e) => {
    e.preventDefault();
    id_code.type = 'password';
});

id_refresh.addEventListener('click', async (e) => {
    e.preventDefault();
    let data = {
        token: token,
        key: "id_code"
    }
    sendRequest(data, 'PATCH', '/login').then((response) => {
        switch (response.status) {
            case 200:
                id_code.value = response.data.value;
                break;
            case 403:
                window.alert("Invalid token!");
                break;
            default:
                window.alert("Something went wrong!");
                break;
        }
    });
});