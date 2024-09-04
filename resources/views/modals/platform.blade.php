<!-- Modal create platform -->
<div class="modal fade" id="create_platform_modal_id" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="backdrop-filter: blur(25px);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-default">
        <div class="modal-header">
            <h5 class="modal-title text-white" id="staticBackdropLabel">Insira uma plataforma da marca</h5>
        </div>
        <div class="modal-body" style="max-height: 75vh; overflow-y: auto">
            <form>
                <div class="form-floating">
                    <input type="name" autocomplete="name" class="form-control type_msg mb-3" id="new_platform_name_id" placeholder="Nome da marca na página" style="border-radius: 15px">
                    <label for="new_platform_name_id">Nome da marca na página</label>
                </div>

                <div class="form-floating">
                    <select class="form-select type_msg mb-3 me-auto" style="border-radius: 15px" id="new_platform_type_id">
                        <option value="instagram">Instagram</option>
                        <option value="tiktok">Tik Tok</option>
                        <option value="youtube">YouTube</option>
                    </select>
                    <label for="new_platform_type_id">Qual plataforma</label>
                </div>

                <div class="form-floating">
                    <input type="text" class="form-control type_msg mb-3" id="new_platform_url_id" placeholder="URL da página" style="border-radius: 15px">
                    <label for="new_platform_url_id">URL da página</label>
                </div>

                <div class="form-floating">
                    <input type="text" class="form-control type_msg mb-3" id="new_platform_id_id" placeholder="ID da página" style="border-radius: 15px">
                    <label for="new_platform_id_id">ID da página</label>
                </div>

                <div class="list-group-item">
                    <input class="form-check-input text-white me-2" type="checkbox" checked id="new_platform_active_id">
                    Ativo
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4 cancel-btn" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-basic btn-primary rounded-pill px-4" id="create_new_platform_btn_id">
                <span><i class="fa-solid fa-file-plus"></i></span>
            </button>
        </div>
        </div>
    </div>
</div>

<!-- Modal edit platform -->
<div class="modal fade" id="edit_platform_modal_id" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="backdrop-filter: blur(25px);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-default">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="staticBackdropLabel">Altere a plataforma da marca</h5>
            </div>
            <div class="modal-body" style="max-height: 75vh; overflow-y: auto">
                <form>
                    <div class="form-floating">
                        <input type="name" autocomplete="name" class="form-control type_msg mb-3" id="edit_platform_name_id" placeholder="Nome da marca na página" style="border-radius: 15px">
                        <label for="edit_platform_name_id">Nome da marca na página</label>
                    </div>

                    <div class="form-floating">
                        <select class="form-select type_msg mb-3 me-auto" style="border-radius: 15px" id="edit_platform_type_id">
                            <option value="instagram">Instagram</option>
                            <option value="tiktok">Tik Tok</option>
                            <option value="youtube">YouTube</option>
                        </select>
                        <label for="edit_platform_type_id">Qual plataforma</label>
                    </div>

                    <div class="form-floating">
                        <input type="text" class="form-control type_msg mb-3" id="edit_platform_url_id" placeholder="URL da página" style="border-radius: 15px">
                        <label for="edit_platform_url_id">URL da página</label>
                    </div>

                    <div class="form-floating">
                        <input type="text" class="form-control type_msg mb-3" id="edit_platform_id_id" placeholder="ID da página" style="border-radius: 15px">
                        <label for="edit_platform_id_id">ID da página</label>
                    </div>

                    <div class="list-group-item">
                        <input class="form-check-input text-white me-2" type="checkbox" checked id="edit_platform_active_id">
                        Ativo
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4 cancel-btn" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-basic btn-primary rounded-pill px-4" id="save_edit_platform_btn_id">
                    <span><i class="fa-solid fa-floppy-disk"></i></span>
                </button>
            </div>
        </div>
    </div>
</div>