<!-- Modal select account -->
<div class="modal fade" id="select_account_modal_id" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="backdrop-filter: blur(25px);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-default">
        <div class="modal-header">
            <h5 class="modal-title text-white" id="staticBackdropLabel">Selecione a conta</h5>
        </div>
        <div class="modal-body">
            <select class="form-select type_msg" style="border-radius: 15px" id="account_select_id">
            </select>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4 cancel-btn" id="select_account_cancel_btn_id">Cancelar</button>
            <button type="button" class="btn btn-basic btn-primary rounded-pill px-4" id="account_select_btn_id">
                <span><i class="fa-solid fa-check"></i></span>
            </button>
        </div>
        </div>
    </div>
</div>

<!-- Modal create account -->
<div class="modal fade" id="create_account_modal_id" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="backdrop-filter: blur(25px);">
    <div class="modal-dialog modal-dialog-centered" style="min-width: 50vw;">
        <div class="modal-content bg-default">
        <div class="modal-header">
            <h5 class="modal-title text-white" id="staticBackdropLabel">Crie uma nova conta</h5>
        </div>
        <div class="modal-body" style="max-height: 75vh; overflow-y: auto">
            <form>
                <div class="form-floating">
                    <input type="name" autocomplete="name" class="form-control type_msg mb-3" id="new_account_name_id" placeholder="Nome da conta" style="border-radius: 15px">
                    <label for="new_account_name_id">Nome da conta</label>
                </div>

                <div class="form-floating">
                    <select class="form-select type_msg mb-3" style="border-radius: 15px" id="new_account_paymethod_id">
                        <option value="0">Boleto</option>
                        <option value="1">Pix</option>
                        <option value="2">Cartão de debito</option>
                        <option value="3">Cartão de crédito</option>
                    </select>
                    <label for="new_account_paymethod_id">Forma de pagamento</label>
                </div>

                <div class="form-floating">
                    <input type="number" class="form-control type_msg mb-3" id="new_account_installments_id" value="1" style="border-radius: 15px">
                    <label for="new_account_installments_id">Número de parcelas</label>
                </div>

                <div class="form-floating">
                    <select class="form-select type_msg mb-3" style="border-radius: 15px" id="new_account_contype_id">
                        <option value="0">Mensal</option>
                        <option value="1">Trimestral</option>
                        <option value="2">Semestral</option>
                        <option value="3">Anual</option>
                        <option value="3">2 anos</option>
                        <option value="3">Promocional</option>
                    </select>
                    <label for="new_account_contype_id">Typo de contrato</label>
                </div>
                
                <div class="form-floating">
                    <textarea class="form-control type_msg mb-3" id="new_account_condesc_id" style="border-radius: 15px"></textarea>
                    <label for="new_account_condesc_id">Descrição do contrato</label>
                </div>
                
                <div class="form-floating">
                    <input type="number" class="form-control type_msg mb-3" id="new_account_conbrands_id" value="1" style="border-radius: 15px">
                    <label for="new_account_conbrands_id">Número de marcas contratadas</label>
                </div>
                
                <div class="form-floating">
                    <input type="number" class="form-control type_msg mb-3" id="new_account_conbrandopp_id" value="2" style="border-radius: 15px">
                    <label for="new_account_conbrandopp_id">Número de concorrentes por marca contratados</label>
                </div>
                
                <div class="form-floating">
                    <input type="number" class="form-control type_msg mb-3" id="new_account_conusers_id" value="1" style="border-radius: 15px">
                    <label for="new_account_conusers_id">Número de usuários contratados</label>
                </div>
                
                <div class="form-floating">
                    <input type="number" class="form-control type_msg mb-3" id="new_account_conbuild_id" value="1" style="border-radius: 15px">
                    <label for="new_account_conbuild_id">Meses de construção de marca contratados</label>
                </div>
                
                <div class="form-floating">
                    <input type="number" class="form-control type_msg mb-3" id="new_account_cancel_id" value="12" style="border-radius: 15px">
                    <label for="new_account_cancel_id">Mínimo de meses para cancelar sem multa</label>
                </div>
                
                <div class="list-group-item mb-3">
                    <input class="form-check-input text-white me-2" type="checkbox" checked id="new_account_paid_id">
                    Pago
                </div>
                
                <div class="list-group-item">
                    <input class="form-check-input text-white me-2" type="checkbox" checked id="new_account_active_id">
                    Ativo
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4 cancel-btn" >Cancelar</button>
            <button type="button" class="btn btn-basic btn-primary rounded-pill px-4" id="create_account_2_btn_id">
                <span><i class="fa-solid fa-cart-plus"></i></span>
            </button>
        </div>
        </div>
    </div>
</div>