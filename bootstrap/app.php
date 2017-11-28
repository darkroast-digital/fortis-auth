<?php

session_start();

use Respect\Validation\Validator as v;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;

require_once __DIR__ . '/../vendor/autoload.php';

use Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware;


// #GET ENV
// =========================================================================

try {
    (new Dotenv\Dotenv(__DIR__ . '/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}




// #SET TIMEZONE
// =========================================================================

date_default_timezone_set ('America/New_York');




// #BOOT APP
// =========================================================================

$app = new Slim\App([
    'settings' => [
        'determineRouteBeforeAppMiddleware' => true,

        'debug' => getenv('WHOOPS_DEBUG') === 'true',
        'whoops.editor' => 'sublime',
        'displayErrorDetails' => getenv('APP_DEBUG') === 'true',

        'addContentLengthHeader' => false,

        'app' => [
            'name' => getenv('APP_NAME')
        ],

        'site' => [
            'url' => getenv('SITE_URL')
        ],

        'views' => [
            'cache' => getenv('VIEW_CACHE_DISABLED') === 'true' ? false : __DIR__ . '/../storage/views'
        ],

        'database' => [
            'driver' => getenv('DB_DRIVER'),
            'host' => getenv('DB_HOST'),
            'port' => getenv('DB_PORT'),
            'database' => getenv('DB_DATABASE'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
        ],

        'mail' => [
            'from' => [
                'name' => getenv('MAIL_FROM_NAME'),
                'address' => getenv('MAIL_FROM_ADDRESS'),
            ]
        ],

    ],
]);



// #ERROR REPORTING
// =========================================================================

$app->add(new WhoopsMiddleware($app));




// #CONTAINER
// =========================================================================

require_once __DIR__ . '/container.php';




// #PAGINATOR
// =========================================================================

Paginator::currentPathResolver(function () {
    return isset($_SERVER['REQUEST_URI']) ? strtok($_SERVER['REQUEST_URI'], '?') : '/';
});

Paginator::currentPageResolver(function ($pageName = 'page') {
    return isset($_GET['page']) ? $_GET['page'] : 1;
});




// #ELOQUENT
// =========================================================================

require_once __DIR__ . '/database.php';




// #CARBON
// =========================================================================

$twig = $container->view->getEnvironment();

$date = new Twig_SimpleFunction('date', function ($date) {
    $date = Carbon::createFromFormat('Y-m-d H:i:s', $date, 'America/Toronto');

    return $date->diffForHumans();
});

$twig->addFunction($date);




// #TRUNCATE
// =========================================================================

$twig = $container->view->getEnvironment();

$excerpt = new Twig_SimpleFunction('excerpt', function($text) {
    if (str_word_count($text, 0) > 15) {
        $words = str_word_count($text, 2);
        $pos = array_keys($words);
        $preview = substr($text, 0, $pos[15]) . "...";
    } else {
        $preview = $text;
    }
    return $preview;
});

$twig->addFunction($excerpt);




// #EXISTS
// =========================================================================

$exists = new Twig_SimpleTest('exists', function($path) {
    if (file_exists(__DIR__ . '/../' . $path)) {
        return true;
    }

    return false;
});

$twig->addTest($exists);




// #VALIDATION
// =========================================================================

v::with('App\\Validation\\Rules\\');




// #API
// =========================================================================

require_once __DIR__ . '/../routes/api.php';




// #AUTH
// =========================================================================

require_once __DIR__ . '/../routes/auth.php';




// #WEB
// =========================================================================

require_once __DIR__ . '/../routes/web.php';
