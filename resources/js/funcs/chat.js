require('../bootstrap');
const { readCookie, cleanDOM, attachDOM, addTextLastBubble, loadingTextLastBubble, setTextLastBubble, checkAuth, enableButtons, disableButtons, cleanMsgBody, appLoad } = require('../utils');
const globals = require('../globals');

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
  
    for (const event of events) {
      if (inputString.includes(event)) {
        return event;
      }
    }
  
    return "incomplete";
}

function processString(chunk) {
    const lines = chunk.split('\n');
    const events = [];

    for (let line of lines) {
        line = line.trim();
        if (!line) continue;

        // Verifica se estamos processando um dado incompleto
        if (globals.incompleteData) {
            globals.incompleteData += line;
            try {
                const jsonData = JSON.parse(globals.incompleteData);
                if (globals.currentEvent) {
                    const contentArray = jsonData.delta?.content || [];
                    for (let content of contentArray) {
                        if (content.type === 'text' && content.text?.value) {
                            events.push(content.text.value);
                        }
                    }
                }
                globals.incompleteData = ''; // Limpa o estado após processar o dado completo
                globals.currentEvent = null;
                continue;
            } catch (error) {
                // Continua acumulando dados se o JSON ainda estiver incompleto
                continue;
            }
        }

        if (globals.incompleteEvent) {
            globals.incompleteEvent += line;
            if (globals.incompleteEvent.startsWith('event:')) {
                globals.currentEvent = globals.incompleteEvent.slice(6).trim();
                if (globals.currentEvent !== "thread.message.delta") {
                    globals.currentEvent = null;
                    globals.incompleteEvent = '';
                    continue;
                }
                globals.incompleteEvent = '';
                continue;
            }
        }

        // Identifica a ID da thread
        if (line.startsWith('API_THREAD_ID:')) {
            let data = line.slice(14).trim();
            globals.current_thread = parseInt(data.slice(0, data.indexOf(';')));
            continue;
        }

        // Identifica eventos
        if (line.startsWith('event:')) {
            globals.currentEvent = line.slice(6).trim();

            let event = identifyEvent(globals.currentEvent);
            if (event != "thread.message.delta") {
                if (event == 'incomplete')
                    globals.incompleteEvent = line;

                globals.currentEvent = null;
                continue;
            }
            continue;
        }

        // Identifica dados
        if (line.startsWith('data:')) {
            let data = line.slice(5).trim();

            // Se há dados incompletos, concatena com os novos dados
            if (globals.incompleteData) {
                data = globals.incompleteData + data;
                globals.incompleteData = '';
            }

            // Verifica se o JSON está completo
            try {
                const jsonData = JSON.parse(data);
                if (globals.currentEvent) {
                    const contentArray = jsonData.delta?.content || [];
                    for (let content of contentArray) {
                        if (content.type === 'image_file') {
                            events.push(`![image](/storage/loading.gif)`);
                        } else if (content.type === 'text' && content.text?.value) {
                            events.push(content.text.value);
                        }
                    }
                }
                globals.currentEvent = null; // Reseta o evento após processar
                continue;
            } catch (error) {
                // Se o JSON está incompleto, armazena o fragmento
                globals.incompleteData = data;
                continue;
            }
        }

        // if got here, probably it's an incomplete event, but check it anyway
        globals.incompleteEvent = line;
    }

    return events;
}

async function addThread() {
    const token = readCookie('token');
    
    let first_message = $('#add_thread_msg_id')[0].value;
    let new_chat_name = $('#add_thread_name_id')[0].value;
    $('#add_thread_msg_id')[0].value = $('#add_thread_name_id')[0].value = '';
    $('#add_thread_modal_id').modal('hide');
    disableButtons();

    if($('#msg_body_footer')[0].classList[1] === 'move-down') {
        $('#msg_body_footer')[0].classList.remove('move-down');
    }

    cleanDOM(globals.msg_body);
    globals.chat_name.innerHTML = new_chat_name;
    globals.chat_num_msgs.innerHTML = '1 mensagem';
    attachDOM(globals.msg_body, globals.bubble_user);
    addTextLastBubble(first_message);

    globals.chat_cards.insertBefore($(globals.chat_card)[0], globals.chat_cards.children[1]);

    if(globals.selected_thread !== -1) {
        let len = globals.chat_cards.children.length - 1;
        globals.chat_cards.children[len - globals.selected_thread].classList.remove('active-card');
    }

    globals.selected_thread = globals.chat_cards.children.length - 2;
    
    let messageTemp = '';

    if(new_chat_name.length === 0) {
        if(first_message.length > 25) {
            messageTemp = first_message.slice(0, 22).trim();
            messageTemp += '...';
        } else {
            messageTemp = first_message;
        }

        globals.chat_name.innerHTML = messageTemp;
        globals.chat_cards.children[1].children[0].children[0].children[0].innerHTML = messageTemp;
    } else {
        globals.chat_cards.children[1].children[0].children[0].children[0].innerHTML = new_chat_name;
    }

    globals.chat_cards.children[1].setAttribute('data-api-index', globals.chat_cards.children.length - 2);
    globals.chat_cards.children[1].addEventListener('click', event => {
        if(event.target == event.currentTarget.children[0].children[1].children[0]
            || event.target == event.currentTarget.children[0].children[1]) {

            globals.menu_chat = parseInt(event.currentTarget.getAttribute('data-api-thread'));
            globals.menu_chat_index = parseInt(event.currentTarget.getAttribute('data-api-index'));
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

        let btn = event.currentTarget;
        let index = parseInt(btn.getAttribute('data-api-index'));
        let thread = parseInt(btn.getAttribute('data-api-thread'));
        btn.classList.add('active-card');

        if(globals.selected_thread !== -1) {
            let len = globals.chat_cards.children.length - 1;
            globals.chat_cards.children[len - globals.selected_thread].classList.remove('active-card');
        }

        globals.selected_thread = index;
        globals.current_thread = thread;
        buildChat();
    });

    attachDOM(globals.msg_body, globals.bubble_sys);
    globals.loading_text = true;
    loadingTextLastBubble();
    globals.msg_body.scrollTop = globals.msg_body.scrollHeight;

    try {
        const response = await fetch(globals.api_url + 'chat/create-run/' + globals.account_id, {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                text: first_message,
                main_brand_id: globals.main_brand_id,
                name: new_chat_name
            })
        });

        if (!response.body) {
            throw new Error('A resposta não contém uma stream legível.');
        }
        
        if (response.status !== 200) {
            throw new Error('ERRO: não foi possível receber uma resposta do servidor');
        }

        const reader = response.body.getReader();
        const decoder = new TextDecoder();

        globals.chat_num_msgs.innerHTML = '2 mensagens';

        const processChunk = async () => {
            globals.incompleteData = '';
            globals.currentEvent = null;
            globals.loading_text = false;
            let text = '';
            
            while (true) {
                
                const { done, value } = await reader.read();
                if (done) {
                    break;
                }

                // Decodificar o chunk e atualizar o DOM
                const chunk = decoder.decode(value, { stream: true });
                let arr = processString(chunk);
                if(arr.length > 0) {
                    globals.loading_text = false;
                }

                arr.forEach((e) => {
                    text += e;
                    setTextLastBubble(text);
                });
                
            }

            globals.chat_cards.children[1].children[0].children[0].children[1].innerHTML = `chat: ${globals.current_thread} - 2 mensagens`;
            globals.chat_cards.children[1].setAttribute('data-api-thread', globals.current_thread);
            globals.thread_ids.push(globals.current_thread);
            globals.thread_names.push(new_chat_name);

            let json = [];

            let msg00 = { who: 'user', text: first_message };
            let msg01 = { who: 'assistant', text: text };

            json.push(msg00);
            json.push(msg01);

            globals.thread_text.push(JSON.stringify(json));
            enableButtons();
        }

        processChunk();
    } catch(e) {
        console.error('Erro ao processar o stream:', e);
        checkAuth(e.response);
    }
}

async function sendMsgThread() {
    const token = readCookie('token');

    disableButtons();

    let message = globals.msg_area.value;
    globals.msg_area.value = '';

    attachDOM(globals.msg_body, globals.bubble_user);
    addTextLastBubble(message);
    attachDOM(globals.msg_body, globals.bubble_sys);

    globals.loading_text = true;
    loadingTextLastBubble();
    globals.msg_body.scrollTop = globals.msg_body.scrollHeight;

    try {
        const response = await fetch(globals.api_url + `chat/add/text/${globals.current_thread}/${globals.account_id}`, {
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

        if (response.status !== 200) {
            throw new Error('ERRO: não foi possível receber uma resposta do servidor');
        }

        const reader = response.body.getReader();
        const decoder = new TextDecoder();

        const processChunk = async () => {
            globals.incompleteData = '';
            globals.currentEvent = null;
            let text = '';
            
            while (true) {
                const { done, value } = await reader.read();
                if (done) {
                    break;
                }

                // Decodificar o chunk e atualizar o DOM
                const chunk = decoder.decode(value, { stream: true });
                let arr = processString(chunk);
                if(arr.length > 0) {
                    globals.loading_text = false;
                }

                arr.forEach((e) => {
                    text += e;
                    setTextLastBubble(text);
                });
                
            }

            let json = JSON.parse(globals.thread_text[globals.selected_thread]);

            let msg00 = { who: 'user', text: message };
            let msg01 = { who: 'assistant', text: text };

            json.push(msg00);
            json.push(msg01);
            globals.chat_num_msgs.innerHTML = `${json.length} mensagens`;

            if(globals.selected_thread !== -1) {
                let len = globals.chat_cards.children.length - 1;
                globals.chat_cards.children[len - globals.selected_thread].children[0].children[0].children[1].innerHTML = `chat: ${globals.current_thread} - ${json.length} mensagens`;
            }

            globals.thread_text[globals.selected_thread] = JSON.stringify(json);

            try {
                const response = await window.axios.get(globals.api_url + `chat/get/${globals.current_thread}/${globals.account_id}`, {
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

                globals.thread_text[globals.selected_thread] = data.text;
                globals.msg_body.style.scrollBehavior = 'initial';
                buildChat();
            } catch(err) {
                console.log('Unable to fetch texts from API');
            }        

            enableButtons();
        }

        processChunk();
    } catch(e) {
        console.error('Erro ao processar o stream:', e);
        checkAuth(e.response);
    }
}

async function listChats() {
    const token = readCookie('token');
    
    globals.chat_cards.innerHTML = globals.add_chat;
    globals.thread_ids.length = globals.thread_text.length = 0;

    try {
        const response = await window.axios.get(globals.api_url + `chat/list/${globals.main_brand_id}/${globals.account_id}`, {
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

        for(let i = 0; i < data.length; i++) {
            e = data[i];

            globals.thread_ids.push(e.thread_id);
            globals.thread_text.push(e.text);
            globals.thread_names.push(e.name);

            let text = JSON.parse(e.text);

            let len = globals.chat_cards.children.length - 1;

            if(len > 0) {
                globals.chat_cards.insertBefore($(globals.chat_card)[0], globals.chat_cards.children[1]);
            } else {
                globals.chat_cards.innerHTML += globals.chat_card;
            }

            let messageTemp = '';

            if(e.name == null || (e.name != null && e.name.length == 0)) {
                if(text[0].text.length > 25) {
                    messageTemp = text[0].text.slice(0, 22).trim();
                    messageTemp += '...';
                } else {
                    messageTemp = text[0].text; 
                }

                globals.chat_cards.children[1].children[0].children[0].children[0].innerHTML = messageTemp;
            } else {
                globals.chat_cards.children[1].children[0].children[0].children[0].innerHTML = e.name;
            }
           
            globals.chat_cards.children[1].children[0].children[0].children[1].innerHTML = `chat: ${e.id} - ${JSON.parse(e.text).length} mensagens`;
            globals.chat_cards.children[1].classList.remove('active-card');
            globals.chat_cards.children[1].setAttribute('data-api-index', i);
            globals.chat_cards.children[1].setAttribute('data-api-thread', e.id);
            globals.chat_cards.children[1].addEventListener('click', event => {

                if(event.target == event.currentTarget.children[0].children[1].children[0]
                    || event.target == event.currentTarget.children[0].children[1]) {

                    globals.menu_chat = parseInt(event.currentTarget.getAttribute('data-api-thread'));
                    globals.menu_chat_index = parseInt(event.currentTarget.getAttribute('data-api-index'));
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

                if(globals.selected_thread != -1) {
                    let len = globals.chat_cards.children.length - 1;
                    globals.chat_cards.children[len - globals.selected_thread].classList.remove('active-card');
                }

                globals.selected_thread = index;
                globals.current_thread = thread;
                buildChat();
            });
        }

        appLoad();
    } catch(e) {
        console.error('Erro ao processar requisição de chats:', e);
        checkAuth(e.response);
        appLoad();
    }
}

async function buildChat() {
    if(globals.current_thread == -1)
        throw new Error('ERRO: nenhuma thread ativa');

    cleanDOM(globals.msg_body);

    if(globals.thread_names[globals.selected_thread] == null || (globals.thread_names[globals.selected_thread] != null && globals.thread_names[globals.selected_thread].length == 0)) {
        let len = globals.chat_cards.children.length - 1;
        globals.chat_name.innerHTML = globals.chat_cards.children[len - globals.selected_thread].children[0].children[0].children[0].innerHTML;
    } else {
        globals.chat_name.innerHTML = globals.thread_names[globals.selected_thread];
    }

    let json = JSON.parse(globals.thread_text[globals.selected_thread]);
    globals.chat_num_msgs.innerHTML = `${json.length} mensagens`;

    for(let i = 0; i < json.length; i++) {
        const e = json[i];

        if(e.who == 'user')
            attachDOM(globals.msg_body, globals.bubble_user);
        else
            attachDOM(globals.msg_body, globals.bubble_sys);

        addTextLastBubble(e.text);
    }

    globals.msg_body.scrollTop = globals.msg_body.scrollHeight;
    globals.msg_body.style.scrollBehavior = 'smooth';
}

function renameChat() {
    const token = readCookie('token');
    let new_chat_name = $('#rename_chat_name_id')[0].value;
    window.axios.patch(globals.api_url + `chat/update/${globals.menu_chat}/${globals.account_id}`, {
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

        globals.thread_names[globals.menu_chat_index] = new_chat_name;

        let len = globals.chat_cards.children.length - 1;
        globals.chat_cards.children[len - globals.menu_chat_index].children[0].children[0].children[0].innerHTML = new_chat_name;

        if(globals.selected_thread == globals.menu_chat_index)
            globals.chat_name.innerHTML = new_chat_name;

    }).catch(e => {
        console.error('Erro ao renomear o chat:', e);
        checkAuth(e.response);
    });
}

function deleteChat() {
    const token = readCookie('token');
    window.axios.delete(globals.api_url + `chat/delete/${globals.menu_chat}/${globals.account_id}`, {
        headers: {
            'Authorization': 'Bearer ' + token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    }).then(response => {

        if (response.status != 200) {
            throw new Error(response.body + ` code: ${response.status}`);
        }

        globals.thread_names[globals.menu_chat_index] = null;

        let len = globals.chat_cards.children.length - 1;
        globals.chat_cards.children[len - globals.menu_chat_index].style.display = "none";

        if(globals.selected_thread == globals.menu_chat_index) {
            cleanMsgBody();
        }

    }).catch(e => {
        console.error('Erro ao deletar o chat:', e);
        checkAuth(e.response);
    });
}

module.exports = {
    addThread,
    sendMsgThread,
    renameChat,
    deleteChat,
    listChats,
    buildChat
};