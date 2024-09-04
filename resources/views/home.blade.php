

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
        
		<script>
			window.env = {
				API_URL: "{{ env('API_URL') }}"
			};
		</script>

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

		@include('modals.account')
		@include('modals.auth')
		@include('modals.brand')
		@include('modals.chat')
		@include('modals.config')
		@include('modals.main-brand')
		@include('modals.platform')
		@include('modals.user')

		<div class="flex-column justify-content-center align-items-center" style="position: absolute; top: 0;left: 0; width: 100vw; height: 100vh; z-index: 99999; backdrop-filter: blur(25px); opacity: 1.0; display: flex; transition: all 1s ease-in-out" id="load_app_id">
			<div class="spinner-border text-white" role="status"></div>
			<h4 class="text-white mt-3">Carregando...</h4>
		</div>

	</body>
</html>

