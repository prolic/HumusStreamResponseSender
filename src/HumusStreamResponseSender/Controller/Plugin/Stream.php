<?php

namespace HumusStreamResponseSender\Controller\Plugin;

use HumusStreamResponseSender\Exception;
use Zend\Http\Headers;
use Zend\Http\Response\Stream as StreamResponse;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class Stream extends AbstractPlugin
{

    /**
     * Returns a stream response for a binary file download
     *
     * It uses the status code 206 (Partial Content)
     * It generates the following headers automatically:
     *
     * Content-Disposition: 'attachment; filename="[basename of your filename argument]"
     * Content-Type: application/octet-stream
     *
     * Sample usage in controller:
     *
     * return $this->plugin('stream')->binaryFile('/path/to/my/file');
     *
     *
     * @param string $filename
     * @return StreamResponse
     * @throws Exception\RuntimeException
     */
    public function binaryFile($filename)
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            throw new Exception\RuntimeException('Invalid filename given; not readable or does not exist');
        }

        $resource = fopen($filename, 'rb');
        $basename = basename($filename);

        $response = new StreamResponse();
        $response->setStream($resource);
        $response->setStatusCode(206);

        $response->setStreamName($basename);
        $response->setContentLength(filesize($filename));

        $headers = new Headers();
        $headers->addHeaders(array(
            'Content-Disposition' => 'attachment; filename="' . $basename . '"',
            'Content-Type' => 'application/octet-stream',

        ));
        $response->setHeaders($headers);
        return $response;
    }
}
