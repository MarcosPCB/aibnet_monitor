const { forEach } = require('lodash');
require('./bootstrap');
const globals = require('./globals');
const { cleanDOM, cleanMsgBody, readCookie, appLoad } = require('./utils');
const { addThread, sendMsgThread, renameChat, deleteChat, listChats } = require('./funcs/chat');
const { login, logout, changePassword } = require('./funcs/auth');
const { listAccounts, createAccount } = require('./funcs/account');
const { switchBrand, mainBrandSelect, listBrands, loadBrandPic, createMainBrand } = require('./funcs/mainBrand');
const { createBrand, listBBrands, editPrimaryBrand } = require('./funcs/brand');
const { createUser, listUsers } = require('./funcs/user');
const { createPlatform, savePlatform } = require('./funcs/platform');

$(document).ready(function(){
    $('#action_menu_btn').click(function(){
        $('.action_menu').toggle();
    });

    $('#load_app_id').on('transitionend', function() {
        if($('#load_app_id').css('opacity') == '0')
            $('#load_app_id').hide();
    });

    $('#add_thread_btn_id').click(addThread);
    $('#send_btn_id').click(sendMsgThread);
    $('#rename_chat_btn_id').click(renameChat);
    $('#delete_chat_btn_id').click(deleteChat);
    $('#switch_brand_btn_id').click(switchBrand);
    $('#change_password_btn_id').click(changePassword);
    $('#logout_btn_id').click(logout);
    $('#login_btn_id').click(login);
    $('#account_select_btn_id').click(mainBrandSelect);
    $('#create_user_btn_id').click(createUser);
    $('#change_account_btn_id').click(listAccounts);
    $('#create_account_2_btn_id').click(createAccount);
    $('#create_new_brand_btn_id').click(createBrand);
    $('#create_new_platform_btn_id').click(createPlatform);
    $('#edit_primary_brand_btn_id').click(editPrimaryBrand);
    $('#save_edit_platform_btn_id').click(savePlatform);
    $('#create_main_brand_btn_id').click(createMainBrand);

    $(document).on('shown.bs.modal', function (e) {
        const modalId = $(e.target).attr('id');
        globals.modalHistory.push(modalId);

        if(modalId == 'create_main_brand_modal_id') {
            listBBrands();

            const pBrand = $('#new_main_brand_primary_id').val();

            if(!pBrand)
                $('#edit_primary_brand_btn_id')[0].classList.add('disabled_btn');
            else
                $('#edit_primary_brand_btn_id')[0].classList.remove('disabled_btn');

            const oBrand1 = $('#new_main_brand_opponent_1_id').val();

            if(oBrand1 == -1)
                $('#edit_opponent_1_brand_btn_id')[0].classList.add('disabled_btn');
            else
                $('#edit_opponent_1_brand_btn_id')[0].classList.remove('disabled_btn');

            const oBrand2 = $('#new_main_brand_opponent_2_id').val();

            if(oBrand2 == -1)
                $('#edit_opponent_2_brand_btn_id')[0].classList.add('disabled_btn');
            else
                $('#edit_opponent_2_brand_btn_id')[0].classList.remove('disabled_btn');
        }
    });

    $('#new_main_brand_primary_id').on('change', function() {
        // A função será executada quando o valor do select mudar
        const pBrand = $(this).val();

        if(!pBrand)
            $('#edit_primary_brand_btn_id')[0].classList.add('disabled_btn');
        else
            $('#edit_primary_brand_btn_id')[0].classList.remove('disabled_btn');
    });

    $('#new_main_brand_opponent_1_id').on('change', function() {
        // A função será executada quando o valor do select mudar
        const oBrand = $(this).val();

        if(oBrand == -1)
            $('#edit_opponent_1_brand_btn_id')[0].classList.add('disabled_btn');
        else
            $('#edit_opponent_1_brand_btn_id')[0].classList.remove('disabled_btn');
    });

    $('#new_main_brand_opponent_2_id').on('change', function() {
        // A função será executada quando o valor do select mudar
        const oBrand = $(this).val();

        if(oBrand == -1)
            $('#edit_opponent_2_brand_btn_id')[0].classList.add('disabled_btn');
        else
            $('#edit_opponent_2_brand_btn_id')[0].classList.remove('disabled_btn');
    });


    $(document).on('click', '.cancel-btn', function (e) {
        const currentModalId = globals.modalHistory.pop();
        const previousModalId = globals.modalHistory[globals.modalHistory.length - 1];
    
        if (previousModalId) {
            $(`#${currentModalId}`).modal('hide');
            $(`#${previousModalId}`).modal('show');
        }
    });

    globals.msg_body = $('#msg_card_body_id')[0];
    globals.msg_area = $('#msg_area_id')[0];
    globals.chat_cards = $('#chat_cards_id')[0];
    globals.chat_name = $('#chat_name_id')[0];
    globals.chat_num_msgs = $('#chat_num_msgs_id')[0];

    globals.msg_area.addEventListener('input', event => {
        let dom = event.target;

        if(dom.value.length > 0)
            $('#send_btn_id')[0].classList.remove('disabled_btn');
        else
            $('#send_btn_id')[0].classList.add('disabled_btn');
    });

    if(readCookie('token') == '') {
        $('#login_modal_id').modal('show');
        return;
    }

    globals.account_id = readCookie('account');
    globals.main_brand_id = readCookie('main_brand');
    globals.is_operator = readCookie('is_operator');
    globals.user_id = readCookie('user');
    const token = readCookie('token');

    window.axios.get(globals.api_url + `user/${globals.user_id}/${globals.account_id}`, {
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    }).then(response => {
        listChats();
        listBrands();
        loadBrandPic();
    }).catch(e => {
        // Try checking if it's an operator
        window.axios.get(globals.api_url + `operator/${globals.user_id}`, {
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        }).then(response => {
            listChats();
            listBrands();
            loadBrandPic();
            $('#account-modal-tab')[0].classList.remove('disabled_btn');
            listUsers();
        }).catch(e => {
            appLoad();
            appLoad();
            appLoad();
            cleanDOM(globals.chat_cards);
            globals.chat_cards.innerHTML = globals.add_chat;
            cleanMsgBody();
            $('#account-modal-tab')[0].classList.add('disabled_btn');
    
            $('#login_modal_id').modal('show');
        });
    });
});
