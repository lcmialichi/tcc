<?php
require __DIR__ . "/vendor/autoload.php";

use CoffeeCode\Router\Router;

$router = new Router("");

$router->setDefaultResponse(function ($exec) {
    return  Source\Middleware\Response::defaultResponse($exec);
});

/*** User */
$router->group("user")->namespace("Source\Service");
$router->post("/authenticate", "AuthController:authenticate")->response(function ($exec) {
    return Source\Middleware\Response::authResponse($exec);
});

/**Consulta CRN */

$router->group("crn");
$router->get("/get/{crn}", "CrnLocate:getCRN");

/** Erro */
$router->group("erro")->namespace('');
$router->get("/404", function () {
    throw new Exception("Endpoint nao encontrado", 404);
})->response(function ($exec) {
    return Source\Middleware\Response::redirect($exec);
});

$router->dispatch();
$router->error();

/*** Erro de redirect */
if ($router->error()) {
    $router->redirect("curlrequest/tcc/api/v1/erro/{$router->error()}");
}
