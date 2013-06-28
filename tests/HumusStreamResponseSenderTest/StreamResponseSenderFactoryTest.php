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

use HumusStreamResponseSender\StreamResponseSenderFactory;

class StreamResponseSenderFactoryTest extends ServiceManagerTestCase
{
    public function testCreateService()
    {
        $serviceManager = $this->getServiceManager();
        $factory = new StreamResponseSenderFactory();
        $streamResponseSender = $factory->createService($serviceManager);

        $this->assertInstanceOf('HumusStreamResponseSender\StreamResponseSender', $streamResponseSender);
        $this->assertInstanceOf('Zend\Stdlib\RequestInterface', $streamResponseSender->getRequest());
    }

    public function testCreateServiceWithOptions()
    {
        $serviceManager = $this->getServiceManager();
        $config = $serviceManager->get('Config');
        $config['HumusStreamResponseSender'] = array(
            'enable_speed_limit' => true
        );
        $serviceManager->setAllowOverride(true);
        $serviceManager->setService('Config', $config);
        $serviceManager->setAllowOverride(false);

        $factory = new StreamResponseSenderFactory();
        $streamResponseSender = $factory->createService($serviceManager);

        $this->assertInstanceOf('HumusStreamResponseSender\StreamResponseSender', $streamResponseSender);
        $this->assertInstanceOf('Zend\Stdlib\RequestInterface', $streamResponseSender->getRequest());
        $this->assertTrue($streamResponseSender->getOptions()->getEnableSpeedLimit());
    }
}
