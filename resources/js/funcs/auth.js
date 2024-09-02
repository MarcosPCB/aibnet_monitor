require('../bootstrap');
const globals = require('../globals');
const { checkAuth, cleanDOM, cleanMsgBody, readCookie, changeToLoad, returnToNormal } = require('../utils');
const { listAccounts } = require('./account');
const { mainBrandSelect } = require('./mainBrand');

function changePassword() {
    const token = readCookie('token');
    const new_password = $('#new_password_id')[0].value;
    const confirm_new_password = $('#new_confirm_password_id')[0].value;

    if(new_password != confirm_new_password) {
        alert('Senhas diferentes');
        console.error('Senhas diferentes');
        return;
    }

    if(globals.is_operator) {
        window.axios.patch(globals.api_url + `operator/update/${globals.user_id}`, {
            password: new_password
        }, {
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        }).then(response => {
            if (response.status != 200) {
                throw new Error(response.body + ` code: ${response.status}`);
            }
            
            alert('Senha alterada!');
            
        }).catch(e => {
            alert('Erro ao mudar a senha:', e);
            checkAuth(e.response);
        });

        return;
    }
    
    window.axios.patch(globals.api_url + `user/update/${globals.user_id}/${globals.account_id}`, {
        password: new_password
    }, {
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    }).then(response => {
        if (response.status != 200) {
            throw new Error(response.body + ` code: ${response.status}`);
        }

       alert('Senha alterada!');
            
    }).catch(e => {
        alert('Erro ao mudar a senha:', e);
        checkAuth(e.response);
    });
}

function logout() {
    const token = readCookie('token');
    
    window.axios.post(globals.api_url + `logout`, {}, {
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    }).then(response => {
        if (response.status != 200) {
            throw new Error(response.body + ` code: ${response.status}`);
        }

        cleanDOM(globals.chat_cards);
        globals.chat_cards.innerHTML = globals.add_chat;
        cleanMsgBody();

        globals.loaded = 0;

        $('#login_modal_id').modal('show');
            
    }).catch(e => {
        alert('Erro ao deslogar:', e);
        checkAuth(e.response);
    });
}

function login(event) {    
    const email = $('#email_id')[0].value;
    const password = $('#password_id')[0].value;
    changeToLoad(event.currentTarget);

    window.axios.post(globals.api_url + `login`, {
        email,
        password
    }, {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    }).then(response => {
        if (response.status != 200) {
            throw new Error(response.body + ` code: ${response.status}`);
        }

        $('#email_id')[0].value = $('#password_id')[0].value = '';

        returnToNormal(event.currentTarget);

        cleanDOM(globals.chat_cards);
        globals.chat_cards.innerHTML = globals.add_chat;
        cleanMsgBody();

        const token = response.data.token;

        globals.is_operator = response.data.isOperator;
        globals.user_id = response.data.user.id;

        if(!globals.is_operator) {
            globals.account_id = response.data.account.id;
            globals.main_brand_id = response.data.mainBrands[0].id;
            $('#account-modal-tab')[0].classList.add('disabled_btn');
            const el = $('#user-modal-tab')[0];
            const tab = new bootstrap.Tab(el);
            tab.show();
        } else {
            globals.account_id =  1;
            globals.main_brand_id = 1;
            $('#account-modal-tab')[0].classList.remove('disabled_btn');
        }

        const expires = new Date();
        expires.setTime(expires.getTime() + (7 * 24 * 60 * 60 * 1000)); // 7 dias
        document.cookie = `token=${token}; expires=${expires.toUTCString()}; path=/;`;
        document.cookie = `account=${globals.account_id}; expires=${expires.toUTCString()}; path=/;`;
        document.cookie = `user=${globals.user_id}; expires=${expires.toUTCString()}; path=/;`;
        document.cookie = `is_operator=${globals.is_operator}; expires=${expires.toUTCString()}; path=/;`;

        if(globals.is_operator) {
            $('#login_modal_id').modal('hide');
            $('#select_account_cancel_btn_id').hide();
            listAccounts();
        } else {
            mainBrandSelect();

            $('#login_modal_id').modal('hide');
        }
            
    }).catch(e => {
        alert('Erro ao fazer login:', e);
        console.error(e);
        returnToNormal(event.currentTarget);
    });
}

module.exports = {
    changePassword,
    logout,
    login
}
