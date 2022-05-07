<?php

namespace Source\Service;

use \Source\Api\CRN;

Class CrnController
{

    public function consult(array $param)
    {
        if (is_null($param['crn']))
        {
            throw new \Exception("Informe um CRN para consulta", 422);
        }

        $crn = new CRN;
        $crns =  $crn->getCRN($param['crn']);

        if($crns->success != true ||empty($crns->data))
        {
            throw new \Exception("Crn nao localizado", 422);

        }else{

            return [
                "data" => $crns->data
            ];
            
        }



    }
}