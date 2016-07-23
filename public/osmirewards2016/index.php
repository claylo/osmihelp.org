<?php

/**
 * libs we'll use here
 */
use Aura\SqlQuery\QueryFactory;
use Monolog\Logger;
use Monolog\Handler\ErrorLogHandler;
use Noodlehaus\Config;
use Slim\App;
use Psr7Middlewares\Middleware\TrailingSlash;
use Aura\Sql\ExtendedPdo;
use Aura\SqlQuery;

define('APPLICATION_PATH', dirname(__DIR__) . '/../osmirewards2016');
setlocale(LC_MONETARY, 'en_US');
date_default_timezone_set('UTC');

/**
 * We use the composer autoloader for everything
 */
require APPLICATION_PATH . "/../vendor/autoload.php";

$dotenv = new Dotenv\Dotenv(APPLICATION_PATH . '/../');
$dotenv->load();

if (!defined('SLIM_MODE')) {
    $mode = getenv('SLIM_MODE') ? getenv('SLIM_MODE') : 'production';
    define('SLIM_MODE', $mode);
}

/**
 * init a session
 */
session_start();

$config = new Config(array(
    APPLICATION_PATH . '/app/config/global.php',
    APPLICATION_PATH . '/app/config/' . SLIM_MODE . '.php',
));

// Create Slim app
$app = new App(['settings' => $config->all()]);

// middleware setup
$app->add(new TrailingSlash(false)); // true adds the trailing slash (false removes it)

// Fetch DI Container
$container = $app->getContainer();

/*
 * set up Monolog
 */
$log = new Logger('GraphStoryCom');
$log->pushHandler(new ErrorLogHandler(
    ErrorLogHandler::OPERATING_SYSTEM,
    $container->get('settings')['monolog.level']
));
$container['logger'] = $log;


/**
 * set Twig parser options
 *
 * @param \Interop\Container\ContainerInterface $c
 *
 * @return \Slim\Views\Twig
 */
$container['view'] = function ($c) {
    /** @var \Slim\Http\Request $request */
    $request = $c['request'];
    /** @var \Slim\Router $router */
    $router = $c['router'];
    $view = new \Slim\Views\Twig(
        $c->get('settings')['twig_templates_path'],
        [
            'cache' => $c->get('settings')['twig_templates_cache_path'],
            'debug' => $c->get('settings')['twig_debug'],
        ]
    );

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $request->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($router, $basePath));
    $view->addExtension(new Twig_Extensions_Extension_Text());
    if ($c->get('settings')['twig_debug']) {
        $view->addExtension(new Twig_Extension_Debug());
    }

    return $view;
};

// Register provider
$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};

$container['pdo'] = function($c) {
    $pdo = new ExtendedPdo(
        $c->get('settings')['db']['connection'],
        $c->get('settings')['db']['username'],
        $c->get('settings')['db']['password']
    );
    return $pdo;
};

$app->get('/', function (\Slim\Http\Request $request, \Slim\Http\Response $response, array $args) {
    $getParams = $request->getQueryParams();

    /** @var ExtendedPdo $pdo */
    $pdo = $this->get('pdo');
    $stm = "SELECT * FROM indiegogo WHERE Email = :email";
    $bind = ['email' => $getParams['email']];
    $sth = $pdo->perform($stm, $bind);
    $result = $sth->fetch(PDO::FETCH_ASSOC);

    /** @var \Slim\Flash\Messages $flash */
    $flash = $this->get('flash');


    $this->view->render(
        $response,
        'pages/osmirewards2016/index.twig',
        $getParams + ['result' => $result, 'formAction' => $this->router->pathFor('postForm'), 'flash' => $flash->getMessages()]
    );
})->setName('getForm');

$app->post('/', function (\Slim\Http\Request $request, \Slim\Http\Response $response, array $args) {
    $postParams = $request->getParsedBody();
    /** @var \Slim\Flash\Messages $flash */
    $flash = $this->get('flash');

    try {
        \Assert\lazy()
            ->that($postParams['email'], 'email')->notEmpty('Email must be provided')->email('Email must be valid')
            ->verifyNow();
    } catch (Assert\LazyAssertionException $e) {
        $flash->addMessage('error', $e->getMessage());
        return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('getForm').'?' . http_build_query(['email' => $postParams['email']]));
    }

    /** @var ExtendedPdo $pdo */
    $pdo = $this->get('pdo');
    $stm = "SELECT * FROM indiegogo WHERE Email = :email";
    $bind = ['email' => $postParams['email']];
    $sth = $pdo->perform($stm, $bind);
    $result = $sth->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        $flash->addMessage('error', 'No entry found for that email');
        return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('getForm').'?' . http_build_query(['email' => $postParams['email']]));
    }

    try {
        if ($result['Perk ID'] == 3613098) {
            \Assert\lazy()
                ->that($postParams, 'Shirt Type')
                    ->keyExists('shirtType', 'You must select a shirt type')
                        ->notEmptyKey('shirtType', 'You must select a shirt type')
                ->that($postParams, 'Shirt Size')
                    ->keyExists('shirtSize', 'You must select a shirt size')
                        ->notEmptyKey('shirtSize', 'You must select a shirt size')
                ->verifyNow();

        } elseif ($result['Perk ID'] == 3633662) {
            \Assert\lazy()
                ->that($postParams, 'Hoodie Size')
                    ->keyExists('hoodieSize', 'You must select a hoodie size')
                        ->notEmptyKey('hoodieSize', 'You must select a hoodie size')
                ->verifyNow();
        }
    } catch (Assert\LazyAssertionException $e) {
        /** @var \Assert\InvalidArgumentException[] $errorExceptions */
        $errorExceptions = $e->getErrorExceptions();
        foreach ($errorExceptions as $errorException) {
            $flash->addMessage('error', $errorException->getMessage());
        }
        return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('getForm').'?' . http_build_query(['email' => $postParams['email']]));
    } catch (\Exception $e) {
        $flash->addMessage('error', $e->getMessage());
        return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('getForm').'?' . http_build_query(['email' => $postParams['email']]));
    }

    /*
     * Save results and redirect
     */
    try {
        $qf = new QueryFactory('mysql');
        /** @var SqlQuery\Mysql\Update $update */
        $update = $qf->newUpdate();
        $update->table('indiegogo')
            ->where('Email = :email')
            ->bindValue('email', $postParams['email']);
        if (!empty($postParams['shirtType'])) {
            $update
                ->set('shirtType', ':shirtType')
                ->bindValue('shirtType', $postParams['shirtType']);
        }
        if (!empty($postParams['shirtSize'])) {
            $update
                ->set('shirtSize', ':shirtSize')
                ->bindValue('shirtSize', $postParams['shirtSize']);
        }
        if (!empty($postParams['hoodieSize'])) {
            $update
                ->set('hoodieSize', ':hoodieSize')
                ->bindValue('hoodieSize', $postParams['hoodieSize']);
        }
        /** @var ExtendedPdo $pdo */
        $pdo = $this->get('pdo');
        $stm = $update->getStatement();
        $sth = $pdo->perform($stm, $update->getBindValues());
        $affectedRows = $sth->rowCount();

        if ($affectedRows < 1) {
            $flash->addMessage('error', 'Problem updating: no rows affected');
            return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('getForm').'?' . http_build_query(['email' => $postParams['email']]));
        }
    } catch(\Exception $e) {
        $flash->addMessage('error', 'Problem updating: exception thrown when updating');
        return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('getForm').'?' . http_build_query(['email' => $postParams['email']]));
    }

    $flash->addMessage('info', 'Preferences saved. We will ship your perk ASAP!');
    return $response->withStatus(302)->withHeader('Location', $this->router->pathFor('getForm').'?' . http_build_query(['email' => $postParams['email']]));

})->setName('postForm');

$app->run();
