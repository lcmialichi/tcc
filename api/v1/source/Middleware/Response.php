<?php

namespace Source\Middleware;

use Source\Log\Log;

/**
 * Classe responsavel pelas respostas da API ao Cliente
 */
class Response
{

    use \Source\Service\TraitsController\SessionController;

    const JSON_CONFIG = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;

    /**
     * Methodo principal de retorno ao cliente
     */
    public static function defaultResponse(callable $routeExec, ?object $route = null): void
    {
        try {
            $retorno = $routeExec();
        } catch (\Exception $e) {

            $httpCode = $e->getCode();
            $message = $e->getMessage();
            $httpCode =  is_string($httpCode) ? 422 : $httpCode;

            Log::setDebug(msg: $message, user: (array) Self::getUserStatic());

            if (preg_match("/sql/i", $message) || $e instanceof \PDOException) {
                $message = "Ocorreu um erro no processamento de dados";
            }

            http_response_code($httpCode);
            echo json_encode(["status" => false, "message" => $message], self::JSON_CONFIG);
            return;
        } catch (\Error $e) {
            Log::setCritical($e->getMessage());
            http_response_code(400);
            echo json_encode(["status" => false, "message" => "Ocorreu um erro durante a execuçao do serviço"], self::JSON_CONFIG);
            return;
        }

        Log::setDebug(msg: $_REQUEST['route'], user: (array) Self::getUserStatic());

        if (is_null($retorno)) {
            echo json_encode(["status" => false, "message" => "Nao houve um retorno da api"], self::JSON_CONFIG);
            return;
        } else {
            isset($retorno['status']) ?: $retorno = ['status' => true] + $retorno;
            echo json_encode($retorno, self::JSON_CONFIG);
            return;
        }
    }

    /**
     * Resposta utilizada apenas durante a autenticaçao do usuario
     * @param callable $routeExec
     */
    public static function authResponse(callable $routeExec): void
    {

        try {
            $retorno = $routeExec();
        } catch (\Exception $e) {

            $httpCode = $e->getCode();
            $message = $e->getMessage();
            $httpCode =  is_string($httpCode) ? 422 : $httpCode;

            Log::authorization($message);

            if (preg_match("/sql/i", $message) || $e instanceof \PDOException) {
                $message = "Ocorreu um erro no processamento de dados";
            }
            http_response_code($httpCode);
            echo json_encode(["status" => false, "message" => $message], self::JSON_CONFIG);
            return;
        } catch (\Error $e) {
            Log::setCritical($e->getMessage());
            http_response_code(400);
            echo json_encode(["status" => false, "message" => "Ocorreu um erro durante a execuçao do serviço"], self::JSON_CONFIG);
            return;
        }

        Log::authorization("Acesso permitido");
        isset($retorno['status']) ?: $retorno = ['status' => true] + $retorno;
        echo json_encode($retorno, self::JSON_CONFIG);
        return;
    }

    /**
     * Resposta utilizada apenas durante a autenticaçao do usuario
     * @param callable $routeExec
     */
    public static function redirect(callable $routeExec): void
    {

        try {
            $retorno = $routeExec();
        } catch (\Exception $e) {

            $httpCode = $e->getCode();
            $message = $e->getMessage();
            $httpCode =  is_string($httpCode) ? 422 : $httpCode;

            if (preg_match("/sql/i", $message) || $e instanceof \PDOException) {
                $message = "Ocorreu um erro no processamento de dados";
            }
            http_response_code($httpCode);
            echo json_encode(["status" => false, "message" => $message], self::JSON_CONFIG);
            return;
        } catch (\Error $e) {

            http_response_code(400);
            echo json_encode(["status" => false, "message" => "Ocorreu um erro durante a execuçao do serviço"], self::JSON_CONFIG);
            return;
        }

        isset($retorno['status']) ?: $retorno = ['status' => true] + $retorno;
        echo json_encode($retorno, self::JSON_CONFIG);
        return;
    }
}
