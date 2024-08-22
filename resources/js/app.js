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

let incompleteData = '';
let currentEvent = null;

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

async function addThread(first_message) {
    const token = readCookie('token');
    
    try {
        const response = await fetch(api_url + 'chat/create-run/1', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                text: first_message,
                main_brand_id: 1
            })
        });

        if (!response.body) {
            throw new Error('A resposta não contém um stream legível.');
        }

        const reader = response.body.getReader();
        const decoder = new TextDecoder();

        const processChunk = async () => {
            while (true) {
                
                const { done, value } = await reader.read();
                if (done) {
                    break;
                }

                // Decodificar o chunk e atualizar o DOM
                const chunk = decoder.decode(value, { stream: true });
                console.log(chunk);
                arr.forEach((e) => {
                    $('#chat_bubble_id')[0].innerHTML += e;
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
        <div class="msg_cotainer">
            <span></span>
        </div>
    </div>`;

const bubble_user =
    `<div class="d-flex justify-content-end mb-4">
        <div class="msg_cotainer_send">
            <span></span>
        </div>
    </div>`;

const chat_card = 
    `li class="active">
        <div class="d-flex bd-highlight">
            <div class="user_info">
                <span></span>
            </div>
        </div>
    </li>`;

$(document).ready(function(){
    $('#action_menu_btn').click(function(){
        $('.action_menu').toggle();
    });

    $('#add_thread_btn_id').click(addThread);
});


