require('../bootstrap');
const globals = require('../globals');
const { readCookie, checkAuth, changeToLoad, returnToNormal } = require('../utils');
const { listPlatforms } = require('./platform');

async function listBBrands() {
    const token = readCookie('token');

    try {
        const response = await window.axios.get(globals.api_url + `brand/list`, {
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (response.status != 200) {
            throw new Error(response.body + ` code: ${response.status}`);
        }

        let options = '';
        const brands = response.data;

        for(let i = 0; i < brands.length; i++) {
            options += `<option value=${brands[i].id}>ID: ${brands[i].id} - ${brands[i].name}</option>\n`;
        }

        $('.list-brands').each(function() {
            $(this).html(options);
        });

        $('.list-brands-opponents').each(function() {
            $(this).html(`<option value=-1>Nenhum</option>\n` + options);
        });
        
    } catch(e) {
        alert('Erro ao listar marcas:', e);
        checkAuth(e.response);
    }
}

async function getBBrand() {
    const token = readCookie('token');

    try {
        const response = await window.axios.get(globals.api_url + `brand/${globals.brand_id}`, {
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (response.status != 200) {
            throw new Error(response.body + ` code: ${response.status}`);
        }

        return response.data;

    } catch(e) {
        alert('Erro ao buscar marca:', e);
        checkAuth(e.response);
        return null;
    }
}

function createBrand(event) {
    const token = readCookie('token');
    const name = $('#new_brand_name_id').val();
    const description = $('#new_brand_desc_id').val();

    changeToLoad(event.currentTarget);

    window.axios.post(globals.api_url + `brand/create`, {
        name,
        description
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

        alert('Marca criada');

        returnToNormal(event.currentTarget);

        listBBrands();
        $('#create_brand_modal_id').modal('hide');
        $('#create_platform_modal_id').modal('show');
        globals.brand_id = response.data.id;
    }).catch(e => {
        alert('Erro ao criar marca:', e);
        returnToNormal(event.currentTarget);
        checkAuth(e.response);
    });
}

async function editPrimaryBrand(event) {
    globals.brand_id = $('#new_main_brand_primary_id').val();
    listPlatforms();

    const brand = await getBBrand();

    $('#edit_brand_name_id').val(brand.name);
    $('#edit_brand_desc_id').val(brand.description);

    $('#create_main_brand_modal_id').modal('hide');
    $('#edit_brand_modal_id').modal('show');
}

module.exports = {
    listBBrands,
    createBrand,
    editPrimaryBrand,
    getBBrand
}
