<!-- Modal switch brand -->
<div class="modal fade" id="switch_modal_id" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-default">
        <div class="modal-header">
            <h5 class="modal-title text-white" id="staticBackdropLabel">Trocar de cliente</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <select class="form-select type_msg" style="border-radius: 15px" id="brand_select_id">
            </select>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-basic btn-primary rounded-pill px-4" id="switch_brand_btn_id">
                <span><i class="fa-solid fa-arrow-right-arrow-left"></i></span>
            </button>
        </div>
        </div>
    </div>
</div>

<!-- Modal create main brand -->
<div class="modal fade" id="create_main_brand_modal_id" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="backdrop-filter: blur(25px);">
    <div class="modal-dialog modal-dialog-centered" style="min-width: 50vw;">
        <div class="modal-content bg-default">
        <div class="modal-header">
            <h5 class="modal-title text-white" id="staticBackdropLabel">Crie um novo cliente</h5>
        </div>
        <div class="modal-body" style="max-height: 75vh; overflow-y: auto">
            <form>
                <div class="form-floating">
                    <input type="name" autocomplete="name" class="form-control type_msg mb-3" id="new_main_brand_name_id" placeholder="Nome do cliente" style="border-radius: 15px">
                    <label for="new_main_brand_name_id">Nome do cliente</label>
                </div>

                <div class="d-flex align-items-center mb-3">
                    <div class="form-floating me-3 w-100">
                        <select class="form-select type_msg w-100 list-brands" style="border-radius: 15px" id="new_main_brand_primary_id">
                        </select>
                        <label for="new_main_brand_primary_id">Marca primária</label>
                    </div>
                    <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4 me-2" id="edit_primary_brand_btn_id"><span><i class="fa-solid fa-file-pen"></i></span></button>
                    <button type="button" class="btn btn-basic btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#create_brand_modal_id" data-bs-dismiss="modal"><span><i class="fa-solid fa-circle-plus"></i></span></button>
                </div>

                <div class="d-flex align-items-center mb-3">
                    <div class="form-floating me-3 w-100">
                        <select class="form-select type_msg list-brands-opponents" style="border-radius: 15px" id="new_main_brand_opponent_1_id">
                            <option value="-1">Nenhum</option>
                        </select>
                        <label for="new_main_brand_opponent_1_id">Marca concorrente 1</label>
                    </div>
                    <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4" id="edit_opponent_1_brand_btn_id"><span><i class="fa-solid fa-file-pen"></i></span></button>
                </div>

                <div class="d-flex align-items-center mb-3">
                    <div class="form-floating me-3 w-100">
                        <select class="form-select type_msg list-brands-opponents" style="border-radius: 15px" id="new_main_brand_opponent_2_id">
                            <option value="-1">Nenhum</option>
                        </select>
                        <label for="new_main_brand_opponent_2_id">Marca concorrente 2</label>
                    </div>
                    <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4" id="edit_opponent_2_brand_btn_id"><span><i class="fa-solid fa-file-pen"></i></span></button>
                </div>

                <div class="form-floating">
                    <input type="text" class="form-control type_msg mb-3" id="new_main_brand_model_id" value="" style="border-radius: 15px">
                    <label for="new_main_brand_model_id">ID do assistente (OpenAI GPT Assistant - pode deixar em branco, qualquer coisa)</label>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4 cancel-btn" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-basic btn-primary rounded-pill px-4" id="create_main_brand_btn_id">
                <span><i class="fa-solid fa-folder-plus"></i></span>
            </button>
        </div>
        </div>
    </div>
</div>

<!-- Modal edit main brand -->
<div class="modal fade" id="edit_main_brand_modal_id" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="backdrop-filter: blur(25px);">
    <div class="modal-dialog modal-dialog-centered" style="min-width: 50vw;">
        <div class="modal-content bg-default">
        <div class="modal-header">
            <h5 class="modal-title text-white" id="staticBackdropLabel">Edite o cliente</h5>
        </div>
        <div class="modal-body" style="max-height: 75vh; overflow-y: auto">
            <form>
                <div class="form-floating">
                    <input type="name" autocomplete="name" class="form-control type_msg mb-3" id="edit_main_brand_name_id" placeholder="Nome do cliente" style="border-radius: 15px">
                    <label for="edit_main_brand_name_id">Nome do cliente</label>
                </div>

                <div class="d-flex flex-column">
                    <ul class="list-group mb-3" style="max-height: 320px" id="edit_main_brand_primary_id">
                        
                    </ul>
                </div>

                 <div class="d-flex flex-column">
                    <ul class="list-group mb-3" style="max-height: 320px" id="edit_main_brand_opponents_id">
                        
                    </ul>
                    <button type="button" class="btn btn-basic btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#add_main_brand_opponent_id" data-bs-dismiss="modal"><span><i class="fa-solid fa-circle-plus"></i></span></button>
                </div>

                <div class="form-floating">
                    <input type="text" class="form-control type_msg mb-3" id="edit_main_brand_model_id" value="" style="border-radius: 15px">
                    <label for="edit_main_brand_model_id">ID do assistente (OpenAI GPT Assistant - pode deixar em branco, qualquer coisa)</label>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4 cancel-btn" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-basic btn-primary rounded-pill px-4" id="edit_main_brand_btn_id">
                <span><i class="fa-solid fa-folder-plus"></i></span>
            </button>
        </div>
        </div>
    </div>
</div>

<!-- Modal select edit brand -->
<div class="modal fade" id="select_edit_brand_modal_id" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-default">
        <div class="modal-header">
            <h5 class="modal-title text-white" id="staticBackdropLabel">Selecione a marca</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <select class="form-select type_msg list-brands" style="border-radius: 15px" id="select_edit_brand_id">
            </select>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-basic btn-primary rounded-pill px-4" id="edit_select_brand_btn_id">
                <span><i class="fa-solid fa-arrow-right"></i></span>
            </button>
        </div>
        </div>
    </div>
</div>
