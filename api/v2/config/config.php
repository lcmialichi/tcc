<?php

define("DB_CONFIG",[
    "host" => '127.0.0.1',
    "username" => 'root', 
    "password" => '2026',
    "dbname" => 'TCC'
]);


/**
 *  Configuraçoes da instancia PDO
*/
define("PDO_CONFIG", "mysql:host=127.0.0.1;dbname=TCC;charset=utf8");

/**
 *  define se é usuario beta ou alpha
*/
define("BETA_OR_ALPHA", "corretor_bloqueio");

/**
 * Tempo que o cliente ficara travado com o corretor: 2 dias
 */
define("CLIENT_DATE_LOCK", date("Y-m-d H:i:s", strtotime("- 2 day"))); 

/**
 * JWT Key access
 */

 define("JWTKEY", "dahdhuduhaiu");