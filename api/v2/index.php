<?php
require __DIR__ . "/vendor/autoload.php";

use CoffeeCode\Router\Router;

$router = new Router("");

/*** User */
$router->group("user")->namespace("Source\Service");
$router->post("/authenticate", "AuthController:authenticate");
/**Consulta CRN */

$router->group("crn");
$router->get("/get/{crn}", "CrnController:consult");

/** Erro */
$router->group("erro")->namespace('');
$router->get("/404", function () {
    throw new Exception("Endpoint nao encontrado", 404);
});

$router->dispatch();
$router->error();

/*** Erro de redirect */
if ($router->error()) {
    $router->redirect("curlrequest/tcc/api/v2/erro/{$router->error()}");
}
