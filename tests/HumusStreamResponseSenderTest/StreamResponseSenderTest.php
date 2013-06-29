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

use HumusStreamResponseSender\StreamResponseSender;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Http\Headers;
use Zend\Http\Response\Stream;

class StreamResponseSenderTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testSendHeadersAndStreamInDefaultMode()
    {
        if (!function_exists('xdebug_get_headers')) {
            $this->markTestSkipped('Xdebug extension needed, skipped test');
        }

        $testFile = __DIR__ . '/TestAsset/sample-stream-file.txt';
        $fileSize = filesize($testFile);
        $basename = basename($testFile);
        $stream = fopen($testFile, 'rb');

        $headers = array(
            'Content-Disposition: attachment; filename="' . $basename . '"',
            'Content-Type: application/octet-stream',
        );

        $response = new Stream();
        $response->setStream($stream);
        $response->setContentLength($fileSize);
        $response->setStreamName($basename);
        $response->getHeaders()->addHeaders($headers);


        $mockSendResponseEvent = $this->getMock(
            'Zend\Mvc\ResponseSender\SendResponseEvent',
            array('getResponse')
        );
        $mockSendResponseEvent
            ->expects($this->any())
            ->method('getResponse')
            ->will($this->returnValue($response));

        $requestMock = $this->getMockForAbstractClass('Zend\Http\Request');

        $responseSender = new StreamResponseSender();
        $responseSender->setRequest($requestMock);

        ob_start();
        $responseSender($mockSendResponseEvent);
        $body = ob_get_clean();

        $this->assertEquals(file_get_contents($testFile), $body);

        $expectedHeaders = array_merge(
            $headers,
            array(
                'Content-Transfer-Encoding: binary',
                'Accept-Ranges: bytes',
                'Content-Range: bytes 0-' . ($fileSize - 1) . '/' . $fileSize,
                'Content-Length: ' . $fileSize
            )
        );

        $sentHeaders = xdebug_get_headers();
        $diff = array_diff($sentHeaders, $expectedHeaders);

        if (count($diff)) {
            $header = array_shift($diff);
            $this->assertContains('XDEBUG_SESSION', $header);
            $this->assertEquals(0, count($diff));
        }
    }
}
