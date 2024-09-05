const globals = {
    loaded: 0,
    account_id: 1,
    main_brand_id: 1,
    user_id: 1,
    is_operator: true,
    backup: '',
    account_creation: false,
    brand_id: -1,
    platform_id: -1,
    main_brand_data: {
        brand_id: -1,
        chat_model: ''
    },
    selected_edit_brand: 0,
    login_flow: false,
    incompleteData: '',
    incompleteEvent: '',
    currentEvent: null,
    current_thread: -1,
    modalHistory: [],
    api_url: window.env.API_URL + '/api/',
    msg_body: undefined,
    msg_area: undefined,
    chat_cards: undefined,
    chat_name: undefined,
    chat_num_msgs: undefined,
    loading_text: false,
    thread_ids: [],
    thread_text: [],
    thread_names: [],
    selected_thread: -1,
    menu_chat: -1,
    menu_chat_index: -1,
    bubble_sys: `<div class="d-flex justify-content-start mb-4">
                    <div class="msg_cotainer msg_bubble_sys">
                        <span></span>
                    </div>
                </div>`,
    bubble_user: `<div class="d-flex justify-content-end mb-4">
                    <div class="msg_cotainer_send msg_bubble_user">
                        <span></span>
                    </div>
                </div>`,
    chat_card: `<li class="chat_btn active-card">
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
                </li>`,
    add_chat: `<li class="d-flex flex-column justify-content-center align-items-center" style="margin-bottom: 15px !important">
                    <button class="d-flex justify-content-center text-black rounded-pill btn-tertiary" style="width: 90% !important; border: 0px;" data-bs-toggle="modal" data-bs-target="#add_thread_modal_id">
                        <span style="padding: 5px; border-radius: 10px;">
                            <i class="fa-solid fa-plus"></i>
                        </span>
                    </button>
                </li>`
};

module.exports = globals;
