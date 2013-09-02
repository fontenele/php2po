<?php

//http://translate.google.com.br/translate_a/t?client=t&sl=pt&tl=en&hl=pt-BR&sc=2&ie=UTF-8&oe=UTF-8&swap=1&oc=2&prev=conf&psl=pt&ptl=en&otf=1&it=sel.3844&ssel=5&tsel=5&q=Trabalho

session_start();

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
ini_set('display_errors', false);

spl_autoload_register('autoload');

function autoload($class) {
    if(file_exists(APPLICATION_PATH . 'controllers/' . $class . '.php')) {
        require_once(APPLICATION_PATH . 'controllers/' . $class . '.php');
        return true;
    }else{
        //xd($class);
    }
}

// Libs
require_once('libs/fdebug.php');
require_once('libs/frequest.php');
require_once('libs/fsession.php');
require_once('libs/fxml.php');
require_once('libs/smarty/Smarty.class.php');
require_once('libs/controller.php');

$request = new FRequest($_SERVER['REQUEST_URI']);
$session = new FSession();

$controller = ucfirst($request->controller);
$action = $request->action;

$controller = new $controller($request, $session);
$controller->$action();





/*
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Libs
require_once('libs/fdebug.php');
require_once('libs/frequest.php');
require_once('libs/fsession.php');
require_once('libs/fxml.php');

// Session
FSession::init();

// Host/Path
$host_path = substr($_SERVER['SCRIPT_FILENAME'], 0, strlen($_SERVER['SCRIPT_FILENAME']) - strpos(strrev($_SERVER['SCRIPT_FILENAME']), "/"));
$http_path = "http://" . $_SERVER['HTTP_HOST'] . substr($_SERVER['PHP_SELF'], 0, strlen($_SERVER['PHP_SELF']) - strpos(strrev($_SERVER['PHP_SELF']), "/"));
define('APPLICATION_PATH', $host_path);
define('APPLICATION_URL', $http_path);

// Request
$request = new FRequest($_SERVER['REQUEST_URI']);

$s = $request->offsetExists('s') ? $request->offsetGet('s') : null;
$breadCrumbs = array(array('Home','./'));

switch($s) {
    default;
    case '1':
        require_once('home.php');
    break;
    case '2A':
        $breadCrumbs[] = array('Novo Projeto', '?s=2A');
        require_once('new.php');
    break;
    case '2B':
        $breadCrumbs[] = array('Importar Projeto', '?s=2B');
        require_once('import.php');
    break;
    case '3':
        $breadCrumbs[] = array('Projeto', '?s=3');
        require_once('project.php');
    break;
}*/

/*require_once('libs/poutils.php');

$outputPo = $outputMo = null;

if($_POST) {
	$dirProjeto = $_POST['directory'];
	$poUtils = new POutils($dirProjeto);
	$poUtils->startSearch();
    $outputPo = $poUtils->createPoFile();
    $outputMo = $poUtils->createMoFile($outputPo);
}*/
/*
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title>php2po</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Create and convert Po and Mo translations files.">
        <meta name="author" content="Guilherme Fontenele, Mathias Grimm">
        <!--link rel="shortcut icon" href="../../assets/ico/favicon.png"-->
        <link href="<?php echo APPLICATION_URL ?>css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo APPLICATION_URL ?>css/bootstrap-theme.min.css" rel="stylesheet">
        <link href="<?php echo APPLICATION_URL ?>css/style.css" rel="stylesheet">
	</head>
	<body>
        <script>
            var hostPath = '<?php echo APPLICATION_URL ?>';
        </script>
        <div id="wrap">
            <div class="container">

                <div class="page-header">
                    <h1>php2po</h1>
                </div>

                <ol class="breadcrumb">
                    <?php
                    foreach($breadCrumbs as $i => $bc){
                        if(($i+1) == count($breadCrumbs)) {
                            echo "<li class='active'>{$bc[0]}</li>";
                        }else{
                            echo "<li><a href='{$bc[1]}'>{$bc[0]}</a></li>";
                        }
                    }
                    ?>
                </ol>


                <?php getContent(); ?>

                <!--form id="fm-search" name="fm-search" method="post">
        			<label>Projeto: </label>
        			<input type="text" name="directory" />
        			<input type="submit" value="Pesquisar" />
        		</form>
                <br /><br /-->
                <?php /*if($outputPo): ?>
                    <a href="<?php echo $outputPo ?>">Arquivo .po</a><br />
                <?php endif; ?>
                <?php if($outputMo): ?>
                    <a href="<?php echo $outputMo ?>">Arquivo .mo</a>
                <?php endif;*/ /* ?>

            </div>
        </div>

        <div id="footer">
            <div class="container">
                <p class="text-muted credit"> <a href="https://github.com/fontenele/php2po">php2po</a></p>
            </div>
        </div>

        <script src="<?php echo APPLICATION_URL ?>js/jquery.js"></script>
        <script src="<?php echo APPLICATION_URL ?>js/bootstrap.min.js"></script>
        <script src="<?php echo APPLICATION_URL ?>js/script.js"></script>

	</body>
</html>
               */ ?>