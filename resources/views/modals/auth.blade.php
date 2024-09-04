<!-- Modal logout -->
<div class="modal fade" id="logout_modal_id" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-default">
        <div class="modal-header">
            <h5 class="modal-title text-white" id="staticBackdropLabel">Deseja mesmo deslogar?</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Não</button>
            <button type="button" class="btn btn-basic btn-primary rounded-pill px-4" id="logout_btn_id" data-bs-dismiss="modal">Sim</button>
        </div>
        </div>
    </div>
</div>

<!-- Modal login -->
<div class="modal fade" id="login_modal_id" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="backdrop-filter: blur(25px);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-default">
        <div class="modal-header">
            <h5 class="modal-title text-white" id="staticBackdropLabel">Faça login</h5>
        </div>
        <div class="modal-body">
            <form>
                <input type="email" autocomplete="email" class="form-control type_msg mb-3" id="email_id" placeholder="Email" style="border-radius: 15px">
                <input type="password" autocomplete="password" class="form-control type_msg mb-3" id="password_id" placeholder="Senha" style="border-radius: 15px">
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4">Esqueci a senha</button>
            <button type="button" class="btn btn-basic btn-primary rounded-pill px-4" id="login_btn_id">
                <span><i class="fa-solid fa-right-to-bracket"></i></span>
            </button>
        </div>
        </div>
    </div>
</div>