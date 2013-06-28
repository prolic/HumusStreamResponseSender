<?php

namespace HumusStreamResponseSender;

use Traversable;
use Zend\Http\Header\AcceptRanges;
use Zend\Http\Header\ContentLength;
use Zend\Http\Header\HeaderInterface;
use Zend\Http\Header\Range;
use Zend\Http\Request;
use Zend\Http\Response\Stream;
use Zend\Mvc\ResponseSender\SimpleStreamResponseSender;
use Zend\Mvc\ResponseSender\SendResponseEvent;

/**
 * @category   Humus
 * @package    HumusStreamResponseSender
 */
class StreamResponseSender extends SimpleStreamResponseSender
{
    /**
     * @var Options
     */
    protected $options;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var int
     */
    private $range;

    /**
     * @param array|Traversable|null|Options $options
     */
    public function __construct($options = null)
    {
        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * Set options
     *
     * @param array|Traversable|Options $options
     */
    public function setOptions($options)
    {
        if (!$options instanceof Options) {
            $options = new Options($options);
        }
        $this->options = $options;
        return $this;
    }

    /**
     * Get options
     *
     * @return Options
     */
    public function getOptions()
    {
        if (!$this->options instanceof Options) {
            $this->options = new Options();
        }
        return $this->options;
    }

    /**
     * Set request
     *
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Send HTTP headers
     *
     * @param  SendResponseEvent $event
     * @return StreamResponseSender
     */
    public function sendHeaders(SendResponseEvent $event)
    {
        /* @var $response Stream */
        $response = $event->getResponse();

        $responseHeaders = $response->getHeaders();
        if (!$responseHeaders->has('Content-Transfer-Encoding')) {
            $responseHeaders->addHeaderLine('Content-Transfer-Encoding', 'binary');
        }

        $responseHeaders->addHeaderLine('Accept-Ranges', 'bytes');

        $size = $response->getContentLength();
        $size2 = $size - 1;

        $requestHeaders = $this->getRequest()->getHeaders();

        if ($requestHeaders->has('Range')) {
            list($a, $range)=explode('=', $requestHeaders->get('Range')->getFieldValue());
            str_replace($range, "-", $range);
            $length = $size - $range;
            $response->setStatusCode(206);
            $responseHeaders->addHeaders(array(
                'Content-Length: ' . $length,
                'Content-Range: bytes ' . $range . $size2 . '/' . $size
            ));
            $this->range = (int) $range;
        } else {
            $responseHeaders->addHeaders(array(
                'Content-Range: bytes 0-' . $size2 . '/' . $size,
                'Content-Length: ' . $size
            ));
            $this->range = 0;
        }

        parent::sendHeaders($event);
    }

    /**
     * Send the stream
     *
     * @param SendResponseEvent $event
     * @return StreamResponseSender
     */
    public function sendStream(SendResponseEvent $event)
    {
        if ($event->contentSent()) {
            return $this;
        }
        $response = $event->getResponse();
        /* @var $response Stream */
        $stream = $response->getStream();

        $options = $this->getOptions();
        $enableDownloadResume = $options->getEnableDownloadResume();
        $enableSpeedLimit = $options->getEnableSpeedLimit();

        // use fpassthru, if download speed limit and download resume are disabled
        if (!$enableDownloadResume && !$enableSpeedLimit) {
            fpassthru($stream);
            $event->setContentSent();
            return $this;
        }

        set_time_limit(0);

        fseek($stream, $this->range);

        $chunkSize = $options->getChunkSize();

        while(!feof($stream) && (connection_status()==0))
        {
            echo fread($stream, $chunkSize);
            flush();
            ob_flush();

            if ($enableSpeedLimit) {
                sleep(1);
            }
        }

        $event->setContentSent();
        return $this;
    }
}
