require('../bootstrap');
const globals = require('../globals');
const { readCookie, checkAuth, cleanMsgBody } = require('../utils');
const { listUsers } = require('./user');
const { listChats } = require('./chat');

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
    }).catch(e => {
        console.error('Erro ao listar marcas:', e);
        checkAuth(e.response);
    });
}

function switchBrand() {
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

        for(let i = 0; i < platforms.length; i++) {
            if(platforms[i].avatar_url != null && platforms[i].avatar_url != '') {
                brand_pic.src = platforms[i].avatar_url;
                break;
            }
        }
    }).catch(e => {
        alert('Erro ao mudar avatar', e);
        checkAuth(e.response);
    });
}

function mainBrandSelect() {
    globals.account_id = $('#account_select_id')[0].value;
    listBrands();
    listUsers();

    const expires = new Date();
    expires.setTime(expires.getTime() + (7 * 24 * 60 * 60 * 1000)); // 7 dias
    document.cookie = `account=${globals.account_id}; expires=${expires.toUTCString()}; path=/;`;
    $('#select_account_modal_id').modal('hide');
    $('#switch_modal_id').modal('show');
}

module.exports = {
    mainBrandSelect,
    listBrands,
    switchBrand,
    loadBrandPic
};
