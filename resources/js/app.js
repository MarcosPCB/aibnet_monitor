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

function cleanMsgArea() {
    msg_area.value = '';
}

async function addThread() {
    const token = readCookie('token');
    
    let first_message = $('#add_thread_msg_id')[0].value;
    $('#add_thread_msg_id')[0].value = '';
    $('#add_thread_modal_id').modal('hide');

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
        }

        processChunk();
    } catch(e) {
        console.error('Erro ao processar o stream:', e);
    }
}

async function sendMsgThread() {
    const token = readCookie('token');
    
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
        }

        processChunk();
    } catch(e) {
        console.error('Erro ao processar o stream:', e);
    }
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

const chats_card = 
    `li class="active">
        <div class="d-flex bd-highlight">
            <div class="user_info">
                <span></span>
            </div>
        </div>
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

$(document).ready(function(){
    $('#action_menu_btn').click(function(){
        $('.action_menu').toggle();
    });

    $('#add_thread_btn_id').click(addThread);
    $('#send_btn_id').click(sendMsgThread);

    msg_body = $('#msg_card_body_id')[0];
    msg_area = $('#msg_area_id')[0];
});


