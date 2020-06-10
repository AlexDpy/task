<?php

namespace App\Controller\XmlRpc;

use Laminas\XmlRpc\Server;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class XmlRpcServer
{
    /**
     * @var XmlRpcApi
     */
    private $xmlRpcApi;

    public function __construct(XmlRpcApi $xmlRpcApi)
    {
        $this->xmlRpcApi = $xmlRpcApi;
    }

    /**
     * @Route("/xml-rpc-api", name="xml_rpc_api")
     */
    public function serve()
    {
        $server = new Server();
        $server->setClass($this->xmlRpcApi, 'exchangeRate');

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');

        ob_start();
        $server->handle();
        $response->setContent(ob_get_clean());

        return $response;
    }


}
