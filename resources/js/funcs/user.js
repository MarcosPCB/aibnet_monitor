require('../bootstrap');
const globals = require('../globals');
const { readCookie, checkAuth, changeToLoad, returnToNormal } = require('../utils');

function listUsers() {
    const token = readCookie('token');

    window.axios.get(globals.api_url + `account/list/users/${globals.account_id}`, {
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    }).then(response => {
        const list = $('#list_users_id')[0];
        list.innerHTML = '';

        response.data.forEach(e => {
            const element = 
            $(`<li class="list-group-item d-flex align-items-center mb-2" data-api-id=${e.id}>
                <span class="me-auto">Usuário: ${e.id} - ${e.name}</span>
                <span class="btn btn-icon permission-btn text-white">
                    <i class="fa-solid fa-ballot-check"></i>
                </span>
                <span class="btn btn-icon delete-btn text-white">
                    <i class="fa-solid fa-trash-can-xmark"></i>
                </span>
            </li>`);

            element.find('.delete-btn').on('click', () => {
                const r = confirm('Deseja mesmo excluir este usuário?');
                if(r) {
                    window.axios.delete(globals.api_url + `user/delete/${e.id}/${globals.account_id}`, {
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    }).then(() => {
                        listUsers();
                    }).catch((e) => {
                        checkAuth(e);
                        alert('Erro: não foi possível excluir o usuário');
                    });
                }
            });

            list.appendChild(element[0]);
        });
    }).catch(e => {
        alert('Erro ao listar usuários');
        console.error('error', e);
        checkAuth(e);
    });
}

function createUser(event) {
    const token = readCookie('token');

    const name = $('#new_user_name_id')[0].value;
    const email = $('#new_user_email_id')[0].value;
    const password = $('#new_user_password_id')[0].value;
    const confirm_password = $('#new_user_confirm_password_id')[0].value;

    if(password != confirm_password) {
        alert('Senhas diferentes');
        console.error('Senhas diferentes');
        return;
    }

    changeToLoad(event.currentTarget);

    window.axios.post(globals.api_url + `user/create/${globals.account_id}`, {
        name,
        email,
        password,
        permission: '1',
        account_id: globals.account_id
    }, {
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    }).then(response => {
        if (response.status != 201) {
            throw new Error(response.body + ` code: ${response.status}`);
        }

        alert('Usuário criado');

        returnToNormal(event.currentTarget);

        listUsers();
        $('#create_user_modal_id').modal('hide');

        if (!globals.account_creation)
            $('#config_modal_id').modal('show');
        else
            $('#create_main_brand_modal_id').modal('show');
            
    }).catch(e => {
        alert('Erro ao criar usuário:', e);
        returnToNormal(event.currentTarget);
        checkAuth(e.response);
    });
}

module.exports = {
    listUsers,
    createUser
};
