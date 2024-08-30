const globals = require('./globals');

function readCookie(name) {
    const nameEQ = name + "=";
    const cookies = document.cookie.split(';');
    for(let i = 0; i < cookies.length; i++) {
        let cookie = cookies[i];
        while (cookie.charAt(0) === ' ') {
            cookie = cookie.substring(1);
        }
        if (cookie.indexOf(nameEQ) === 0) {
            return cookie.substring(nameEQ.length, cookie.length);
        }
    }
    return null;
}

function saveCookie(type, token) {
    const expires = new Date();
    expires.setTime(expires.getTime() + (7 * 24 * 60 * 60 * 1000)); // 7 dias

    switch(type) {
        case 'token':
            document.cookie = `token=${token}; expires=${expires.toUTCString()}; path=/;`;
            break;

        case 'account':
            document.cookie = `account=${globals.account_id}; expires=${expires.toUTCString()}; path=/;`;
            break;

        case 'user':
            document.cookie = `user=${globals.user_id}; expires=${expires.toUTCString()}; path=/;`;
            break;
        
        case 'operator':
            document.cookie = `is_operator=${globals.is_operator}; expires=${expires.toUTCString()}; path=/;`;
            break;

        case 'main_brand':
            document.cookie = `main_brand=${globals.main_brand_id}; expires=${expires.toUTCString()}; path=/;`;
            break;

        case 'all':
            document.cookie = `token=${token}; expires=${expires.toUTCString()}; path=/;`;
            document.cookie = `account=${globals.account_id}; expires=${expires.toUTCString()}; path=/;`;
            document.cookie = `user=${globals.user_id}; expires=${expires.toUTCString()}; path=/;`;
            document.cookie = `is_operator=${globals.is_operator}; expires=${expires.toUTCString()}; path=/;`;
            document.cookie = `main_brand=${globals.main_brand_id}; expires=${expires.toUTCString()}; path=/;`;
            break;
    }
}

function checkAuth(data) {
    if(data.status == 401) {
        alert('Você foi deslogado');
        cleanDOM(globals.chat_cards);
        globals.chat_cards.innerHTML = globals.add_chat;
        cleanMsgBody();

        $('#login_modal_id').modal('show');

        document.cookie = `token=;`;
    }
}

function cleanMsgBody() {
    globals.msg_area.value = '';
    globals.msg_body.innerHTML = 
        `<div class="d-flex justify-content-center align-items-center h-100">
            <img class="img-fluid h-100" src="img/logo_cyan.png">
        </div>`;

    if($('#msg_body_footer')[0].classList.length == 1)
        $('#msg_body_footer')[0].classList.add('move-down');

    globals.chat_name.innerHTML = '';
    globals.chat_num_msgs.innerHTML = '';
}

function changeToLoad(target) {
    globals.backup = target.innerHTML;
    target.innerHTML = `<div class="spinner-border text-black" role="status"></div>`;
}

function returnToNormal(target) {
    target.innerHTML = globals.backup;
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function cleanDOM(dom) {
    dom.innerHTML = '';
}

function attachDOM(dom, content) {
    dom.innerHTML += content;
}

function addTextLastBubble(text) {
    let len = globals.msg_body.children.length - 1;
    let s = globals.msg_body.children[len].children[0].children[0];
    s.innerHTML += marked.parse(text); 
    globals.msg_body.scrollTop = globals.msg_body.scrollHeight;
}

function setTextLastBubble(text) {
    let len = globals.msg_body.children.length - 1;
    let s = globals.msg_body.children[len].children[0].children[0];
    s.innerHTML = marked.parse(text); 
    globals.msg_body.scrollTop = globals.msg_body.scrollHeight;
}

async function loadingTextLastBubble() {
    let len = globals.msg_body.children.length - 1;
    let s = globals.msg_body.children[len].children[0].children[0];
    s.innerHTML = `<div class="spinner-border text-white" role="status"></div>`;
} 

function disableButtons() {
    $('#send_btn_id')[0].classList.add('disabled_btn');
    $('#add_thread_btn_id')[0].classList.add('disabled_btn');
    globals.msg_area.classList.add('disabled_btn');
    globals.chat_cards.classList.add('disabled_btn');

    Array.from(globals.chat_cards.children).forEach((e, i) => {
        if(i == 0)
            return;

        e.children[0].children[1].children[0].classList.add('disabled_btn');
    });
}

function enableButtons() {
    //$('#send_btn_id')[0].classList.remove('disabled_btn');
    $('#add_thread_btn_id')[0].classList.remove('disabled_btn');
    globals.msg_area.classList.remove('disabled_btn');
    globals.chat_cards.classList.remove('disabled_btn');

    Array.from(globals.chat_cards.children).forEach((e, i) => {
        if(i == 0)
            return;
        
        e.children[0].children[1].children[0].classList.remove('disabled_btn');
    });
}

module.exports = {
    readCookie,
    saveCookie,
    checkAuth,
    cleanMsgBody,
    changeToLoad,
    returnToNormal,
    sleep,
    cleanDOM,
    attachDOM,
    addTextLastBubble,
    setTextLastBubble,
    loadingTextLastBubble,
    disableButtons,
    enableButtons,
};
