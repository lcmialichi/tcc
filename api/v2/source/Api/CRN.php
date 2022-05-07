<?php

namespace Source\Api;

class CRN
{
    const URLBASE = 'https://cnn.cfn.org.br/application/front-resource';

    /**
     * @param array $post payload
     * @param string $uri complemento da url
     * @return object
     */
    private function post(array $post, string $uri)
    {
        $header = [
            'Host: cnn.cfn.org.br',
            'User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:94.0) Gecko/20100101 Firefox/94.0',
            'Accept: application/json, text/plain, */*',
            'Accept-Language: pt-BR,pt;q=0.8,en-US;q=0.5,en;q=0.3',
            'Accept-Encoding: gzip, deflate, br',
            'Referer: https://cnn.cfn.org.br/application/index/consulta-nacional',
            'Content-Type: application/json;charset=utf-8',
            'Origin: https://cnn.cfn.org.br',
            'Connection: keep-alive',
        ];

        $opt = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($post),
            CURLOPT_AUTOREFERER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_URL => self::URLBASE . $uri,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $opt);
        return json_decode(curl_exec($ch));
    }

    /**
     * @param int $registerCrn
     */
    public function getCRN($registerCrn)
    {

        $post = [
            'comando' => 'get-nutricionista',
            'options' => [
                'registro' => $registerCrn,
                'geral' => true,
            ],
        ];

        return $this->post($post, '/get');
    }
}