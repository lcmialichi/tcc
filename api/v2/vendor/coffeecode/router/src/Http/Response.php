<?php

namespace CoffeeCode\Router\Http;

/**
 * Classe respopnsavel por tratar as mensagens de resposta ao cliente
 */
class Response {

     const JSON_CONFIG = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;

    public function __construct( $route ){

        $this->route = $route['route'];
    }

    public function createResponseCallback($handler, $args)
    {

        $this->userResponse(function() use ($handler, $args){
            return call_user_func($handler,$args);
        });          

    } 

    public function createControllerResponse($controller, $methode, $data)
    {
                    
        $this->userResponse(function() use ($controller, $methode, $data){
            return $controller->$methode(($data));
        });
       
    }

    
    public function userResponse( callable $routeExec ) : void
    {

        try{
            
           $retorno = $routeExec();
           if(is_null($retorno)){
                http_response_code(400);
                echo json_encode(["status" => false,"message" => "Nao houve um retorno da api"], self::JSON_CONFIG); 

            }else{
                isset($retorno['status']) ?: $retorno = ['status' => true] + $retorno;        
                echo json_encode($retorno, self::JSON_CONFIG);
                return;

            }

        }catch(\Exception $e){
            
            $httpCode = $e->getCode();
            $message = $e->getMessage();
            $httpCode =  is_string($httpCode) ? 422 : $httpCode;

            if(preg_match("/sql/i",$message) || $e instanceOf \PDOException ){
                // $message = "Ocorreu um erro no processamento de dados: $this->route";

            }else if($e instanceOf \UnexpectedValueException || $e instanceOf \InvalidArgumentException || $e instanceOf \DomainException){ 
                $message = "token de autenticaçao invalido";
                $httpCode = 401;
            }

            http_response_code($httpCode);
            echo json_encode(["status" => false,"message" => $message], self::JSON_CONFIG); 
            return;

        }catch(\Error $e){
 
            $message = $e->getMessage();
            http_response_code(400);
            echo json_encode(["status" => false,"message" => $message], self::JSON_CONFIG); 
            return;
        }

    }

}