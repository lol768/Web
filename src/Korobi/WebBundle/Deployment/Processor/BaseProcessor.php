<?php

namespace Korobi\WebBundle\Deployment\Processor;

use Doctrine\ODM\MongoDB\DocumentManager;
use Korobi\WebBundle\Deployment\DeploymentInfo;
use Korobi\WebBundle\Deployment\DeploymentLogger;
use Korobi\WebBundle\Deployment\DeploymentStatus;
use Korobi\WebBundle\Util\Akio;
use Korobi\WebBundle\Util\AkioMessageBuilder;
use Korobi\WebBundle\Util\GitInfo;

/**
 * All processors extend this.
 *
 * @package Korobi\WebBundle\Deployment\Processor
 */
abstract class BaseProcessor implements DeploymentProcessorInterface {

    /**
     * @var DeploymentProcessorInterface
     */
    private $next;

    /**
     * @var DeploymentLogger
     */
    protected $logger;

    /**
     * @var Akio
     */
    protected $akio;

    /** @var GitInfo */
    protected $gitInfo;

    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var AkioMessageBuilder[] Holds a queue of messages to send all at once.
     */
    protected $messageQueue = [];

    public function __construct(DeploymentLogger $logger, Akio $akio, DocumentManager $dm, GitInfo $gi) {
        $this->logger = $logger;
        $this->akio = $akio;
        $this->dm = $dm;
        $this->gitInfo = $gi;
    }


    /**
     * @param DeploymentInfo $info
     * @return string Status of deployment
     */
    public function handle(DeploymentInfo $info) {
        if ($this->next !== null) {
            return $this->next->handle($info);
        }
        return DeploymentStatus::OK;
    }

    /**
     * @return DeploymentProcessorInterface The next class in the COR structure.
     */
    public function getNext() {
        return $this->next;
    }

    /**
     * @param $next DeploymentProcessorInterface The next class in the COR structure.
     */
    public function setNext($next) {
        $this->next = $next;
    }
}
