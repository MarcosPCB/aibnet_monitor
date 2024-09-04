<!-- Modal create brand -->
<div class="modal fade" id="create_brand_modal_id" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="backdrop-filter: blur(25px);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-default">
        <div class="modal-header">
            <h5 class="modal-title text-white" id="staticBackdropLabel">Crie uma nova marca</h5>
        </div>
        <div class="modal-body">
            <form>
                <div class="form-floating">
                    <input type="name" autocomplete="name" class="form-control type_msg mb-3" id="new_brand_name_id" placeholder="Nome da marca" style="border-radius: 15px">
                    <label for="new_brand_name_id">Nome da marca</label>
                </div>

                <div class="form-floating">
                    <textarea class="form-control type_msg mb-3" id="new_brand_desc_id" style="border-radius: 15px"></textarea>
                    <label for="new_brand_desc_id">Descrição da marca</label>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4 cancel-btn" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-basic btn-primary rounded-pill px-4" id="create_new_brand_btn_id">
                <span><i class="fa-solid fa-file-plus"></i></span>
            </button>
        </div>
        </div>
    </div>
</div>

<!-- Modal edit brand -->
<div class="modal fade" id="edit_brand_modal_id" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="backdrop-filter: blur(25px);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-default">
        <div class="modal-header">
            <h5 class="modal-title text-white" id="staticBackdropLabel">Edite a marca</h5>
        </div>
        <div class="modal-body">
            <form>
                <div class="form-floating">
                    <input type="name" autocomplete="name" class="form-control type_msg mb-3" id="edit_brand_name_id" style="border-radius: 15px">
                    <label for="edit_brand_name_id">Nome da marca</label>
                </div>

                <div class="form-floating">
                    <textarea class="form-control type_msg mb-3" id="edit_brand_desc_id" style="border-radius: 15px"></textarea>
                    <label for="edit_brand_desc_id">Descrição da marca</label>
                </div>
            </form>

            <h3 class="text-white">Gerenciar plataformas</h3>
            <div class="d-flex flex-column">
                <ul class="list-group mb-3" style="max-height: 320px" id="list_platforms_id">
                    <li class="list-group-item d-flex align-items-center">
                        <span class="me-auto">Plataforma 1: Instagram - Nome da plataforma</span>
                        <span class="btn btn-icon text-white">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </span>
                        <span class="btn btn-icon text-white">
                            <i class="fa-solid fa-trash-can-xmark"></i>
                        </span>
                    </li>
                </ul>
                <button type="button" class="btn btn-basic btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#create_platform_modal_id" data-bs-dismiss="modal"><span><i class="fa-solid fa-circle-plus"></i></span></button>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4 cancel-btn" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-basic btn-primary rounded-pill px-4" id="save_brand_btn_id">
                <span><i class="fa-solid fa-floppy-disk"></i></span>
            </button>
        </div>
        </div>
    </div>
</div>