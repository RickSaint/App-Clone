<?php

	namespace App\Controllers;

	//os recursos do miniframework
	use MF\Controller\Action;
	use MF\Model\Container;

	class AppController extends Action {

		public function timeline() {

			$this->validaAutentificacao();

			//recuperação dos tweets
			$tweet = Container::getModel('Tweet');

			$tweet->__set('id_usuario', $_SESSION['id']);

			$tweets = $tweet->getAll();

			$this->view->tweets = $tweets;

			//variaveis de paginação
			$total_registros_pagina = 10;
			$deslocamento = 0;
			$pagina = 1;

			echo "<br><br><br><br>Pagina: $pagina | Toltal de registro por pagina: $total_registros_pagina | Deslocamento: $deslocamento";
			$tweets = $tweet->getPorPagina($total_registros_pagina, $deslocamento);



			$usuario = Container::getModel('Usuario');
			$usuario->__set('id', $_SESSION['id']);

			$this->view->info_usuario = $usuario->getInfoUsuario();
			$this->view->info_tweets = $usuario->getTotalTweets();
			$this->view->info_seguindo = $usuario->getTotalSeguindo();
			$this->view->info_seguidores = $usuario->getTotalSeguidores();


			$this->render('timeline');
				
		}

		public function tweet() {

			$this->validaAutentificacao();
				
			$tweet = Container::getModel('tweet');

			$tweet->__set('tweet', $_POST['tweet']);
			$tweet->__set('id_usuario', $_SESSION['id']);

			$tweet->salvar();

			header('Location: /timeline');

		}

		public function validaAutentificacao() {

			session_start();

			if(!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
				header('Location: /?login=erro');
			}
		}

		public function quemSeguir() {

			$this->validaAutentificacao();

			$pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';

			$usuarios = array();

			if($pesquisarPor != '') {

				$usuario = Container::getModel('Usuario');
				$usuario->__set('nome', $pesquisarPor);
				$usuario->__set('id', $_SESSION['id']);
				$usuarios = $usuario->getAll();
			}

			$this->view->usuarios = $usuarios;

			$usuario = Container::getModel('Usuario');
			$usuario->__set('id', $_SESSION['id']);

			$this->view->info_usuario = $usuario->getInfoUsuario();
			$this->view->info_tweets = $usuario->getTotalTweets();
			$this->view->info_seguindo = $usuario->getTotalSeguindo();
			$this->view->info_seguidores = $usuario->getTotalSeguidores();

			$this->render('quemSeguir');
		}

		public function acao() {

			$this->validaAutentificacao();

			$acao = isset($_GET['acao']) ? $_GET['acao'] : '';
			$id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

			$usuario = Container::getModel('Usuario');
			$usuario->__set('id', $_SESSION['id']);

			if($acao == 'seguir') {
				$usuario->seguirUsuario($id_usuario_seguindo);
			} else if($acao == 'deixar_de_seguir') {
				$usuario->deixarSeguirUsuario($id_usuario_seguindo);
			}

			header('Location: /quem_seguir');

		}

		public function removerTweet(){    
			$this->validaAutenticacao();    
			$id = isset($_GET['id']) ? $_GET['id'] : '';    
			$tweet = Container::getModel('Tweet');    
			$tweet->__set('id',$id);    
			$tweet->remover();    
			header('location: /timeline');
		}
	}

?>