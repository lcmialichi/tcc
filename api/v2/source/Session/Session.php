<?php

namespace Source\Session;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Source\Model\User;
use \Source\Connection\DBConnect as DB;
trait Session
{


    /**
     * @var string
     */
    private $hashSession;
    /**
     * @var object
     */
    private $userInfo;
    /**
     * @var string
     */
    private $hashDb;
    
    /**
     * **Instancia Sessao do usuario**
     */
    public function sessionStart()
    {
        $header = apache_request_headers();
        $token = $header['Authorization'];

        if (isset($token)) {
            $this->hashSession = $token;
            $this->userInfo = JWT::decode($token, new Key(JWTKEY, 'HS256'));
            $this->hashBD = User::getLastSession($this->userInfo->id);
            $this->updateExpire();

            if($this->sessionStatus()){
                return true;
            }

        } else {
            throw new \Exception("Necessario informar token", 401);

        }
    }

     /**
      * valida Sessao
      * @return bool
     */
    private function sessionStatus() : bool
    {    
         if(is_null($this->hashSession) || $this->hashSession == false || is_null($this->hashBD)){
            throw new \Exception("Nao foi estabelecido um token de sessao!", 401);

         }else if($this->hashBD['sessao_last_update'] < date("Y-m-d H:i:s")){
              throw new \Exception("token de sessao expirado!", 401);

         }else if($this->hashSession != $this->hashBD['sessao_key']) {
              throw new \Exception("Usuario nao autenticado!", 401);

         }else{
              return true;
         }

    }
    
    private function updateExpire()
    {
        $actualDate = new \DateTime("NOW");
        $hashDate  = new \DateTime($this->hashBD['sessao_last_update']);
        $interval = $hashDate->diff($actualDate)->format("%h");

         if( $interval < 1 ){
            DB::table("corretor_sessao")
                   ->update()
                   ->set("sessao_last_update", date("Y-m-d H:i:s", strtotime("+ 1 hour")))
                   ->where("sessao_id_corretor", $this->userInfo->id)->execute();
              
         }
    }

    public function getUser() : object
    {
        return $this->userInfo;
    }
}
