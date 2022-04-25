<?php

namespace Source\Service\TraitsController;

use Source\Model\Session;

/**
 * Controle de sessao do usuario
 */
trait SessionController
{

    /**
     * @var object $session instancia
     */
    private $session;

    /**
     * Instancia o controle de sessao
     */
    public function sessionStart()
    {

        $this->session = new Session;
        $this->session->getSessionHashFromBD();
        $this->session->isValid();

        if ($this->session->getPermission() === true) {
            $this->session->updateExpire();
        } else {
            throw new \Exception("Sessao expirada!", 401);
        }
    }

    /**
     * retorna usuario logado
     */
    public function getUser()
    {
        return $this->session->getUser();
    }

    public static function getUserStatic()
    {
        $session = new Session;
        return $session->getUser();
    }
}
