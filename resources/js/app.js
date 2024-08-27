const { forEach } = require('lodash');

require('./bootstrap');
const expires = new Date();
expires.setTime(expires.getTime() + (7 * 24 * 60 * 60 * 1000)); // 7 dias
document.cookie = `token=1|vozJ1bfLrt2q1cineDcroHEvPVDDEmaoPhYIQfSi; expires=${expires.toUTCString()}; path=/;`;

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

var account_id = 1;
var main_brand_id = 1;

var incompleteData = '';
var incompleteEvent = '';
var currentEvent = null;
var current_thread = -1;

function identifyEvent(inputString) {
    const events = [
      "thread.created",
      "thread.run.created",
      "thread.run.queued",
      "thread.run.in_progress",
      "thread.run.requires_action",
      "thread.run.completed",
      "thread.run.incomplete",
      "thread.run.failed",
      "thread.run.cancelling",
      "thread.run.cancelled",
      "thread.run.expired",
      "thread.run.step.created",
      "thread.run.step.in_progress",
      "thread.run.step.delta",
      "thread.run.step.completed",
      "thread.run.step.failed",
      "thread.run.step.cancelled",
      "thread.run.step.expired",
      "thread.message.created",
      "thread.message.in_progress",
      "thread.message.delta",
      "thread.message.completed",
      "thread.message.incomplete",
      "error",
      "done"
    ];
  
    // Verifica se algum dos eventos está na string
    for (const event of events) {
      if (inputString.includes(event)) {
        return event;
      }
    }
  
    // Se nenhum evento foi identificado, retorna "incomplete"
    return "incomplete";
  }

function processString(chunk) {
    const lines = chunk.split('\n');
    const events = [];

    for (let line of lines) {
        line = line.trim();
        if (!line) continue;

        // Verifica se estamos processando um dado incompleto
        if (incompleteData) {
            incompleteData += line;
            try {
                const jsonData = JSON.parse(incompleteData);
                if (currentEvent) {
                    const contentArray = jsonData.delta?.content || [];
                    for (let content of contentArray) {
                        if (content.type === 'text' && content.text?.value) {
                            events.push(content.text.value);
                        }
                    }
                }
                incompleteData = ''; // Limpa o estado após processar o dado completo
                currentEvent = null;
                continue;
            } catch (error) {
                // Continua acumulando dados se o JSON ainda estiver incompleto
                continue;
            }
        }

        if(incompleteEvent) {
            incompleteEvent += line;
            if (incompleteEvent.startsWith('event:')) {
                currentEvent = incompleteEvent.slice(6).trim();
                if (currentEvent !== "thread.message.delta") {
                    currentEvent = null;
                    incompleteEvent = '';
                    continue;
                }
                incompleteEvent = '';
                continue;
            }
        }

        // Identifica a ID da thread
        if (line.startsWith('API_THREAD_ID:')) {
            let data = line.slice(14).trim();
            current_thread = parseInt(data.slice(0, data.indexOf(';')));
            continue;
        }

        // Identifica eventos
        if (line.startsWith('event:')) {
            currentEvent = line.slice(6).trim();

            let event = identifyEvent(currentEvent);
            if (event != "thread.message.delta") {
                if(event == 'incomplete')
                    incompleteEvent = line;

                currentEvent = null;
                continue;
            }
            continue;
        }

        // Identifica dados
        if (line.startsWith('data:')) {
            let data = line.slice(5).trim();

            // Se há dados incompletos, concatena com os novos dados
            if (incompleteData) {
                data = incompleteData + data;
                incompleteData = '';
            }

            // Verifica se o JSON está completo
            try {
                const jsonData = JSON.parse(data);
                if (currentEvent) {
                    const contentArray = jsonData.delta?.content || [];
                    for (let content of contentArray) {
                        if (content.type === 'text' && content.text?.value) {
                            events.push(content.text.value);
                        }
                    }
                }
                currentEvent = null; // Reseta o evento após processar
                continue;
            } catch (error) {
                // Se o JSON está incompleto, armazena o fragmento
                incompleteData = data;
                continue;
            }
        }

        // if got here, probably it's an incomplete event, but check it anyway
        incompleteEvent = line;
    }

    return events;
}

var api_url = 'http://localhost:8000/api/';
var msg_body;
var msg_area;
var chat_cards;
var chat_name;
var chat_num_msgs;
var loading_text = false;

function cleanMsgBody() {
    msg_area.value = '';
    msg_body.innerHTML = 
        `<div class="d-flex justify-content-center align-items-center h-100">
            <img class="img-fluid h-100" src="img/logo_cyan.png">
        </div>`;

    if($('#msg_body_footer')[0].classList.length == 1)
        $('#msg_body_footer')[0].classList.add('move-down');

    chat_name.innerHTML = '';
    chat_num_msgs.innerHTML = '';
}

async function addThread() {
    const token = readCookie('token');
    
    let first_message = $('#add_thread_msg_id')[0].value;
    let new_chat_name = $('#add_thread_name_id')[0].value;
    $('#add_thread_msg_id')[0].value = $('#add_thread_name_id')[0].value = '';
    $('#add_thread_modal_id').modal('hide');
    disableButtons();

    if($('#msg_body_footer')[0].classList[1] == 'move-down')
        $('#msg_body_footer')[0].classList.remove('move-down');

    cleanDOM(msg_body);
    chat_name.innerHTML = new_chat_name;
    chat_num_msgs.innerHTML = '1 mensagem';
    attachDOM(msg_body, bubble_user);
    addTextLastBubble(first_message);

    chat_cards.insertBefore($(chat_card)[0], chat_cards.children[1]);

    if(selected_thread != -1) {
        let len = chat_cards.children.length - 1;
        chat_cards.children[len - selected_thread].classList.remove('active-card');
    }

    selected_thread = chat_cards.children.length - 2;
    
    let messageTemp = '';

    if(new_chat_name.length == 0) {
        if(first_message.length > 25) {
            messageTemp = first_message.slice(0, 22).trim();
            messageTemp += '...';
        } else 
            messageTemp = first_message; 

        chat_name.innerHTML = messageTemp;
        chat_cards.children[1].children[0].children[0].children[0].innerHTML = messageTemp;
    } else
        chat_cards.children[1].children[0].children[0].children[0].innerHTML = new_chat_name

    chat_cards.children[1].setAttribute('data-api-index', chat_cards.children.length - 2);
    chat_cards.children[1].addEventListener('click', event => {

        if(event.target == event.currentTarget.children[0].children[1].children[0]
            || event.target == event.currentTarget.children[0].children[1])
            return;

        let btn = event.currentTarget;
        let index = parseInt(btn.getAttribute('data-api-index'));
        let thread = parseInt(btn.getAttribute('data-api-thread'));
        btn.classList.add('active-card');

        if(selected_thread != -1) {
            let len = chat_cards.children.length - 1;
            chat_cards.children[len - selected_thread].classList.remove('active-card');
        }

        selected_thread = index;
        current_thread = thread;
        buildChat();
    });

    attachDOM(msg_body, bubble_sys);
    loading_text = true;
    loadingTextLastBubble();

    try {
        const response = await fetch(api_url + 'chat/create-run/' + account_id, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                text: first_message,
                main_brand_id: 1,
                name: new_chat_name
            })
        });

        if (!response.body) {
            throw new Error('A resposta não contém uma stream legível.');
        }
        
        if (response.status != 200) {
            throw new Error('ERRO: não foi possível receber uma responsta do servidor');
        }

        const reader = response.body.getReader();
        const decoder = new TextDecoder();

        chat_num_msgs.innerHTML = '2 mensagens';

        const processChunk = async () => {
            incompleteData = '';
            currentEvent = null;
            loading_text = false;
            let text = '';
            
            while (true) {
                
                const { done, value } = await reader.read();
                if (done) {
                    break;
                }

                // Decodificar o chunk e atualizar o DOM
                const chunk = decoder.decode(value, { stream: true });
                console.log(chunk);
                let arr = processString(chunk);
                if(arr.length > 0)
                    loading_text = false;

                arr.forEach((e) => {
                    text += e;
                    setTextLastBubble(text);
                });
                
            }

            chat_cards.children[1].children[0].children[0].children[1].innerHTML = `chat: ${current_thread} - 2 mensagens`;
            chat_cards.children[1].setAttribute('data-api-thread', current_thread);
            thread_ids.push(current_thread);
            thread_names.push(new_chat_name);

            let json = [];

            let msg00 = new Object();
            msg00.who = 'user';
            msg00.text = first_message;
            
            let msg01 = new Object();
            msg01.who = 'assistant';
            msg01.text = text;

            json.push(msg00);
            json.push(msg01);

            thread_text.push(JSON.stringify(json));
            enableButtons();
        }

        processChunk();
    } catch(e) {
        console.error('Erro ao processar o stream:', e);
    }
}

async function sendMsgThread() {
    const token = readCookie('token');

    disableButtons();

    let message = msg_area.value;
    msg_area.value = '';

    attachDOM(msg_body, bubble_user);
    addTextLastBubble(message);
    attachDOM(msg_body, bubble_sys);

    loading_text = true;
    loadingTextLastBubble();

    try {
        const response = await fetch(api_url + `chat/add/text/${current_thread}/${account_id}`, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                text: message,
            })
        });

        if (!response.body) {
            throw new Error('A resposta não contém um stream legível.');
        }

        if (response.status != 200) {
            throw new Error('ERRO: não foi possível receber uma resposta do servidor');
        }

        const reader = response.body.getReader();
        const decoder = new TextDecoder();

        const processChunk = async () => {
            incompleteData = '';
            currentEvent = null;
            let text = '';
            
            while (true) {
                const { done, value } = await reader.read();
                if (done) {
                    break;
                }

                // Decodificar o chunk e atualizar o DOM
                const chunk = decoder.decode(value, { stream: true });
                let arr = processString(chunk);
                if(arr.length > 0)
                    loading_text = false;

                console.log(chunk);
                arr.forEach((e) => {
                    text += e;
                    setTextLastBubble(text);
                });
                
            }

            let json = JSON.parse(thread_text[selected_thread]);

            let msg00 = new Object();
            msg00.who = 'user';
            msg00.text = message;
            
            let msg01 = new Object();
            msg01.who = 'assistant';
            msg01.text = text;

            json.push(msg00);
            json.push(msg01);
            chat_num_msgs.innerHTML = `${json.length} mensagens`;

            if(selected_thread != -1) {
                let len = chat_cards.children.length - 1;
                chat_cards.children[len - selected_thread].children[0].children[0].children[1].innerHTML = `chat: ${current_thread} - ${json.length} mensagens`;
            }

            thread_text[selected_thread] = JSON.stringify(json);

            enableButtons();
        }

        processChunk();
    } catch(e) {
        console.error('Erro ao processar o stream:', e);
    }
}

var thread_ids = [];
var thread_text = [];
var thread_names = [];
var selected_thread = -1;

async function listChats() {
    const token = readCookie('token');
    
    chat_cards.innerHTML = add_chat;
    thread_ids.length = thread_text.length = 0;

    try {
        const response = await window.axios.get(api_url + `chat/list/${main_brand_id}/${account_id}`, {
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        if (response.status != 200) {
            throw new Error(response.body + ` code: ${response.status}`);
        }

        let data = response.data;

        data.forEach((e, i) => {
            thread_ids.push(e.thread_id);
            thread_text.push(e.text);
            thread_names.push(e.name);

            let text = JSON.parse(e.text);

            let len = chat_cards.children.length - 1;

            if(len > 0) {
                chat_cards.insertBefore($(chat_card)[0], chat_cards.children[1]);
            } else chat_cards.innerHTML += chat_card;

            let messageTemp = '';

            if(e.name == null || (e.name != null && e.name.length == 0)) {
                if(text[0].text.length > 25) {
                    messageTemp = text[0].text.slice(0, 22).trim();
                    messageTemp += '...';
                } else 
                    messageTemp = text[0].text; 

                chat_cards.children[1].children[0].children[0].children[0].innerHTML = messageTemp;
            } else
                chat_cards.children[1].children[0].children[0].children[0].innerHTML = e.name;
           
            chat_cards.children[1].children[0].children[0].children[1].innerHTML = `chat: ${e.id} - ${JSON.parse(e.text).length} mensagens`;
            chat_cards.children[1].classList.remove('active-card');
            chat_cards.children[1].setAttribute('data-api-index', i);
            chat_cards.children[1].setAttribute('data-api-thread', e.id);
            chat_cards.children[1].addEventListener('click', event => {

                if(event.target == event.currentTarget.children[0].children[1].children[0]
                    || event.target == event.currentTarget.children[0].children[1]) {

                    menu_chat = parseInt(event.currentTarget.getAttribute('data-api-thread'));
                    menu_chat_index = parseInt(event.currentTarget.getAttribute('data-api-index'));
                    return;
                }

                // Rename
                if(event.target == event.currentTarget.children[0].children[1].children[1].children[0] ||
                    event.target == event.currentTarget.children[0].children[1].children[1].children[0].children[0])
                    return;

                // Delete
                if(event.target == event.currentTarget.children[0].children[1].children[1].children[1] ||
                    event.target == event.currentTarget.children[0].children[1].children[1].children[1].children[0])
                    return;

                if($('#msg_body_footer')[0].classList[1] == 'move-down')
                    $('#msg_body_footer')[0].classList.remove('move-down');

                let btn = event.currentTarget;
                let index = parseInt(btn.getAttribute('data-api-index'));
                let thread = parseInt(btn.getAttribute('data-api-thread'));
                btn.classList.add('active-card');

                if(selected_thread != -1) {
                    let len = chat_cards.children.length - 1;
                    chat_cards.children[len - selected_thread].classList.remove('active-card');
                }

                selected_thread = index;
                current_thread = thread;
                buildChat();
            });
        });
    } catch(e) {
        console.error('Erro ao processar requisição de chats:', e);
    }
}

async function buildChat() {
    if(current_thread == -1)
        throw new Error('ERRO: nenhuma thread ativa');

    cleanDOM(msg_body);

    if(thread_names[selected_thread] == null || (thread_names[selected_thread] != null && thread_names[selected_thread].length == 0)) {
        let len = chat_cards.children.length - 1;
        chat_name.innerHTML = chat_cards.children[len - selected_thread].children[0].children[0].children[0].innerHTML;
    } else
        chat_name.innerHTML = thread_names[selected_thread];


    let json = JSON.parse(thread_text[selected_thread]);
    chat_num_msgs.innerHTML = `${json.length} mensagens`;

    json.forEach(e => {
        if(e.who == 'user')
            attachDOM(msg_body, bubble_user);
        else
            attachDOM(msg_body, bubble_sys);

        addTextLastBubble(e.text);
    });
}

function listBrands() {
    const token = readCookie('token');

    window.axios.get(api_url + `account/list/main-brands/${account_id}`, {
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

        const brands = response.data;

        brands.forEach((e, i) => {
            let selected = main_brand_id == e.id ? 'selected' : '';
            list.innerHTML += `<option value=${e.id} ${selected}>${e.name}</option>`;
        });
    }).catch(e => {
        console.error('Erro ao renomear o chat:', e);
    });
}

var menu_chat = -1;
var menu_chat_index = -1;

function renameChat() {
    const token = readCookie('token');
    let new_chat_name = $('#rename_chat_name_id')[0].value;
    window.axios.patch(api_url + `chat/update/${menu_chat}/${account_id}`, {
        name: new_chat_name
    },{
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    }).then(response => {

        if (response.status != 200) {
            throw new Error(response.body + ` code: ${response.status}`);
        }

        thread_names[menu_chat_index] = new_chat_name;

        let len = chat_cards.children.length - 1;
        chat_cards.children[len - menu_chat_index].children[0].children[0].children[0].innerHTML = new_chat_name;

        if(selected_thread == menu_chat_index)
            chat_name.innerHTML = new_chat_name;

    }).catch(e => {
        console.error('Erro ao renomear o chat:', e);
    });
}

function deleteChat() {
    const token = readCookie('token');
    window.axios.delete(api_url + `chat/delete/${menu_chat}/${account_id}`, {
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    }).then(response => {

        if (response.status != 200) {
            throw new Error(response.body + ` code: ${response.status}`);
        }

        thread_names[menu_chat_index] = null;

        let len = chat_cards.children.length - 1;
        chat_cards.children[len - menu_chat_index].style.display = "none";

        if(selected_thread == menu_chat_index) {
            cleanMsgBody();
        }

    }).catch(e => {
        console.error('Erro ao deletar o chat:', e);
    });
}

function switchBrand() {
    main_brand_id = $('#brand_select_id')[0].value;

    $('#switch_modal_id').modal('hide');
    cleanMsgBody();
    listChats();
}

const bubble_sys = 
    `<div class="d-flex justify-content-start mb-4">
        <div class="msg_cotainer msg_bubble_sys">
            <span></span>
        </div>
    </div>`;

const bubble_user =
    `<div class="d-flex justify-content-end mb-4">
        <div class="msg_cotainer_send msg_bubble_user">
            <span></span>
        </div>
    </div>`;

const chat_card = 
    `<li class="chat_btn active-card">
        <div class="d-flex justify-content-between bd-highlight btn w-100 text-start">
            <div class="user_info">
                <span></span>
                <p></p>
            </div>
            <span id="btn-group dropend">
                <i class="fas fa-ellipsis-v btn btn-dots" data-bs-toggle="dropdown"></i>
                <ul class="dropdown-menu">
                    <li data-bs-toggle="modal" data-bs-target="#rename_modal_id"><i class="fas fa-pen"></i> Renomear</li>
                    <li data-bs-toggle="modal" data-bs-target="#delete_modal_id"><i class="fas fa-trash-can-xmark"></i> Deletar</li>
                </ul>
            </span>
        </div>
    </li>`;

const add_chat = 
    `<li class=" d-flex flex-column justify-content-center align-items-center" style="margin-bottom: 15px !important">
        <button class="d-flex justify-content-center text-black rounded-pill btn-tertiary" style="width: 90% !important; border: 0px;" data-bs-toggle="modal" data-bs-target="#add_thread_modal_id">
            <span style="padding: 5px; border-radius: 10px;">
                <i class="fa-solid fa-plus"></i>
            </span>
        </button>
    </li>`;

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
    let len = msg_body.children.length - 1;
    let s = msg_body.children[len].children[0].children[0];
    s.innerHTML += marked.parse(text); 
    msg_body.scrollTop = msg_body.scrollHeight
}

function setTextLastBubble(text) {
    let len = msg_body.children.length - 1;
    let s = msg_body.children[len].children[0].children[0];
    s.innerHTML = marked.parse(text); 
    msg_body.scrollTop = msg_body.scrollHeight
}

async function loadingTextLastBubble() {
    let len = msg_body.children.length - 1;
    let s = msg_body.children[len].children[0].children[0];
    s.innerHTML = `<div class="spinner-border text-white" role="status"></div>`;
} 

function disableButtons() {
    $('#send_btn_id')[0].classList.add('disabled_btn');
    $('#add_thread_btn_id')[0].classList.add('disabled_btn');
    msg_area.classList.add('disabled_btn');
    chat_cards.classList.add('disabled_btn');

    Array.from(chat_cards.children).forEach((e, i) => {
        if(i == 0)
            return;

        e.children[0].children[1].children[0].classList.add('disabled_btn');
    });
}

function enableButtons() {
    //$('#send_btn_id')[0].classList.remove('disabled_btn');
    $('#add_thread_btn_id')[0].classList.remove('disabled_btn');
    msg_area.classList.remove('disabled_btn');
    chat_cards.classList.remove('disabled_btn');

    Array.from(chat_cards.children).forEach((e, i) => {
        if(i == 0)
            return;
        
        e.children[0].children[1].children[0].classList.remove('disabled_btn');
    });
}

$(document).ready(function(){
    $('#action_menu_btn').click(function(){
        $('.action_menu').toggle();
    });

    $('#add_thread_btn_id').click(addThread);
    $('#send_btn_id').click(sendMsgThread);
    $('#rename_chat_btn_id').click(renameChat);
    $('#delete_chat_btn_id').click(deleteChat);
    $('#switch_brand_btn_id').click(switchBrand);

    msg_body = $('#msg_card_body_id')[0];
    msg_area = $('#msg_area_id')[0];
    chat_cards = $('#chat_cards_id')[0];
    chat_name = $('#chat_name_id')[0];
    chat_num_msgs = $('#chat_num_msgs_id')[0];

    msg_area.addEventListener('input', event => {
        let dom = event.target;

        if(dom.value.length > 0)
            $('#send_btn_id')[0].classList.remove('disabled_btn');
        else
            $('#send_btn_id')[0].classList.add('disabled_btn');
    });

    listChats();
    listBrands();
});


