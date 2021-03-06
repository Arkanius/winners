<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>X Brand</title>

    <?php echo $this->Html->css('font-awesome/css/font-awesome.min'); ?>

    <!-- Bootstrap Core CSS -->
    <link href="/app/webroot/xstore/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="/app/webroot/xstore/css/shop-homepage.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!--Start of Zopim Live Chat Script-->
    <script type="text/javascript">
    window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
    d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
    _.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');
    $.src='//v2.zopim.com/?29Cgb0x8PWQ3X1FKXZXUNJx4D9TjDD5d';z.t=+new Date;$.
    type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');
    </script>
    <!--End of Zopim Live Chat Script-->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>


</head>

<body>

    <div id="fb-root"></div>
    <script>(function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "//connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v2.4&appId=576330039137830";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>

    <!-- Navigation -->
    <div class="container">

            <nav class="navbar navbar-inverse">
          <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="/">X Brand</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav">
                <?php foreach($categorias as $indice => $valor): ?>
                <li class=""><a href="/category/<?php echo $valor['Categoria']['id'] ?>/<?php echo $valor['Categoria']['nome'] ?>"><?php echo $valor['Categoria']['nome'] ?></a></li>
                <?php endforeach; ?>
              </ul>

              <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Sobre <span class="caret"></span></a>
                  <ul class="dropdown-menu">
                    <li><a href="#">Contato</a></li>
                    <li><a href="#">Quem Somos</a></li>
                    <li><a href="#">Como Comprar</a></li
                  </ul>
                </li>
              </ul>

            </div><!-- /.navbar-collapse -->
          </div><!-- /.container-fluid -->
        </nav>

    </div>

    <?php echo $this->fetch('content'); ?>

    <div class="container">

        <hr>

        <!-- Footer -->
        <footer>
        </footer>

    </div>
    <!-- /.container -->

    <!-- jQuery -->
    <script src="/app/webroot/xstore/js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="/app/webroot/xstore/js/bootstrap.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function(){
            $('#myModal').modal('show');   
        });
    </script>

    <!-- Modal -->
<!--     <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <form action="/saveEmailNewsletter" method="post">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Cadastre seu e-mail e ganhe 10% de desconto.</h4>
              </div>
              <div class="modal-body">
                
                <div class='form-row'>
                    <div class='form-group required'>
                        <input type="text" class="form-control" placeholder="Digite seu nome" />
                    </div>
                </div>

                <div class='form-row'>
                    <div class='form-group required'>
                        <input type="email" class="form-control" placeholder="Digite seu email" required/>
                    </div>
                </div>

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary">Quero desconto</button>
              </div>
            </div>
        </form>
      </div>
    </div>
 -->
</body>

</html>
