<?php

namespace Korobi\WebBundle\Deployment\Processor;

use Korobi\WebBundle\Deployment\DeploymentInfo;
use Korobi\WebBundle\Deployment\DeploymentStatus;
use Korobi\WebBundle\Deployment\TestOutputParser;

/**
 * Runs the tests.
 *
 * @package Korobi\WebBundle\Deployment\Processor
 */
class RunTests extends BaseProcessor implements DeploymentProcessorInterface {

    public function handle(DeploymentInfo $deploymentInfo) {
        chdir($deploymentInfo->getRootPath() . DIRECTORY_SEPARATOR . 'app');
        $execOutput = [];
        $testOutput = exec('phpunit', $execOutput);
        $parsed = (new TestOutputParser())->parseLine($testOutput);
        $message = $this->akio->startMessage()->insertGreen()->insertText($parsed['passed'] . " tests passed.");



        if ($parsed['incomplete'] > 0) {
            $message = $message->insertYellow(" " . $parsed['incomplete'] . " skipped/risky tests.");
        }

        if ($parsed['failures'] > 0) {
            $this->logger->debug("Tests failed!", [implode("\n", $execOutput)], true);
            $message = $message->insertRed(" " . $parsed['failed'] . " failed.");
            $deploymentInfo->addStatus(DeploymentStatus::$TESTS_FAILED);
        }

        $this->akio->sendMessage($message);
        $this->akio->sendMessage(json_encode($parsed));
        $deploymentInfo->getRevision()->setTestsOutput(implode("\n", $execOutput));
        $deploymentInfo->getRevision()->setTestsPassed($parsed['failures'] === 0);
        $deploymentInfo->getRevision()->setTestsInfo($parsed);
        return parent::handle($deploymentInfo);
    }
}
