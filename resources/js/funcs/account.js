require('../bootstrap');
const globals = require('../globals');
const { checkAuth, readCookie, changeToLoad, returnToNormal, saveCookie, cleanDOM, cleanMsgBody } = require('../utils');

function listAccounts() {
    const token = readCookie('token');

    window.axios.get(globals.api_url + `account/list/all`, {
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    }).then(response => {
        const list = $('#account_select_id')[0];
        list.innerHTML = '';

        for(let i = 0; i < response.data.length; i++) {
            const e = response.data[i];
            let selected = i == 0 ? 'selected' : '';
            list.innerHTML += `<option value=${e.id} ${selected}>Conta: ${e.id} - ${e.name}</option>`;
        };

        $('#select_account_modal_id').modal('show');
    }).catch(e => {
        alert('Erro ao listar contas');
        checkAuth(e);
    });
}

function createAccount(event) {
    const token = readCookie('token');

    const r = confirm('Revise as informações inseridas.\nDeseja prosseguir?');

    function getContractTimeInMonths(contractType) {
        switch (contractType) {
            case '0': // Mensal
                return 1;
            case '1': // Trimestral
                return 3;
            case '2': // Semestral
                return 6;
            case '3': // Anual
                return 12;
            case '4': // 2 anos
                return 24;
            case '5': // Promocional
                return -1;
            default:
                return 0; // Valor padrão se nenhum dos casos corresponder
        }
    }

    if(r) {
        changeToLoad(event.currentTarget);
        const contractTypeValue = $('#new_account_contype_id').val();
        const contractTimeInMonths = getContractTimeInMonths(contractTypeValue);
        
        window.axios.post(globals.api_url + `account/create`, {
            name: $('#new_account_name_id').val(),
            token: 'some_token_value', // Valor padrão para token
            payment_method: $('#new_account_paymethod_id').val(),
            installments: $('#new_account_installments_id').val(),
            contract_type: contractTypeValue,
            contract_description: $('#new_account_condesc_id').val(),
            contract_brands: $('#new_account_conbrands_id').val(),
            contract_brand_opponents: $('#new_account_conbrandopp_id').val(),
            contract_users: $('#new_account_conusers_id').val(),
            contract_build_brand_time: $('#new_account_conbuild_id').val(),
            contract_time: contractTimeInMonths,
            contract_monitored: 1, // Valor padrão para contrato monitorado
            cancel_time: $('#new_account_cancel_id').val(),
            paid: $('#new_account_paid_id').is(':checked'),
            active: $('#new_account_active_id').is(':checked')
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
    
            alert('Conta criada');
            returnToNormal(event.currentTarget);

            globals.account_creation = true;
            globals.account_id = response.data.id;
            saveCookie('account');

            cleanDOM(globals.chat_cards);
            cleanMsgBody();
            globals.chat_cards.innerHTML = globals.add_chat;
    
            $('#create_account_modal_id').modal('hide');
            $('#create_user_modal_id').modal('show');
        }).catch(e => {
            alert('Erro ao criar conta:', e);
            returnToNormal(event.currentTarget);
            console.error('Erro ao criar conta:', e);
            checkAuth(e.response);
        });
    }
}

module.exports = {
    listAccounts,
    createAccount
}
