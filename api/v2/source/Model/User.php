<?php

namespace Source\Model;

use Firebase\JWT\JWT;

/**
 * Autenticaçao de usuario
 */
class User
{

    use \Source\Connection\DBConnect;

    /**
     * @var object $credencials
     */
    private object $credencials;

    /**
     * @var array $userInfo
     */
    private $userInfo;

    public function setCredencials(string $login, string $password): void
    {
        $this->credencials = new \stdClass;
        $this->credencials->login = $login;
        $this->credencials->password = $password;
        return;
    }

    public function getAccess(): User
    {

        $return = self::table("user")
            ->select()
            ->where("user_login", $this->credencials->login)
            ->where("user_password", $this->credencials->password)
            ->one();

        if ($return) {

            $this->userInfo =  $return;
            return $this;
        } else {

            throw new \Exception("Usuario ou senha incorretos", 401);
        }
    }

    public function encrypt()
    {
        $preJWT = [
            "id" => $this->userInfo['user_id'],
            "type" => $this->userInfo['user_type'],
            "expire" => date("Y-m-d H:i:s", strtotime("+ 2 hour"))
        ];

        $this->accessToken = JWT::encode($preJWT, JWTKEY, 'HS256');
    }

    public function  insertEncryptedSession()
    {
        $table =  self::table("user_session");
        $payload = [
            "session_user_id" => $this->userInfo['user_id'],
            "session_last_update" => $table->raw('(NOW() + interval 3 hour)'),
            "session_key" => $this->accessToken
        ];

        $v = $table->select()->where("session_user_id", $this->userInfo['user_id'])->count();
        if ($v === 0) {
            return $table->insert($payload)->execute();
        } else {
            return $table->update($payload)->where("session_user_id", $this->userInfo['user_id'])->execute();
        }
    }

    public function getAccesToken()
    {
        return $this->accessToken;
    }

    public function getUser()
    {
        return $this->userInfo;
    }
}
