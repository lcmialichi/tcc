<?php

namespace CoffeeCode\Router\Http;
use \Source\Log\Log;

class Response {

    const JSON_CONFIG = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;

    public function __construct( $route ){
        $this->route = $route['route'];
    }

    /**
     * Retorna funcao callback (chamada diretamente da rota)
     */
    public function createResponseCallback($handler, $args)
    {
        $this->userResponse(function() use ($handler, $args){
            return call_user_func($handler,$args);
        });          

    } 

    /**
    * Retorna funcao chada do controller
    */
    public function createControllerResponse($controller, $methode, $data)
    {
        $this->userResponse(function() use ($controller, $methode, $data){
            return $controller->$methode(($data));
        });
       
    }
    /**
     * Aqui sao aplicados as validaçoes de errors, separadores de logs, e formataçao de resposta ao cliente
     * @param callable $routeExec
     */
    public function userResponse( callable $routeExec ) : void
    {
        try{
           $retorno = $routeExec();

        }catch(\Exception $e){

            $httpCode = $e->getCode();
            $message = $e->getMessage();
            $httpCode =  is_string($httpCode) ? 422 : $httpCode;

            if(preg_match("/sql/i",$message) || $e instanceOf \PDOException){
                $message = "Ocorreu um erro no processamento de dados: $this->route";
            }

            http_response_code($httpCode);
            echo json_encode([ "status" => false, "message" => $message], self::JSON_CONFIG); 
            return;

        }catch(\Error $e){
            
            http_response_code(400);
            echo json_encode(["status" => false,"message" => "Ocorreu um erro durante a execuçao do serviço"], self::JSON_CONFIG); 
            return;

        }

        if(is_null($retorno)){
            echo json_encode(["status" => false,"message" => "Nao houve um retorno da api"], self::JSON_CONFIG); 
            return;

        }else{
            isset($retorno['status']) ?: $retorno = ['status' => true] + $retorno;        
            echo json_encode($retorno, self::JSON_CONFIG);
            return;

        }

         

    }

}

