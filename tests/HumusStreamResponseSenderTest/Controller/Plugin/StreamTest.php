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

namespace HumusStreamResponseSenderTest;

use HumusStreamResponseSender\Controller\Plugin\Stream;
use PHPUnit_Framework_TestCase as TestCase;

class StreamTest extends TestCase
{
    public function testBinaryFile()
    {
        $utt = new Stream();
        $filename = __DIR__ . '/../../TestAsset/sample-stream-file.txt';
        $filesize = filesize($filename);
        $basename = basename($filename);
        $lastModified = new \DateTime();
        $lastModified->setTimestamp(filemtime($filename));
        $lastModified = $lastModified->format(\DateTime::RFC1123);

        $response = $utt->binaryFile($filename);
        $this->assertInstanceOf('Zend\Http\Response\Stream', $response);
        $this->assertInternalType('resource', $response->getStream());
        $this->assertSame($basename, $response->getStreamName());
        $this->assertSame($filesize, $response->getContentLength());
        $headers = $response->getHeaders()->toArray();
        $expectedHeaders = array(
            'Content-Disposition' => 'attachment; filename="' . $basename . '"',
            'Content-Type' => 'application/octet-stream',
            'Last-Modified' => $lastModified
        );
        $this->assertSame($expectedHeaders, $headers);
    }
}
