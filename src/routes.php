<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


// Register route converters.
 

// Register routes.


$app->match('/login.php', 'GWatch\Controller\UserController::loginAction')
    ->bind('login');

$app->match('/register.php', 'GWatch\Controller\UserController::addAction')
    ->bind('register');

//Index section

	$app->get('/', 'GWatch\Controller\IndexController::indexAction')
	->bind('homepage');
	
	$app->get('/imgcolor.php', 'GWatch\Controller\IndexController::imgcolorAction')
	->bind('imgcolor');

	$app->get('/smallimgcolor.php', 'GWatch\Controller\IndexController::smallImgcolorAction')
	->bind('smallimgcolor');

	$app->get('/contacts.php', 'GWatch\Controller\IndexController::contactsAction')
	->bind('contacts');

	$app->get('/description.php', 'GWatch\Controller\IndexController::descriptionAction')
	->bind('description');

	$app->get('/features.php', 'GWatch\Controller\IndexController::featuresAction')
	->bind('features');

	$app->get('/feedback.php', 'GWatch\Controller\IndexController::feedbackAction')
	->bind('feedback');

	$app->get('/paper.php', 'GWatch\Controller\IndexController::paperAction')
	->bind('paper');

	$app->get('/tutorial.php', 'GWatch\Controller\IndexController::tutorialAction')
	->bind('tutorial');

	$app->get('/upload.php', 'GWatch\Controller\IndexController::uploadAction')
	->bind('upload');

	$app->get('/modules.php', 'GWatch\Controller\IndexController::showDatasetsAction')
	->bind('modules');
	
	
//Display section
	
	
	$app->get('/display.php', 'GWatch\Controller\DisplayController::indexAction')
	->bind('display');

	$app->get('/getColumns.php', 'GWatch\Controller\DisplayController::columnsAction')
	->bind('columns');

	$app->get('/getData.php', 'GWatch\Controller\DisplayController::dataAction')
	->bind('data');

	$app->get('/search.php', 'GWatch\Controller\DisplayController::searchAction')
	->bind('search');

	$app->get('/report.php', 'GWatch\Controller\DisplayController::reportAction')
	->bind('report');




