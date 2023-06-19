const login = document.querySelector('#login-trigger');
const login_form = document.querySelector('#login-form');

const register = document.querySelector('#register-trigger');
const register_form = document.querySelector('#register-form');


const session = document.querySelector('#sesh-form');


const target = "/login";

login.addEventListener('click', async (e) => {
    e.preventDefault();

    let data = {
        login: login_form.login.value,
        password: login_form.password.value
    }
    sendRequest(data, 'POST', target).then((response) => {
        switch (response.status) {
            case 200:
                session.token.value = response.data.token;
                session.submit();
                break;
            case 403:
                window.alert("Invalid credentials!");
                break;
            default:
                window.alert("Something went wrong!");
                break;
        }
    });
});

register.addEventListener('click', async (e) => {
    e.preventDefault();

    let password1 = register_form.password.value;
    let password2 = register_form.password2.value;

    if (password1 !== password2) {
        window.alert("Passwords do not match!");
        return;
    }

    let data = {
        password: register_form.password.value,
        login: register_form.login.value
    }
    sendRequest(data, 'PUT', target).then((response) => {
        switch (response.status) {
            case 200:
                session.token.value = response.data.token;
                session.submit();
                break;
            case 403:
                window.alert("Invalid credentials!");
                break;
            default:
                window.alert("Something went wrong!");
                break;
        }
    });
});