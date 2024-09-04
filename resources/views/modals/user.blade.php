<!-- Modal create user -->
<div class="modal fade" id="create_user_modal_id" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="backdrop-filter: blur(25px);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-default">
        <div class="modal-header">
            <h5 class="modal-title text-white" id="staticBackdropLabel">Crie um novo usuário</h5>
        </div>
        <div class="modal-body">
            <form>
                <input type="name" autocomplete="name" class="form-control type_msg mb-3" id="new_user_name_id" placeholder="Nome" style="border-radius: 15px">
                <input type="email" autocomplete="email" class="form-control type_msg mb-3" id="new_user_email_id" placeholder="Email" style="border-radius: 15px">
                <input type="password" autocomplete="password" class="form-control type_msg mb-3" id="new_user_password_id" placeholder="Senha" style="border-radius: 15px">
                <input type="password" autocomplete="password" class="form-control type_msg mb-3" id="new_user_confirm_password_id" placeholder="Confirme a senha" style="border-radius: 15px">
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4 cancel-btn" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-basic btn-primary rounded-pill px-4" id="create_user_btn_id">
                <span><i class="fa-solid fa-user-plus"></i></span>
            </button>
        </div>
        </div>
    </div>
</div>