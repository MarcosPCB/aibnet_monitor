require('../bootstrap');
const globals = require('../globals');
const { readCookie, checkAuth, cleanMsgBody, changeToLoad, returnToNormal, saveCookie, appLoad, startAppLoad } = require('../utils');
const { listUsers } = require('./user');
const { listChats } = require('./chat');
const { listBBrands } = require('./brand');

function listBrands() {
    const token = readCookie('token');

    window.axios.get(globals.api_url + `account/list/main-brands/${globals.account_id}`, {
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    }).then(response => {
        if (response.status != 200) {
            throw new Error(response.body + ` code: ${response.status}`);
        }

        const list = $('#brand_select_id')[0];
        list.innerHTML = '';

        const brands = response.data;

        brands.forEach((e, i) => {
            let selected = globals.main_brand_id == e.id ? 'selected' : '';
            list.innerHTML += `<option value=${e.id} ${selected}>${e.name}</option>`;
        });

        appLoad();
    }).catch(e => {
        console.error('Erro ao listar marcas:', e);
        checkAuth(e.response);
        appLoad();
    });
}

function fillEditOpponents() {
    const token = readCookie('token');

    window.axios.get(globals.api_url + `main-brand/${globals.main_brand_id}/${globals.account_id}`, {
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    }).then(response => {
        if (response.status != 200) {
            throw new Error(response.body + ` code: ${response.status}`);
        }

        const mb = response.data;

        $('#edit_main_brand_name_id').val(mb.mainBrand.name);
        $('#edit_main_brand_model_id').val(mb.mainBrand.chat_model);

        const primary = $('#edit_main_brand_primary_id')[0];
        primary.innerHTML = '';

        const elementPrimary = 
            $(`<li class="list-group-item d-flex align-items-center data-api-id=${mb.primary.id}">
                <span class="me-auto">Marca ${mb.primary.id}: ${mb.primary.name}</span>
                <span class="btn btn-icon edit-btn text-white">
                    <i class="fa-solid fa-pen-to-square"></i>
                </span>
            </li>`);

        elementPrimary.find('.edit-btn').on('click', async () => {
            await listBBrands();

            // Definindo os valores dos campos no modal
            $('#select_edit_brand_id').val(mb.primary.id);
            select_edit_brand_id = 0;

            $('#edit_main_brand_modal_id').modal('hide');
            $('#select_edit_brand_modal_id').modal('show');
        });

        primary.appendChild(elementPrimary[0]);

        const list = $('#edit_main_brand_opponents_id')[0];
        list.innerHTML = '';

        for(let i = 0; i < mb.opponents.length; i++) {
            const e = mb.opponents[i];
            const element = 
                $(`<li class="list-group-item d-flex align-items-center data-api-id=${e.id}">
                    <span class="me-auto">Marca ${e.id}: ${e.name}</span>
                    <span class="btn btn-icon edit-btn text-white">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </span>
                    <span class="btn btn-icon delete-btn text-white">
                        <i class="fa-solid fa-trash-can-xmark"></i>
                    </span>
                </li>`);

            element.find('.edit-btn').on('click', async () => {
                await listBBrands();

                $('#select_edit_brand_id').val(e.id);
                select_edit_brand_id = i + 1;
    
                // Definindo os valores dos campos no modal
                $('#select_edit_brand_id').val(e.id);
    
                $('#edit_main_brand_modal_id').modal('hide');
                $('#select_edit_brand_modal_id').modal('show');
            });

            if(i > 1) {
                element.find('.delete-btn').on('click', () => {
                    const r = confirm('Deseja mesmo excluir este concorrente?');
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
                            alert('Erro: não foi possível excluir o concorrente');
                        });
                    }
                });
            } else {
                element.find('.delete-btn')[0].classList.add('disabled_btn');
            }

            list.appendChild(element[0]);
        }
    }).catch(e => {
        console.error('Erro ao listar marcas:', e);
        checkAuth(e);
    });
}

function switchBrand() {
    startAppLoad();
    globals.main_brand_id = $('#brand_select_id')[0].value;

    const expires = new Date();
    expires.setTime(expires.getTime() + (7 * 24 * 60 * 60 * 1000)); // 7 dias
    document.cookie = `main_brand=${globals.main_brand_id}; expires=${expires.toUTCString()}; path=/;`;

    $('#switch_modal_id').modal('hide');
    cleanMsgBody();
    listChats();
    loadBrandPic();
}

function loadBrandPic() {
    const token = readCookie('token');

    window.axios.get(globals.api_url + `main-brand/primary/platforms/${globals.main_brand_id}/${globals.account_id}`, {
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    }).then(response => {
        if (response.status != 200) {
            throw new Error(response.body + ` code: ${response.status}`);
        }

        const brand_pic = $('#brand_pic_id')[0];

        const platforms = response.data;

        if(platforms.length == 0) {
            throw 'Nenhuma plataforma foi carregada. acione o administrador da conta';
        }

        let src = '';

        for(let i = 0; i < platforms.length; i++) {
            if(platforms[i].avatar_url != null && platforms[i].avatar_url != '') {
                src = platforms[i].avatar_url;
                break;
            }
        }

        fetch(`/api/proxy-image`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ url: src })
        })
        .then(response => response.blob()) // Recebe a imagem como blob
        .then(blob => {
            brand_pic.src = URL.createObjectURL(blob); // Exibe a imagem
            appLoad();
        })
        .catch(error => {
            console.error('Erro puxando a imagem:', error);
            brand_pic.src = 'img/logo_black.png'
            appLoad();
        });
    }).catch(e => {
        alert(`Erro ao mudar avatar: ${e}`);
        checkAuth(e);
        appLoad();
    });
}

function mainBrandSelect() {
    if(!globals.login_flow)
        globals.account_id = $('#account_select_id')[0].value;

    globals.login_flow = false;

    listBrands();
    listUsers();

    const expires = new Date();
    expires.setTime(expires.getTime() + (7 * 24 * 60 * 60 * 1000)); // 7 dias
    document.cookie = `account=${globals.account_id}; expires=${expires.toUTCString()}; path=/;`;
    $('#select_account_modal_id').modal('hide');
    $('#switch_modal_id').modal('show');
}

function createMainBrand(event) {
    const token = readCookie('token');
    const name = $('#new_main_brand_name_id').val();
    const primaryBrandId = $('#new_main_brand_primary_id').val();
    const opponentBrand1Id = $('#new_main_brand_opponent_1_id').val();
    const opponentBrand2Id = $('#new_main_brand_opponent_2_id').val();
    const chat_model = $('#new_main_brand_model_id').val();

    changeToLoad(event.currentTarget);

    window.axios.post(globals.api_url + `main-brand/create/${globals.account_id}`, {
        name,
        main_brand_id: primaryBrandId,
        chat_model,
        opponents: [
            opponentBrand1Id,
            opponentBrand2Id
        ]
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

        alert('Cliente criado');

        returnToNormal(event.currentTarget);

        listBrands();
        $('#create_main_brand_modal_id').modal('hide');
        globals.modalHistory = [];
        globals.main_brand_id = response.data.id;
        saveCookie('main_brand');
        globals.account_creation = false;
        globals.platform_id = -1;
        globals.brand_id = -1;
        cleanMsgBody();
        listChats();
        loadBrandPic();
    }).catch(e => {
        alert('Erro ao criar cliente:', e);
        returnToNormal(event.currentTarget);
        checkAuth(e);
    });
}

function genWeeklyReport(event) {
    const token = readCookie('token');
    changeToLoad(event.currentTarget);

    window.axios.get(globals.api_url + `main-brand/weekly/${globals.main_brand_id}/${globals.account_id}`, {
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    }).then(response => {
        if (response.status != 200) {
            throw new Error(response.body + ` code: ${response.status}`);
        }

        returnToNormal(event.currentTarget);

        alert('Relatório criado e armazenado no sistema');
    }).catch(e => {
        alert(`Erro ao gerar relatório: ${e}`);
        returnToNormal(event.currentTarget);
        checkAuth(e);
    });
}

function genMonthReport(event) {
    const token = readCookie('token');
    const month = $('#select_month_id').val();
    changeToLoad(event.currentTarget);

    window.axios.get(globals.api_url + `main-brand/month/${globals.main_brand_id}/${month}/${globals.account_id}`, {
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    }).then(response => {
        if (response.status != 200) {
            throw new Error(response.body + ` code: ${response.status}`);
        }

        returnToNormal(event.currentTarget);

        alert('Relatório criado e armazenado no sistema');
        $('#select_month_report_modal_id').modal('hide');
        $('#config_modal_id').modal('show');
    }).catch(e => {
        alert(`Erro ao gerar relatório: ${e}`);
        returnToNormal(event.currentTarget);
        checkAuth(e);
    });
}

async function editMainBrandBrands(event) {
    const token = readCookie('token');
    const brand_id = $('#select_edit_brand_id').val();

    changeToLoad(event.currentTarget);

    let response = await window.axios.get(globals.api_url + `main-brand/${globals.main_brand_id}/${globals.account_id}`, {
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });

    const mb = response.data;

    async function detach(id) {
        try {
        const response = await window.axios.patch(globals.api_url + `main-brand/detach/${globals.main_brand_id}/${globals.account_id}`, {
            brand_id: id
            }, {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            if (response.status != 200) {
                throw new Error(response.body + ` code: ${response.status}`);
            }
    
           return true;
        } catch(e) {
            alert(`Erro ao retirar marca ${id} do cliente`);
            checkAuth(e);
            return false;
        }
    }


    if(globals.selected_edit_brand == 0) {
        const result = await detach(mb.primary.id);

        if(!result) {
            returnToNormal(event.currentTarget);
            return false;
        }
    } else {
        const result = await detach(mb.opponents[globals.selected_edit_brand - 1]);

        if(!result) {
            returnToNormal(event.currentTarget);
            return false;
        }
    }

    try {
        response = await window.axios.patch(globals.api_url + `main-brand/attach/${globals.main_brand_id}/${globals.account_id}`, {
        brand_id,
        is_opponent: globals.selected_edit_brand > 0 ? true : false
        }, {
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (response.status != 200) {
            throw new Error(response.body + ` code: ${response.status}`);
        }

        returnToNormal(event.currentTarget);

        fillEditOpponents();
        $('#select_edit_brand_modal_id').modal('hide');
        $('#edit_main_brand_modal_id').modal('show');

        return true;
    } catch(e) {
        alert('Erro ao atracar nova marca');
        returnToNormal(event.currentTarget);
        checkAuth(e);
        return false;
    }
}

module.exports = {
    mainBrandSelect,
    listBrands,
    switchBrand,
    loadBrandPic,
    createMainBrand,
    fillEditOpponents,
    genWeeklyReport,
    editMainBrandBrands,
    genMonthReport
};
