<?php

namespace HumusStreamResponseSender;

use Zend\Stdlib\AbstractOptions;

/**
 * @category Humus
 * @package HumusStreamResponseSender
 */
class Options extends AbstractOptions
{
    /**
     * @var bool
     */
    protected $enableDownloadResume = false;

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
     * Set enable download resume
     *
     * @param bool $enableDownloadResume
     */
    public function setEnableDownloadResume($enableDownloadResume)
    {
        $this->enableDownloadResume = (bool) $enableDownloadResume;
    }

    /**
     * Get enable download resume
     *
     * @return bool
     */
    public function getEnableDownloadResume()
    {
        return $this->enableDownloadResume;
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
