require('../bootstrap');
const globals = require('../globals');
const { readCookie, checkAuth, changeToLoad, returnToNormal } = require('../utils');

function createPlatform(event) {
    const token = readCookie('token');

    const name = $('#new_platform_name_id').val();
    const url = $('#new_platform_url_id').val();
    const type = $('#new_platform_type_id').val();
    const platform_id = $('#new_platform_id_id').val();
    const active = $('#new_platform_active_id').is(':checked');

    changeToLoad(event.currentTarget);

    window.axios.post(globals.api_url + `platform/create`, {
        name,
        url,
        type,
        platform_id,
        active,
        brand_id: globals.brand_id
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

        returnToNormal(event.currentTarget);

        const r = confirm('Plataforma criada, deseja adicionar outra?');

        if(r) {
            $('#new_platform_name_id').val('');
            $('#new_platform_url_id').val('');
            $('#new_platform_type_id').val('');
            $('#new_platform_id_id').val('');
            $('#new_platform_active_id').prop('checked', false);
        } else {
            if (globals.account_creation) {
                $('#create_brand_modal_id').modal('hide');
                $('#create_main_brand_modal_id').modal('show');
            } else {
                const currentModalId = globals.modalHistory.pop();
                const previousModalId = globals.modalHistory[globals.modalHistory.length - 1];
            
                if (previousModalId) {
                    $(`#${currentModalId}`).modal('hide');
                    $(`#${previousModalId}`).modal('show');
                }
            }
        }
    }).catch(e => {
        alert('Erro ao criar marca:', e);
        returnToNormal(event.currentTarget);
        checkAuth(e.response);
    });
}

function listPlatforms() {
    const token = readCookie('token');

    window.axios.get(globals.api_url + `brand/list/platforms/${globals.brand_id}`, {
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    }).then(response => {
        const list = $('#list_platforms_id')[0];
        list.innerHTML = '';

        response.data.forEach(e => {
            const element = 
                $(`<li class="list-group-item d-flex align-items-center data-api-id=${e.id}>
                    <span class="me-auto">Plataforma ${e.id}: ${e.type} - ${e.name}</span>
                    <span class="btn btn-icon edit-btn text-white">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </span>
                    <span class="btn btn-icon delete-btn text-white">
                        <i class="fa-solid fa-trash-can-xmark"></i>
                    </span>
                </li>`);

            element.find('.delete-btn').on('click', () => {
                const r = confirm('Deseja mesmo excluir esta plataforma?');
                if(r) {
                    window.axios.delete(globals.api_url + `platform/delete/${e.id}`, {
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    }).then(() => {
                        listUsers();
                    }).catch((e) => {
                        checkAuth(e);
                        alert('Erro: não foi possível excluir a plataforma');
                    });
                }
            });

            element.find('.edit-btn').on('click', () => {
                globals.platform_id = e.id;
                $('#edit_brand_modal_id').modal('hide');
                $('#edit_platform_modal_id').modal('show');
            });

            list.appendChild(element[0]);
        });
    }).catch(e => {
        alert('Erro ao listar usuários');
        console.error('error', e);
        checkAuth(e);
    });
}

module.exports = {
    createPlatform,
    listPlatforms
};
