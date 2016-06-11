<?php

use Symfony\Component\HttpFoundation\Request;
use Rorua\ArabicRootExtractor\Extractor;

require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();

$app['debug'] = true;

//Registering Twig provider
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

// Registering asset component
$app->register(new Silex\Provider\AssetServiceProvider(), array(
    'assets.version' => 'v1',
    'assets.version_format' => '%s?version=%s',
    'assets.named_packages' => array(
        'css' => array('version' => 'css2', 'base_path' => '/css'),
    ),
));

// Matching active page
$app->before(function ($request) use ($app) {
    $app['twig']->addGlobal('active', $request->get("_route"));
});


// Routes

$app->match('/', function(Request $request) use ($app) {

    $params = [];
    if ($request->isMethod("POST")) {
        $word = $request->get("word");
        if ($word !== null) {
            $parser = new Rorua\ArabicRootExtractor\Extractor();

            $results = $parser->process($word);
            $params = ['results' => $results];
        }
    }

    return $app['twig']->render('pages/home.twig', $params);

})->bind('home');

$app->get('/about', function() use ($app) {
    return $app['twig']->render('pages/about.twig');
})->bind('about');

$app->get('/contact', function() use ($app) {
    return $app['twig']->render('pages/contact.twig');
})->bind('contact');

$app->run();
