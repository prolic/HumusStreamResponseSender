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

namespace HumusStreamResponseSender\Controller\Plugin;

use DateTime;
use HumusStreamResponseSender\Exception;
use Zend\Http\Headers;
use Zend\Http\Response\Stream as StreamResponse;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * @category   Humus
 * @package    HumusStreamResponseSender
 * @license    MIT
 */
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
     * @param $filename
     * @param string|null $basename
     * @param string|int $filesize
     * @param DateTime|null $lastModified
     * @return StreamResponse
     */
    public function binaryFile($filename, $basename = null, $filesize = null, DateTime $lastModified = null)
    {
        $response = new StreamResponse();

        // assume static file download
        if (file_exists($filename) && is_readable($filename)) {
            $resource = fopen($filename, 'rb');
            $response->setStream($resource);

            if (null === $basename) {
                $basename = basename($filename);
            }

            if (null === $filesize) {
                $filesize = filesize($filename);
            }

            if (null === $lastModified) {
                $lastModified = new DateTime();
                $lastModified->setTimestamp(filemtime($filename));
                $lastModified = $lastModified->format(DateTime::RFC1123);
            }
        }

        $response->setStreamName($basename);
        $response->setContentLength($filesize);

        $autoAddedHeaders = array(
            'Content-Disposition' => 'attachment; filename="' . $basename . '"',
            'Content-Type' => 'application/octet-stream',
        );

        if (null !== $lastModified) {
            $autoAddedHeaders['Last-Modified'] = $lastModified;
        }

        $headers = new Headers();
        $headers->addHeaders($autoAddedHeaders);
        $response->setHeaders($headers);
        return $response;
    }
}
