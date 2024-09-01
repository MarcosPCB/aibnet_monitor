

<!DOCTYPE html>
<html>
	<head>
		<title>AIBNet Monitor - Chat</title>

		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

		<link rel="stylesheet" href="css/fonts/all.css">

		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
        
        <script src="{{asset('js/app.js')}}"></script>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
	</head>
	<body>
		<div class="container-fluid h-100">
			<div class="row justify-content-center h-100">
				<div class="col-md-4 col-xl-3 chat"><div class="card mb-sm-3 mb-md-0 contacts_card">
					<div class="card-header">
						<div class="input-group align-items-center">
							<div class="btn-group dropend">
								<div class="d-flex justify-content-center align-items-center me-3 btn" style="padding: 0;" data-bs-toggle="dropdown">
									<img class="img-fluid" style="max-width: 64px; border-radius: 50%" id="brand_pic_id" src="img/logo_black.png">
								</div>
								<ul class="dropdown-menu">
									<li data-bs-toggle="modal" data-bs-target="#switch_modal_id"><i class="fa-solid fa-arrow-right-arrow-left"></i> Mudar cliente</li>
									<li data-bs-toggle="modal" data-bs-target="#config_modal_id"><i class="fa-solid fa-gear"></i> Configurar</li>
									<li data-bs-toggle="modal" data-bs-target="#logout_modal_id"><i class="fa-solid fa-circle-xmark"></i> Logout</li>
								</ul>
							</div>
							<input type="text" placeholder="Search..." name="" class="form-control search">
							<div class="input-group-prepend">
								<span class="input-group-text search_btn"><i class="fas fa-search"></i></span>
							</div>
						</div>
					</div>
					<div class="card-body contacts_body">
                        <ui class="contacts d-flex justify-content-center align-items-center flex-column" id="chat_cards_id">
						</ui>
					</div>
					<div class="card-footer"></div>
				</div></div>
				<div class="col-md-8 col-xl-6 chat">
					<div class="card">
						<div class="card-header msg_head">
							<div class="d-flex bd-highlight justify-content-between">
								<div class="user_info">
									<span id="chat_name_id"></span>
									<p id="chat_num_msgs_id"></p>
								</div>
							</div>
						</div>
						<div class="card-body msg_card_body" id="msg_card_body_id">
							<div class="d-flex justify-content-center align-items-center h-100">
								<img class="img-fluid h-100" src="img/logo_cyan.png">
							</div>
						</div>
						<div class="card-footer move-down" id="msg_body_footer">
							<div class="input-group">
								<!--<div class="input-group-append">
									<span class="input-group-text attach_btn"><i class="fas fa-paperclip"></i></span>
								</div>-->
									<textarea name="" class="form-control type_msg" style="border-radius: 15px 0 0 15px !important;" placeholder="Digite a sua mensagem..." id="msg_area_id"></textarea>
								<div class="input-group-append">
									<span class="input-group-text send_btn disabled_btn" id="send_btn_id"><i class="fas fa-location-arrow"></i></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

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
							<hr class="dropdown-divider text-white">
							<h3 class="text-white">Gerenciar usuário</h3>
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
							
							<h3 class="text-white">Gerenciar marca</h3>
							<hr class="dropdown-divider text-white">
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

	<div class="flex-column justify-content-center align-items-center" style="position: absolute; top: 0;left: 0; width: 100vw; height: 100vh; z-index: 99999; backdrop-filter: blur(25px); opacity: 1.0; display: flex; transition: all 1s ease-in-out" id="load_app_id">
		<div class="spinner-border text-white" role="status"></div>
		<h4 class="text-white mt-3">Carregando...</h4>
	</div>

	</body>
</html>

