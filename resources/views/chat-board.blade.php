

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
									<li data-bs-toggle="modal" data-bs-target="#switch_modal_id"><i class="fa-solid fa-arrow-up-arrow-down"></i> Mudar marca</li>
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
								<img class="img-fluid" src="img/logo_cyan.png">
							</div>
						</div>
						<div class="card-footer move-down" id="msg_body_footer">
							<div class="input-group">
								<div class="input-group-append">
									<span class="input-group-text attach_btn"><i class="fas fa-paperclip"></i></span>
								</div>
								<textarea name="" class="form-control type_msg" placeholder="Digite a sua mensagem..." id="msg_area_id"></textarea>
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
            <div class="modal-content">
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
            <div class="modal-content">
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
            <div class="modal-content">
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
                <h5 class="modal-title text-white" id="staticBackdropLabel">Trocar de marca</h5>
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
						Trocar
					</span>
				</button>
            </div>
            </div>
        </div>
    </div>

	<!-- Config account -->
	<div class="modal fade" id="config_modal_id" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="staticBackdropLabel">Configurar conta</h5>
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
						<i class="fa-solid fa-floppy-disk"></i>
						Salvar
					</span>
				</button>
            </div>
            </div>
        </div>
    </div>

	<!-- Modal logout chat -->
	<div class="modal fade" id="logout_modal_id" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="staticBackdropLabel">Deseja mesmo deslogar?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-basic btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Não</button>
                <button type="button" class="btn btn-basic btn-primary rounded-pill px-4" id="delete_chat_btn_id" data-bs-dismiss="modal">Sim</button>
            </div>
            </div>
        </div>
    </div>

	</body>
</html>

