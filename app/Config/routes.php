<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
	//Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */
	//Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));
	
	$dominio = verificar_dominio();
	if ($dominio['is_winners']) {
		Router::connect('/', array('controller' => 'home', 'action' => 'index'));
	} else {
		Router::connect('/', array('controller' => $dominio['controller'], 'action' => $dominio['funcao']));
	}

	
/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
/**
 * Função para verificar se o dominio pentece ao site, caso não pertença redireciona ao site correto 
 */
	function verificar_dominio() {
		$dominiosWinners = array (
			'winners.local',
			'blog.winnersdesenvolvimento.com.br',
			'www.ciawn.com.br',
			'ciawn.com.br',
			'api.ciawn.com.br',
		);

		$dominiosWinnersRedirect = array(
			'www.winnersdesenvolvimento.com.br',
			'winnersdesenvolvimento.com.br',
		);

		$varDominio = $_SERVER['SERVER_NAME'];

		if (array_search($varDominio, $dominiosWinnersRedirect) !== false) {
			header('Location: http://www.ciawn.com.br');
			exit();
		}

		if (array_search($varDominio, $dominiosWinners) !== false) {
			$retorno['is_winners'] = true;

			return $retorno;
		}

		require(APP . 'Config/Domain/' . $varDominio . '.php');

		$retorno['is_winners'] = false;
		$retorno['id_usuario'] = $dominio['id_usuario'];
		$retorno['controller'] = $dominio['controller'];
		$retorno['funcao']	   = $dominio['funcao'];

		return $retorno;
	}
