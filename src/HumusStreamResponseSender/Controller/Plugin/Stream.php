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
