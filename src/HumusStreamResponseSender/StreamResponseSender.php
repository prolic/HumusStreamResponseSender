<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

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
 * @license    MIT
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
    private $rangeStart;

    /**
     * @var int
     */
    private $rangeEnd;

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

        $size = $response->getContentLength();
        $size2 = $size - 1;

        $length = $size;
        $this->rangeStart = 0;
        $this->rangeEnd = null;

        $enableRangeSupport = $this->getOptions()->getEnableRangeSupport();
        $requestHeaders = $this->getRequest()->getHeaders();

        if ($enableRangeSupport && $requestHeaders->has('Range')) {
            list($a, $range) = explode('=', $requestHeaders->get('Range')->getFieldValue());
            if (substr($range, -1) == '-') {
                // range: 3442-
                $range = substr($range, 0, -1);
                if (!is_numeric($range) || $range > $size2) {
                    // 416 (Requested range not satisfiable)
                    $response->setStatusCode(416);
                    $event->setContentSent();
                    return $this;
                }
                $this->rangeStart = $range;
                $length = $size - $range;
            } else {
                $ranges = explode('-', $range, 2);
                $rangeStart = $ranges[0];
                $rangeEnd = $ranges[1];
                if (!is_numeric($rangeStart)
                    || !is_numeric($rangeEnd)
                    || ($rangeStart >= $rangeEnd)
                    || $rangeEnd > $size2
                ) {
                    // 416 (Requested range not satisfiable)
                    $response->setStatusCode(416);
                    $event->setContentSent();
                    return $this;
                }
                $this->rangeStart = $rangeStart;
                $this->rangeEnd = $rangeEnd;
                $length = $rangeEnd - $rangeStart;
                $size2 = $rangeEnd;
            }
            $response->setStatusCode(206); // 206 (Partial Content)
        }

        $responseHeaders->addHeaderLine('Content-Length: ' . $length);

        if ($enableRangeSupport) {
            $responseHeaders->addHeaders(
                array(
                    'Accept-Ranges: bytes',
                    'Content-Range: bytes ' . $this->rangeStart . '-' . $size2 . '/' . $size,
                )
            );
        } else {
            $responseHeaders->addHeaderLine('Accept-Ranges: none');
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
        $enableRangeSupport = $options->getEnableRangeSupport();
        $enableSpeedLimit = $options->getEnableSpeedLimit();

        // use fpassthru, if download speed limit and download resume are disabled
        if (!$enableRangeSupport && !$enableSpeedLimit) {
            fpassthru($stream);
            $event->setContentSent();
            return $this;
        }

        set_time_limit(0);
        $rangeStart = $this->rangeStart;
        if (null !== $this->rangeEnd) {
            $rangeEnd = $this->rangeEnd;
            $length = $rangeEnd-$rangeStart;
        } else {
            $length = $response->getContentLength();
        }

        fseek($stream, $rangeStart);
        $chunkSize = $options->getChunkSize();

        if ($chunkSize > $length) {
            $chunkSize = $length;
        }
        $sizeSent = 0;

        while (!feof($stream) && (connection_status()==0)) {

            echo fread($stream, $chunkSize);
            flush();

            $sizeSent += $chunkSize;

            if ($sizeSent == $length) {
                $event->setContentSent();
                return $this;
            }

            if ($enableSpeedLimit) {
                sleep(1);
            }
        }
    }
}
