<!-- Modal add thread -->
<div class="modal fade" id="add_thread_modal_id" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-default">
        <div class="modal-header">
            <h5 class="modal-title text-white" id="staticBackdropLabel">Novo chat</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input type="text" class="form-control type_msg" id="add_thread_name_id" placeholder="Nome do chat..." style="border-radius: 15px"></input>
            <br>
            <textarea class="form-control type_msg" id="add_thread_msg_id" placeholder="Primeira mensagem..." style="border-radius: 15px"></textarea>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-basic btn-primary rounded-pill px-4" id="add_thread_btn_id">
                <span>
                    <i class="fa-solid fa-paper-plane-top"></i>
                </span>
            </button>
        </div>
        </div>
    </div>
</div>

<!-- Modal rename chat -->
<div class="modal fade" id="rename_modal_id" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-default">
        <div class="modal-header">
            <h5 class="modal-title text-white" id="staticBackdropLabel">Renomear chat</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input type="text" class="form-control type_msg" id="rename_chat_name_id" placeholder="Novo nome do chat..." style="border-radius: 15px"></input>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-basic btn-primary rounded-pill px-4" id="rename_chat_btn_id" data-bs-dismiss="modal"> Renomear </button>
        </div>
        </div>
    </div>
</div>

<!-- Modal delete chat -->
<div class="modal fade" id="delete_modal_id" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-default">
        <div class="modal-header">
            <h5 class="modal-title text-white" id="staticBackdropLabel">Deseja mesmo deletar o chat?</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Não</button>
            <button type="button" class="btn btn-basic btn-primary rounded-pill px-4" id="delete_chat_btn_id" data-bs-dismiss="modal">Sim</button>
        </div>
        </div>
    </div>
</div>