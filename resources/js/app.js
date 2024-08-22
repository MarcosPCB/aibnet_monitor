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

var incompleteData = '';
var currentEvent = null;
var current_thread = -1;

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
            } catch (error) {
                // Continua acumulando dados se o JSON ainda estiver incompleto
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
            if (currentEvent !== "thread.message.delta") {
                currentEvent = null;
                continue;
            }
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
            } catch (error) {
                // Se o JSON está incompleto, armazena o fragmento
                incompleteData = data;
            }
        }
    }

    return events;
}

var api_url = 'http://localhost:8000/api/';
var msg_body;
var msg_area;
var chat_cards;

function cleanMsgArea() {
    msg_area.value = '';
}

async function addThread() {
    const token = readCookie('token');
    
    let first_message = $('#add_thread_msg_id')[0].value;
    $('#add_thread_msg_id')[0].value = '';
    $('#add_thread_modal_id').modal('hide');
    disableButtons();

    try {
        const response = await fetch(api_url + 'chat/create-run/1', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                text: first_message,
                main_brand_id: 1
            })
        });

        if (!response.body) {
            throw new Error('A resposta não contém um stream legível.');
        }

        cleanDOM(msg_body);
        attachDOM(msg_body, bubble_user);
        addTextLastBubble(first_message);

        chat_cards.insertBefore($(chat_card)[0], chat_cards.children[1]);

        if(selected_thread != -1) {
            let len = chat_cards.children.length - 1;
            chat_cards.children[len - selected_thread].classList.remove('active');
        }

        selected_thread = chat_cards.children.length - 2;
        
        let messageTemp = '';

        if(first_message.length > 25) {
            messageTemp = first_message.slice(0, 22).trim();
            messageTemp += '...';
        } else 
            messageTemp = first_message; 

        chat_cards.children[1].children[0].children[0].children[0].innerHTML = messageTemp;
        chat_cards.children[1].setAttribute('data-api-index', chat_cards.children.length - 2);
        chat_cards.children[1].addEventListener('click', event => {
            let btn = event.currentTarget;
            let index = parseInt(btn.getAttribute('data-api-index'));
            let thread = parseInt(btn.getAttribute('data-api-thread'));
            btn.classList.add('active');

            if(selected_thread != -1) {
                let len = chat_cards.children.length - 1;
                chat_cards.children[len - selected_thread].classList.remove('active');
            }

            selected_thread = index;
            current_thread = thread;
            buildChat();
        });

        const reader = response.body.getReader();
        const decoder = new TextDecoder();

        const processChunk = async () => {
            attachDOM(msg_body, bubble_sys);
            incompleteData = '';
            currentEvent = null;
            
            while (true) {
                
                const { done, value } = await reader.read();
                if (done) {
                    break;
                }

                // Decodificar o chunk e atualizar o DOM
                const chunk = decoder.decode(value, { stream: true });
                let arr = processString(chunk);
                arr.forEach((e) => {
                    addTextLastBubble(e);
                });
                
            }

            chat_cards.children[1].children[0].children[0].children[1].innerHTML = `chat: ${current_thread}`;
            chat_cards.children[1].setAttribute('data-api-thread', current_thread);
            thread_ids.push(current_thread);

            let json = [];

            let msg00 = new Object();
            msg00.who = 'user';
            msg00.text = first_message;
            
            let msg01 = new Object();
            msg01.who = 'assistant';
            let len = msg_body.children.length - 1;
            let s = msg_body.children[len].children[0].children[0];

            msg01.text = s.innerHTML;

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

    try {
        const response = await fetch(api_url + `chat/add/text/${current_thread}/1`, {
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

        attachDOM(msg_body, bubble_user);
        addTextLastBubble(message);

        const reader = response.body.getReader();
        const decoder = new TextDecoder();

        const processChunk = async () => {
            attachDOM(msg_body, bubble_sys);
            incompleteData = '';
            currentEvent = null;
            
            while (true) {
                const { done, value } = await reader.read();
                if (done) {
                    break;
                }

                // Decodificar o chunk e atualizar o DOM
                const chunk = decoder.decode(value, { stream: true });
                let arr = processString(chunk);
                arr.forEach((e) => {
                    addTextLastBubble(e);
                });
                
            }

            enableButtons();
        }

        processChunk();
    } catch(e) {
        console.error('Erro ao processar o stream:', e);
    }
}

var thread_ids = [];
var thread_text = [];
var selected_thread = -1;

async function listChats() {
    const token = readCookie('token');
    
    chat_cards.innerHTML = add_chat;
    thread_ids.length = thread_text.length = 0;

    try {
        const response = await window.axios.get(api_url + `chat/list/1/1`, {
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

            let text = JSON.parse(e.text);

            let len = chat_cards.children.length - 1;

            if(len > 1) {
                chat_cards.insertBefore($(chat_card)[0], chat_cards.children[1]);
            } else chat_cards.innerHTML += chat_card;

            let messageTemp = '';

            if(text[0].text.length > 25) {
                messageTemp = text[0].text.slice(0, 22).trim();
                messageTemp += '...';
            } else 
                messageTemp = text[0].text; 

            chat_cards.children[1].children[0].children[0].children[0].innerHTML = messageTemp;
           
            chat_cards.children[1].children[0].children[0].children[1].innerHTML = `chat: ${e.id}`;
            chat_cards.children[1].classList.remove('active');
            chat_cards.children[1].setAttribute('data-api-index', i);
            chat_cards.children[1].setAttribute('data-api-thread', e.id);
            chat_cards.children[1].addEventListener('click', event => {
                let btn = event.currentTarget;
                let index = parseInt(btn.getAttribute('data-api-index'));
                let thread = parseInt(btn.getAttribute('data-api-thread'));
                btn.classList.add('active');

                if(selected_thread != -1) {
                    let len = chat_cards.children.length - 1;
                    chat_cards.children[len - selected_thread].classList.remove('active');
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

    let json = JSON.parse(thread_text[selected_thread]);

    json.forEach(e => {
        if(e.who == 'user')
            attachDOM(msg_body, bubble_user);
        else
            attachDOM(msg_body, bubble_sys);

        addTextLastBubble(e.text);
    });
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
    `<li class="border-top border-bottom border-dark active">
        <button class="d-flex bd-highlight btn w-100 text-start">
            <div class="user_info">
                <span></span>
                <p></p>
            </div>
        </button>
    </li>`;

const add_chat = 
    `<li class=" d-flex flex-column justify-content-center align-items-center">
        <button class="d-flex justify-content-center text-black bd-highlight rounded-pill bg-white" style="width: 90% !important;" data-bs-toggle="modal" data-bs-target="#add_thread_modal_id">
            <span style="padding: 5px; border-radius: 10px;">
                <i class="fa-solid fa-plus"></i>
            </span>
        </button>
    </li>`;

function cleanDOM(dom) {
    dom.innerHTML = '';
}

function attachDOM(dom, content) {
    dom.innerHTML += content;
}

function addTextLastBubble(text) {
    let len = msg_body.children.length - 1;
    let s = msg_body.children[len].children[0].children[0];
    s.innerHTML += text; 
}

function disableButtons() {
    $('#send_btn_id')[0].classList.add('disabled_btn');
    $('#add_thread_btn_id')[0].classList.add('disabled_btn');
    chat_cards.classList.add('disabled_btn');
}

function enableButtons() {
    //$('#send_btn_id')[0].classList.remove('disabled_btn');
    $('#add_thread_btn_id')[0].classList.remove('disabled_btn');
    chat_cards.classList.remove('disabled_btn');
}

$(document).ready(function(){
    $('#action_menu_btn').click(function(){
        $('.action_menu').toggle();
    });

    $('#add_thread_btn_id').click(addThread);
    $('#send_btn_id').click(sendMsgThread);

    msg_body = $('#msg_card_body_id')[0];
    msg_area = $('#msg_area_id')[0];
    chat_cards = $('#chat_cards_id')[0];

    msg_area.addEventListener('input', event => {
        let dom = event.target;

        if(dom.value.length > 0)
            $('#send_btn_id')[0].classList.remove('disabled_btn');
        else
            $('#send_btn_id')[0].classList.add('disabled_btn');
    });

    listChats();
});


