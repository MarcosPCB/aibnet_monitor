<!-- Modal select month report brand -->
<div class="modal fade" id="select_month_report_modal_id" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-default">
        <div class="modal-header">
            <h5 class="modal-title text-white" id="staticBackdropLabel">Selecione o mês para gerar o relatório</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <select class="form-select type_msg" style="border-radius: 15px" id="select_month_id">
                <option value="1">Janeiro</option>
                <option value="2">Fevereiro</option>
                <option value="3">Março</option>
                <option value="4">Abril</option>
                <option value="5">Maio</option>
                <option value="6">Junho</option>
                <option value="7">Julho</option>
                <option value="8">Agosto</option>
                <option value="9">Setembro</option>
                <option value="10">Outubro</option>
                <option value="11">Novembro</option>
                <option value="12">Dezembro</option>
            </select>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4 cancel-btn" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-basic btn-primary rounded-pill px-4" id="generate_month_report_btn_id">
                <span><i class="fa-solid fa-file-chart-column"></i></span>
            </button>
        </div>
        </div>
    </div>
</div>

<!-- Modal Config account -->
<div class="modal fade" id="config_modal_id" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-default">
        <div class="modal-header">
            <h5 class="modal-title text-white" id="staticBackdropLabel">Configurar conta</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <ul class="nav nav-tabs" id="myTab" role="tablist" style="border: 0;">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="user-modal-tab" data-bs-toggle="tab" data-bs-target="#user-tab" type="button" role="tab" aria-selected="true">Usuário</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link disabled_btn" id="account-modal-tab" data-bs-toggle="tab" data-bs-target="#account-tab" type="button" role="tab" aria-selected="false">Conta</button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade active show" id="user-tab" role="tabpanel">
                    <div class="p-4 d-flex flex-column">
                        <h3 class="text-white mb-3">Mudar a senha</h3>
                        <hr class="dropdown-divider text-white">
                        <form>
                            <input type="password" autocomplete="new-password" class="form-control type_msg mb-3" id="new_password_id" placeholder="Nova senha" style="border-radius: 15px">
                            <input type="password" autocomplete="new-password" class="form-control type_msg mb-3" id="new_confirm_password_id" placeholder="Confirme a nova senha" style="border-radius: 15px">
                            <div class="d-flex flex-column align-items-end">
                                <button type="button" class="btn btn-basic btn-primary rounded-pill px-4 mb-3" id="change_password_btn_id">
                                    <span>
                                        <i class="fa-solid fa-paper-plane-top"></i>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="tab-pane fade" id="account-tab" role="tabpanel">
                    <div class="p-4 d-flex flex-column">
                        <div class="d-flex">
                            <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4 mb-3 me-2" id="change_account_btn_id" data-bs-dismiss="modal">Trocar de conta</button>
                            <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4 mb-3" id="create_account_btn_id" data-bs-toggle="modal" data-bs-target="#create_account_modal_id" data-bs-dismiss="modal">Criar conta</button>
                        </div>
                        <br>
                        <h3 class="text-white">Gerenciar usuário</h3>
                        <hr class="dropdown-divider text-white">
                        <ul class="list-group" style="max-height: 320px" id="list_users_id">
                            <li class="list-group-item d-flex align-items-center">
                                <span class="me-auto">User: 1 - John Doe</span>
                                <span class="btn btn-icon text-white">
                                    <i class="fa-solid fa-trash-can-xmark"></i>
                                </span>
                                <span class="btn btn-icon text-white">
                                    <i class="fa-solid fa-ballot-check"></i>
                                </span>
                            </li>
                        </ul>
                        <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4 mb-5 align-self-end" data-bs-toggle="modal" data-bs-target="#create_user_modal_id" data-bs-dismiss="modal">
                            <span><i class="fa-solid fa-user-plus"></i></span>
                        </button>
                        
                        <h3 class="text-white">Gerenciar cliente</h3>
                        <hr class="dropdown-divider text-white">
                        <div class="d-flex">
                            <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4 mb-3 me-2" title="Trocar cliente" data-bs-toggle="modal" data-bs-target="#switch_modal_id" data-bs-dismiss="modal"><span><i class="fa-solid fa-arrow-right-arrow-left"></i></span></button>
                            <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4 mb-3 me-2" title="Editar cliente" id="edit_main_brand_config_btn_id"><span><i class="fa-solid fa-file-pen"></i></i></span></button>
                            <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4 mb-3 me-2" title="Gerar relatório da semana" id="generate_weekly_report_btn_id"><span><i class="fa-solid fa-file-chart-pie"></i></span></button>
                            <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4 mb-3 me-2" title="Gerar relatório do mês" data-bs-toggle="modal" data-bs-target="#select_month_report_modal_id" data-bs-dismiss="modal"><span><i class="fa-solid fa-file-chart-column"></i></span></button>
                            <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4 mb-3 me-2" title="Criar novo cliente" data-bs-toggle="modal" data-bs-target="#create_main_brand_modal_id" data-bs-dismiss="modal"><span><i class="fa-solid fa-folder-plus"></i></span></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Fechar</button>
        </div>
        </div>
    </div>
</div>

