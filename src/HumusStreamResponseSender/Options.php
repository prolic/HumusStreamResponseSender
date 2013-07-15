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

use Zend\Stdlib\AbstractOptions;

/**
 * @category   Humus
 * @package    HumusStreamResponseSender
 * @license    MIT
 */
class Options extends AbstractOptions
{
    /**
     * @var bool
     */
    protected $enableRangeSupport = false;

    /**
     * @var bool
     */
    protected $enableSpeedLimit = false;

    /**
     * @var int
     */
    protected $chunkSize = 262144; //in bytes (this will also be your download speed limit in bytes per second)

    /**
     * Set chunk size in  bytes
     *
     * If enable speed limit is set to true, this will also be the speed limit in bytes per second
     *
     * @param int $chunkSize
     */
    public function setChunkSize($chunkSize)
    {
        $this->chunkSize = (int) $chunkSize;
    }

    /**
     * Get chunk size in bytes
     *
     * If enable speed limit is set to true, this will also be the speed limit in bytes per second
     *
     * @return int
     */
    public function getChunkSize()
    {
        return $this->chunkSize;
    }

    /**
     * Set enable range support
     *
     * @param bool $enableRangeSupport
     */
    public function setEnableRangeSupport($enableRangeSupport)
    {
        $this->enableRangeSupport = (bool) $enableRangeSupport;
    }

    /**
     * Get enable range support
     *
     * @return bool
     */
    public function getEnableRangeSupport()
    {
        return $this->enableRangeSupport;
    }

    /**
     * Set enable speed limit
     *
     * @param bool $enableSpeedLimit
     */
    public function setEnableSpeedLimit($enableSpeedLimit)
    {
        $this->enableSpeedLimit = (bool) $enableSpeedLimit;
    }

    /**
     * Get enable speed limit
     *
     * @return bool
     */
    public function getEnableSpeedLimit()
    {
        return $this->enableSpeedLimit;
    }
}
