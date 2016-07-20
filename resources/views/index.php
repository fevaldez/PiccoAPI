<!doctype html>
<html lang="en" ng-app="RDash">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Sistema SIE</title>
  <!-- STYLES -->
  <link rel="stylesheet" href="lib/css/main.min.css"/>
  <link rel="stylesheet" href="css/main.css"/>
  <!-- SCRIPTS -->
  <script src="lib/js/main.min.js"></script>
  <!-- Custom Scripts -->
  <script type="text/javascript" src="js/dashboard.min.js"></script>
</head>
<body ng-controller="MasterCtrl">


        <!-- NAV Bar -->
    <!-- <div id="content-wrapper">
      <div class="page-content"> -->
    <nav ng-hide="$state.current.name === 'auth'" class="navbar navbar-ct-green navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapse" data-toggle="bs-example-navbar-collapse-1"
          data-target="#collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand navbar-brand-logo" href="">
                <span class="brand">Pico</span>
                <div class="logo">
                    <img src="img/Picco-Logo (Custom).png">
                </div>
                <span class="brand"> SIE</span>
              </a>
        </div><!-- navbar-header -->
        <div class="navbar-collapse collapse" id="bs-example-navbar-collapse-1">
          <ul class="nav navbar-nav navbar-left">
              <li ng-class="{'disabled':true}">
                  <!-- <a href="#/dashboard" -->
                  <a href=""
                    uib-tooltip="Dashboard" tooltip-placement="bottom">
                      Dashboard
                  </a>
              </li>
              
              <li class="dropdown">
                <a href="" class="dropdown-toggle" data-toggle="dropdown">
                  Estado de Resultado <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                  <li><a tabindex="-1" ui-sref="statusByRange">Estado de Resultado por Fechas</a></li>
                </ul>
              </li>
              
              <li class="dropdown" permission="['', 'ceo', 'stakeholder']">
                <a href="" class="dropdown-toggle" data-toggle="dropdown">
                  Reportes <b class="caret"></b>
                </a>
                <ul class="dropdown-menu">
                  <li><a tabindex="-1" ui-sref="obras">Resultado de Obra</a></li>
                </ul>
              </li>

              <!-- <li>
                  <a href="#"
                    uib-tooltip="Messages" tooltip-placement="bottom">
                      <i class="fa fa-bell-o">
                      </i><span class="redlight">9</span>
                  </a>
              </li> -->
          </ul>

          <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
              <a href="" class="dropdown-toggle" data-toggle="dropdown">
                  Bienvenido
                  <b class="caret"></b>
              </a>
              <ul class="dropdown-menu">
                <!-- <li><a tabindex="-1" href=""><span class="fa fa-user"></span> Perfil</a></li> -->
                <li class="divider"></li>
                <li><a tabindex="-1" ng-click="logout()" style="cursor: pointer"><span class="fa fa-sign-out"></span> Logout</a></li>
              </ul>
            </li>
            
          </ul>
        </div><!-- navbar-collapse collapse -->
      </div><!-- /.container-fluid -->
    </nav>
        <!-- End NAV Bar -->

        <!-- Main Content -->
        <div ng-view class="fadeInUp">
          <div growl></div>
        </div>

        <div class="full-height" ui-view>
        </div>

    <!--   </div><!-- End Page Content -->
    <!-- </div>End Content Wrapper -->
</body>
</html>
