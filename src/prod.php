<?php


$app['debug'] = true;

// Timezone.
date_default_timezone_set('Europe/London');


// Emails.
$app['admin_email'] = 'admin@gen-watch.org';
$app['site_email'] = 'noreply@gen-watch.org';

// Doctrine (db)
$app['db.options'] = array(
    'driver'   => 'pdo_mysql',
    'host'     => '127.0.0.1',
    'port'     => '3306',
    'dbname'   => 'Module_S',
    'user'     => 'gwatch',
    'password' => 'gwatch',
);

$app['DatabaseNamePrefics'] = 'Module_';
$app['ManagmentDatabaseName'] = 'GWATCH';

// SwiftMailer
// See http://silex.sensiolabs.org/doc/providers/swiftmailer.html
$app['swiftmailer.options'] = array(
    'host' => 'host',
    'port' => '25',
    'username' => 'username',
    'password' => 'password',
    'encryption' => null,
    'auth_mode' => null
);

