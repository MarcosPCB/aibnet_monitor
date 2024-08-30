const { forEach } = require('lodash');
require('./bootstrap');
const globals = require('./globals');
const { cleanDOM, cleanMsgBody, readCookie } = require('./utils');
const { addThread, sendMsgThread, renameChat, deleteChat, listChats } = require('./funcs/chat');
const { login, logout, changePassword } = require('./funcs/auth');
const { listAccounts, createAccount } = require('./funcs/account');
const { switchBrand, mainBrandSelect, listBrands, loadBrandPic } = require('./funcs/mainBrand');
const { createBrand, listBBrands, editPrimaryBrand } = require('./funcs/brand');
const { createUser, listUsers } = require('./funcs/user');
const { createPlatform } = require('./funcs/platform');

$(document).ready(function(){
    $('#action_menu_btn').click(function(){
        $('.action_menu').toggle();
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

    $(document).on('shown.bs.modal', function (e) {
        const modalId = $(e.target).attr('id');
        globals.modalHistory.push(modalId);

        if(modalId == 'create_main_brand_modal_id')
            listBBrands();
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

    $('#create_main_brand_modal_id').modal('show');

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
            alert('Você foi deslogado');
            cleanDOM(globals.chat_cards);
            globals.chat_cards.innerHTML = globals.add_chat;
            cleanMsgBody();
            $('#account-modal-tab')[0].classList.add('disabled_btn');
    
            $('#login_modal_id').modal('show');
        });
    });
});
