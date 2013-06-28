<?php

namespace HumusStreamResponseSender\Controller\Plugin;

use HumusStreamResponseSender\Exception;
use Zend\Http\Response\Stream as StreamResponse;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

class Stream extends AbstractPlugin
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var StreamResponse
     */
    protected $response;

    /**
     * Set request
     *
     * @param RequestInterface $request
     * @return Stream
     */
    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Get request
     *
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set response
     *
     * @param ResponseInterface $response
     * @return Stream
     * @throws Exception\InvalidArgumentException
     */
    public function setResponse(ResponseInterface $response)
    {
        if (!$response instanceof StreamResponse) {
            throw new Exception\InvalidArgumentException(
                'Response must be an instance of Zend\Http\Response\Stream'
            );
        }
        $this->response = $response;
        return $this;
    }

    /**
     * Get response
     *
     * @return StreamResponse
     */
    public function getResponse()
    {
        if (!$this->response instanceof StreamResponse) {
            $this->response = new StreamResponse();
        }
        return $this->response;
    }
}
