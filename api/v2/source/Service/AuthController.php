<?php

namespace Source\Service;

use Source\Model\User;

/**
 * /api/user/authorization
 * 
 * Classe responsavel pelas autoriaçoes do usuario
 * 
 */
class authController
{

    use \Source\Session\Session; # Trait SessionController

    /**
     * Autenticaçao 
     * 
     * @param array $credenciais ["usuario" => string , "senha" => string] 
     * 
     */
    public function authenticate(array $credenciais)
    {

        if (!isset($credenciais['usuario']) || !isset($credenciais['senha'])) {
            throw new \Exception("Necessario informar usuario e senha.", 401);

        } else {

            $auth = new User;
            $auth->setCredencials($credenciais['usuario'], $credenciais['senha']);
            $auth->getAccess();
            $auth->encrypt();
            $auth->insertEncryptedSession();
            $userInfo = $auth->getUser();
            return [
                "status" => true,
                "message" => null,
                "data" => [
                    "jwt" => $auth->getAccesToken(),
                    "user" => [
                        "name" => $userInfo['user_name'],
                        "userType" => $userInfo['user_type']
                    ]

                ]
            ];
        }
    }
}
