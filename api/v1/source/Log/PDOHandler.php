<?php namespace Source\Log;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

/**
 * classe responsavel por efetuar o Handler de inserçao de log em banco
 */
class PDOHandler extends AbstractProcessingHandler 
{
    use \Source\Connection\DBConnect;
    use \Source\Service\TraitsController\SessionController;
    
    private $initialized = false;
    private $user;
    private $statement;
    public function __construct( $level = Logger::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
    }

    protected function write(array $record): void
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        $log = self::table('log');
        $insert = [
            "log_user" => $this->user->id,
            "log_level" => $record['level_name'],
            "log_message" => $record['message'],
            "log_date" => $log->raw("NOW()")
        ];

        $log->insert($insert)->execute();
    }

    private function initialize()
    {   
        $this->user = self::getUserStatic();
        $this->initialize = true;
    }
}