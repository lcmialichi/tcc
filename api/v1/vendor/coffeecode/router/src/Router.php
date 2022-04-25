<?php

namespace CoffeeCode\Router;

use CoffeeCode\Router\Http\Request;

/**
 * Class CoffeeCode Router
 * 
 * Alterada para comportar response ao cliente
 * 
 * 
 * **METODOS ADICIONADOS**
 * 
 * - @method response                            
 * {@example  adiciona um callback na execuçao do controller atual }
 * - @method setDefaultResponse
 * {@example adiciona um callback a todas as execuçoes de controllers 
 * menos aos que tiveram suas respostas pre definidas com o metodo "response"}
 * 
 * 
 * **ALERAÇOES**
 *  
 * - implementado requisiçao POST via JSON
 * 
 * @author Robson .V Leite
 * @author Lucas Mialichi :)
 */
class Router extends Dispatch
{
    /**
     * Router constructor.
     *
     * @param string $projectUrl
     * @param null|string $separator
     */
    public function __construct(string $projectUrl, ?string $separator = ":")
    {

        parent::__construct($projectUrl, $separator);
    }

    /**
     * @param string $route
     * @param $handler
     * @param string|null $name
     */
    public function post(string $route, $handler, string $name = null): object
    {

        $this->addRoute("POST", $route, $handler, $name);
        return $this;
    }

    /**
     * @param string $route
     * @param $handler
     * @param string|null $name
     */
    public function get(string $route, $handler, string $name = null): object
    {
        $this->addRoute("GET", $route, $handler, $name);
        return $this;
    }

    /**
     * @param string $route
     * @param $handler
     * @param string|null $name
     */
    public function put(string $route, $handler, string $name = null): object
    {
        $this->addRoute("PUT", $route, $handler, $name);
        return $this;
    }

    /**
     * @param string $route
     * @param $handler
     * @param string|null $name
     */
    public function patch(string $route, $handler, string $name = null): object
    {
        $this->addRoute("PATCH", $route, $handler, $name);
        return $this;
    }

    /**
     * @param string $route
     * @param $handler
     * @param string|null $name
     */
    public function delete(string $route, $handler, string $name = null): object
    {
        $this->addRoute("DELETE", $route, $handler, $name);
        return $this;
    }

    /**
     * Seta uma response para um endpoint especifico
     * @param callable $response
     */
    public function response(callable $response): void
    {
        if (in_array($this->httpMethod, array_keys($this->routes))) {
            $this->routes[$this->httpMethod][array_key_last($this->routes[$this->httpMethod])]["MiddlewareResponse"] = $response;
        }
    }

    /**
     * Seta uma response para todos os metodos exceto os ja definidos com o metodo response ^^
     * @param callable $response 
     */
    public function setDefaultResponse(callable $response): void
    {
        $this->defaultResponse = $response;
    }
}
