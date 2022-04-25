<?php

namespace Source\Service;

// header('Content-Type: text/plain; charset=utf-8');

use \Source\Collection;

class CrnLocate
{
    use \Source\Service\TraitsController\SessionController;
    use \Source\Connection\DBConnect;

    public function getCRN(array $params)
    {
        $this->sessionStart();
        $crn = new Collection\CRN;
        $return = $crn->getCRN($params['crn']);

        if (empty($return->data)) {
            throw new \Exception("Nutricionista nao identificado", 422);
        } else {
            return [
                "data" => $return->data
            ];
        }
    }
}
