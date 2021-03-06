<?php

require 'IntegracaoPagseguroController.php';
require 'CupomController.php';
require 'NewsletterController.php';
require 'VendaController.php';

class LojaController extends IntegracaoPagseguroController {
	public $layout = 'lojaexemplo';	

	public function beforeFilter(){
	   return true;
	}

	public function loadProducts($id_categoria = null, $id_produto = null) {
		$this->loadModel('Produto');

      $params = array('conditions' => 
         array(
            'Produto.ativo' => 1,
            'Produto.id_usuario' => $this->Session->read('Usuario.id')
         )
      );

      if ($id_categoria != null) {
         $params['conditions']['Produto.categoria_id'] = $id_categoria;
      }

      if ($id_produto != null) {
         $params['conditions']['Produto.id'] = $id_produto;
      }

		$produtos = $this->Produto->find('all', $params);

	   return $produtos;
	}

   public function loadBanners($id_banner = null) {
      $this->loadModel('Banner');

      $params = array('conditions' => 
         array('ativo' => 1,
              'usuario_id' => $this->Session->read('Usuario.id')
         )
      );

      $banners = $this->Banner->find('all', $params);

      return $banners;
   } 

	public function addCart() {
		$produto = $this->request->data('produto');
		
		if (empty($produto)) {
			$this->redirect('/');
		}

      if (!$this->validateProduct($produto)) {
         $this->Session->setFlash('Quantidade de produtos escolhidas é maior do que a disponivel!');
         $this->redirect('/');
      }

		$cont = count($this->Session->read('Produto'));

		$this->Session->write('Produto.'.$produto['id'].'.id' , $produto['id']);
      $this->Session->write('Produto.'.$produto['id'].'.quantidade' , $produto['quantidade']);
      $this->Session->write('Produto.'.$produto['id'].'.variacao', $produto['variacao']);

		$this->redirect('/cart');
	}

   public function removeProductCart() {
      if ( ($this->Session->read('Produto.' . $this->params['id'])) !== null ) {
         $this->Session->delete( 'Produto.' . $this->params['id'] );
      }

      $this->redirect('/cart');
   }

	public function clearCart() {
		$this->Session->delete('Produto');
	}

   public function loadProductsAndValuesCart() {
      $this->loadModel('Produto');
      $this->loadModel('Variacao');

      $productsSession = $this->Session->read('Produto');

      (float) $total = 0.00;
      $produtos      = array();
      foreach ($productsSession as $indice => $item) {
         $produto =  $this->Produto->find('all', 
            array('conditions' => 
               array('Produto.ativo' => 1,
                    'Produto.id' => $item['id']
               )
            )
         );

         $total     += $produto[0]['Produto']['preco'] * $item['quantidade'];

         $produto[0]['Produto']['quantidade'] = $item['quantidade'];

         $variacao = $this->Variacao->find('all', 
            array('conditions' =>
               array('Variacao.id' => $item['variacao'])
            ),
            array('fields' => 
               array('Variacao.nome_variacao')
            )
         );

         $produto[0]['Produto']['variacao'] = $variacao[0]['Variacao']['nome_variacao'];

         $produtos[] = $produto[0];
      }

      return array('products_cart' => $produtos, 'total' => $total);
   }

   public function loadCategoriesProducts($id_categoria = null) {
      $this->loadModel('Categoria');

      $params = array('conditions' => 
         array('ativo' => 1,
              'usuario_id' => $this->Session->read('Usuario.id')
         )
      );

      $categorias = $this->Categoria->find('all', $params);

      return $categorias;
   }

   public function payment() {
      $andress = $this->request->data('endereco');
      $client  = $this->request->data('cliente');

      $products = $this->loadProductsAndValuesCart();

      (float) $valor_frete = number_format($this->Session->read('Frete.valor'), 2, '.', ',');

      $objVenda = new VendaController();
      $productsSale = $this->prepareProductsSale($products['products_cart']);
      $usuario_id = $this->Session->read('Usuario.id');

      $retorno_venda = $objVenda->salvar_venda($productsSale, array(), array('valor' => $valor_frete + $products['total']), $usuario_id);

      $this->paymentPagSeguro($products['products_cart'], $andress, $client, $products['total'], $valor_frete, $retorno_venda['id']);
   }

   public function prepareProductsSale($products) {
      $retorno = array();
      foreach ($products as $i => $product) {
         $retorno[$i]['id_produto'] = $product['Produto']['id'];
         $retorno[$i]['quantidade'] = $product['Produto']['quantidade'];
         $retorno[$i]['variacao']   = $product['Produto']['variacao'];
      }

      return $retorno;
   }

   public function searchAndressByCep($cep) {
      $this->layout = 'ajax';

      $curl = curl_init('http://cep.correiocontrol.com.br/'.$cep.'.js');

      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      $resultado = curl_exec($curl);

      echo $resultado;
      exit();
   }

   public function calcTransportAjax() {
      $this->layout = 'ajax';
      $cep_destino = $this->request->data('cep_destino');
      $cep_origem  = $this->request->data('cep_origem');

      $dataProducts = $this->loadProductsAndValuesCart();

      (float) $peso = 0;
      foreach ($dataProducts['products_cart'] as $i => $product) {
         $peso += $product['Produto']['peso_bruto'] * $product['Produto']['quantidade'];
      }

      $fretes = $this->transport($cep_destino, $cep_origem, $peso);

      $disponiveis = array();
      $cont = 0;
      foreach ($fretes as $i => $frete) {
         $disponiveis[$cont]['valor']  = (array) $frete->Valor;
         $disponiveis[$cont]['prazo']  = (array) $frete->PrazoEntrega;
         $disponiveis[$cont]['codigo'] = (array) $frete->Codigo;

         $disponiveis[$cont]['valor']  = array_shift($disponiveis[$cont]['valor']);
         $disponiveis[$cont]['prazo']  = array_shift($disponiveis[$cont]['prazo']);
         $disponiveis[$cont]['codigo'] = array_shift($disponiveis[$cont]['codigo']);
         $cont++;
      }

      $this->Session->write('Frete.valor', $disponiveis[$cont - 1]['valor']);

      (float) $total = $disponiveis[$cont - 1]['valor'] + $dataProducts['total'];
      $total = number_format($total, 2, ',', '.');

      $retorno = array('frete' => $disponiveis[$cont - 1]['valor'], 'total' => $total);

      echo json_encode($retorno);   
      exit();
   }
   
   public function transport($cep_destino, $cep_origem, $peso) {
      $altura = '2';
      $largura = '11';
      $comprimento = '16';

      $dados['sCepDestino'] = '07252-000';
      $dados['sCepOrigem'] = '09181-000';
      $dados['nVlPeso'] = $peso;
      $dados['nVlComprimento'] = $comprimento;
      $dados['nVlAltura'] = $altura;
      $dados['nVlLargura'] = $largura;
      $dados['nCdServico'] = 41106;//pac varejo
      $dados['nVlDiametro'] = '2';
      $dados['nCdFormato'] = '1';
      $dados['sCdMaoPropria'] = 'n';
      $dados['nVlValorDeclarado'] = '0';
      $dados['StrRetorno'] = 'xml';

      $dados = http_build_query($dados);

      $curl = curl_init('http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx' . '?' . $dados);

      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      $resultado = curl_exec($curl);

      $resultado = simplexml_load_string($resultado);

      return $resultado;
   }

   public function saveEmailNewsletter() {
      $nome  = $this->request->data('nome');
      $email = $this->request->data('email');

      $objNewsletter = new NewsletterController();

      if ($objNewsletter->newsletter_cadastro($nome, $email, $this->Session->read('Usuario.id')))
      {
         echo json_encode(true);
         exit();
      } 

      echo json_encode(false);
      exit();
   }

   public function useCoupon() {
      $this->layout = 'ajax';

      $cupom = $this->request->data('cupom');
      $valor  = $this->request->data('valor');
         
      $objCupom = new CupomController();
      $novo_valor = $objCupom->utilizar_cupom($cupom, $valor, $this->Session->read('Usuario.id'));

      if (!$novo_valor)
      {
         echo json_encode(false);
         exit();
      }

      echo json_encode($novo_valor);
      exit();
   }

   public function validateProduct($data) {
      $this->loadModel('Variacao');

      $params = array('conditions' => 
         array(
            'Variacao.id' => $data['variacao']
         )
      );

      $variacao = $this->Variacao->find('all', $params);

      if ($variacao[0]['Variacao']['estoque'] <= 0 || $data['quantidade'] > $variacao[0]['Variacao']['estoque'])
      {
         return false;
      }

      return true;   
   }

	/**
	* Views
	*/
	public function index() {
      $this->set('banners', $this->loadBanners());
      $this->set('categorias', $this->loadCategoriesProducts());
		$this->set('produtos', $this->loadProducts());
	}

   public function cart() {
      $this->set('categorias', $this->loadCategoriesProducts());
      $products = $this->loadProductsAndValuesCart();

      $this->set('products', $products['products_cart']);
      $this->set('total', $products['total']);
   }

   public function checkout() {
      $this->set('categorias', $this->loadCategoriesProducts());  
      $products = $this->loadProductsAndValuesCart();

      $this->set('products', $products['products_cart']);
      $this->set('total', $products['total']);
   }

   public function category() {
      $id   = $this->params['id'];
      $nome = $this->params['nome'];

      $products = $this->loadProducts($id);

      $this->set('categorias', $this->loadCategoriesProducts());
      $this->set('produtos', $products);
      $this->set('nameCategory', $nome);
   }

   public function product() {
      $this->loadModel('Produto');

      $id = $this->params['id'];

      $this->set('categorias', $this->loadCategoriesProducts());
      
      $produto = $this->loadProducts(null, $id)[0];

      $this->loadModel('Variacao');

      $query = array (
         'joins' => array(
                array(
                    'table' => 'produtos',
                    'alias' => 'Produto',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Variacao.produto_id = Produto.id',
                    ),
                )
            ),
           'conditions' => array('Variacao.produto_id' => $id, 'Variacao.ativo' => 1),
           'fields' => array('Produto.id, Variacao.*'),
      );

      $variacoes = $this->Variacao->find('all', $query);
      $this->set('variacoes', $variacoes);

      $this->set('produto', $produto);
   }

}