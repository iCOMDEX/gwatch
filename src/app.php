<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Silex\Provider\FormServiceProvider;
 


// Register service providers.
$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
$app->register(new Silex\Provider\RoutingServiceProvider());
$app->register(new Silex\Provider\FormServiceProvider());
 
 

//Twig
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.options' => array(
        'cache' => isset($app['twig.options.cache']) ? $app['twig.options.cache'] : false,
        'strict_variables' => true,
    ),
     'twig.path' => array(__DIR__ . '/Views')

));


//Security
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'admin' => array(
            'pattern' => '^/',
            'form' => array(
                'login_path' => '/login',
                'check_path' => '/login_check',
                'username_parameter' => 'form[username]',
                'password_parameter' => 'form[password]',
            ),
            'logout'  => true,
            'anonymous' => true,
            
        ),
    ),
    'security.role_hierarchy' => array(
       'ROLE_ADMIN' => array('ROLE_USER'),
    ),
));

 
//CAche
$app['useCacheFile'] = 0;
  

// Register repositories.
$app['repository.display'] = function ($app) {
    return new GWatch\Repository\DisplayRepository( $app['db'], $app['DatabaseNamePrefics'], $app['ManagmentDatabaseName'] );  
};

$app['repository.index'] = function ($app) {
    return new GWatch\Repository\IndexRepository( $app['db'], $app['DatabaseNamePrefics'], $app['ManagmentDatabaseName'] );  
};

 
$app['repository.user'] = function ($app) {
    return new GWatch\Repository\UserRepository( $app['db'], $app['security.encoder.digest']);  
};
 

// Register the error handler.
$app->error(function (\Exception $e, Request $request, $code) use($app) {

    if ($app['debug']) {
       return;
 	}
 
    switch ($code) {
        case 404:
            $data = array(
	    	'description' => 'Requested page could not found',
	    	'headtitle' => 'Error 404',      	      

        );
           return $app['twig']->render('404.html.twig', $data);
         //   $message = 'The requested page could not be found.';
            
        break;
        default:
        
            $message = 'We are sorry, but something went terribly wrong.';
            
    }

 
});