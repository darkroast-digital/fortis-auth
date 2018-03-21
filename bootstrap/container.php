<?php

/*
|--------------------------------------------------------------------------
| #CONTAINER
|--------------------------------------------------------------------------
*/

// #BOOT CONTAINER
// =========================================================================

$container = $app->getContainer();

// #AUTH
// =========================================================================

$container['auth'] = function ($container) {
    return new \App\Auth\Auth;
};

// #RESOURCES
// =========================================================================

$container['resources'] = function ($container) {
    return new \App\Resources\Resources('assets/uploads/resources/');
};

// #URL
// =========================================================================

$container['url'] = function ($container) {
    return $container->settings['url'];
};

// #VIEWS
// =========================================================================

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../resources/views', [
        'cache' => $container->settings['views']['cache']
    ]);

    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));
    $view->getEnvironment()->addGlobal('current_path', $container['request']->getUri()->getPath());

    $view->getEnvironment()->addGlobal('auth', [
        'check' => $container->auth->check(),
        'user' => $container->auth->user(),
        'admin' => $container->auth->admin(),
    ]);

    $view->getEnvironment()->addGlobal('url', $container->settings['site']['url']);

    $view->getEnvironment()->addGlobal('flash', $container['flash']);

    return $view;
};

if (getenv('PHP_ERRORS') !== 'true') {
    $container['errorHandler'] = function ($container) {
        return function ($request, $response, $exception) use ($container) {
            return $container['view']->render($response->withStatus(500), 'errors/error.twig');
        };
    };
    $container['phpErrorHandler'] = function ($container) {
        return function ($request, $response, $error) use ($container) {
            return $container['view']->render($response->withStatus(500), 'errors/error.twig');
        };
    };
}

// #MAIL
// =========================================================================

$container['mail'] = function ($container) {
    $config = $container->settings['mail'];

    $mail = new PHPMailer;

    return (new App\Mail\Mailer\Mailer($mail, $container->view))->alwaysFrom($config['from']['address'], $config['from']['name']);
};

// #VALIDATION
// =========================================================================

$container['validator'] = function ($container) {
    return new App\Validation\Validator;
};

$app->add(new \App\Middleware\ValidationErrorsMiddleware($container));

// #REMEBER ME
// =========================================================================

$app->add(new \App\Middleware\RememberMeMiddleware($container));

// #MARKDOWN
// =========================================================================

$container['markdown'] = function ($container) {
    return new Parsedown();
};

// #SLUGIFY
// =========================================================================

$container['slug'] = function ($container) {
    return new Cocur\Slugify\Slugify();
};

// #OLD INPUTS
// =========================================================================

if (!isset($_SESSION['old'])) {
    $_SESSION['old'] = [];
}

$app->add(new \App\Middleware\OldInputMiddleware($container));

// #FLASH
// =========================================================================

$container['flash'] = function ($container) {
    return new \Slim\Flash\Messages;
};

// #CSRF
// =========================================================================

// $container['csrf'] = function ($container) {
//     return new \Slim\Csrf\Guard;
// };

// $app->add(new \App\Middleware\CsrfViewMiddleware($container));

// $app->add($container->csrf);
