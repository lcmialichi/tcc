<?php

namespace Source\Model;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Session
{

     use \Source\Connection\DBConnect;
     /**
      * @var string hash banco de dados
      *
      */
     private $hashBD;

     /**
      * @var string hash sessao
      *
      */
     private $hashSession;


     /**
      * @var string data que expira hash
      *
      */
     private $hashExpire;

     /**
      * vem da hash
      * @var object dados do usuarios

      */
     private $userInfo;

     /**
      * usuario possui hash valida
      *@var bool
      *
      */
     private $permission = false;
     /**
      * inicia o bufering instacia a variavel global de sessao
      */
     public function __construct()
     {

          $header = apache_request_headers();
          $token = $header['Authorization'] ?? null;

          if (isset($token)) {

               $this->hashSession = $token;
               $this->userInfo = JWT::decode($token, new key(JWTKEY, 'HS256'));
          } else {

               throw new \Exception("informe um token", 401);
          }
     }

     /**
      * pega ultima hash salva no banco
      */
     public function getSessionHashFromBD()
     {

          $sessionBd = self::table("user_session")->select()->where("session_user_id", $this->userInfo->id)->one();
          if ($sessionBd !== false) {

               $this->hashBD = $sessionBd['session_key'];
               $this->hashExpire = $sessionBd['session_last_update'];
          } else {

               throw new \Exception("Sessao expirada", 401);
          }
     }

     /**
      * Verifica se hash do corretor no banco de dados é valida ainda
      */
     public function isValid()
     {
          if ($this->hashSession != $this->hashBD) {
               return false;
          } else if ($this->hashExpire < date("Y-m-d H:i:s")) {
               return false;
          } else if (is_null($this->hashSession) && empty($this->hashSession) && empty($this->hashBD)  && is_null($this->hashBD)) {
               return false;
          } else {
               $this->permission = true;
               return $this;
          }
     }

     public function updateExpire()
     {
          $data15Min = date("Y-m-d H:i:s", strtotime("- 15 minutes"));

          if ($this->hashExpire > $data15Min) {

               self::table("user_session")
                    ->update()
                    ->set("session_last_update", date("Y-m-d H:i:s", strtotime("+ 1 hour")))
                    ->where("session_user_id", $this->userInfo->id)->execute();
          }
     }

     public function getPermission()
     {
          return $this->permission;
     }
     /**
      * funcao responsavel por retornar id do usuario logado
      */
     public function getUser()
     {
          return $this->userInfo;
     }
}
