<?php

namespace Korobi\WebBundle\Controller\Internal;

use Korobi\WebBundle\Controller\BaseController;
use Korobi\WebBundle\Deployment\DeploymentInfo;
use Korobi\WebBundle\Deployment\DeploymentProcessor;
use Korobi\WebBundle\Document\Revision;
use Korobi\WebBundle\Util\Akio;
use Korobi\WebBundle\Util\GitInfo;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DeploymentController extends BaseController {

    /**
     * @var LoggerInterface Logger we can use.
     */
    private $logger;

    /**
     * @var string The root path (one up from kernel root).
     */
    private $rootPath;

    /**
     * @var string The HMAC secret used to ensure deploy requests from GitHub are valid.
     */
    private $hmacKey;

    /**
     * @var GitInfo
     */
    private $gitInfo;

    /**
     * @var Akio
     */
    private $akio;

    /**
     * @param LoggerInterface $logger
     * @param GitInfo $gitInfo
     */
    public function __construct(LoggerInterface $logger, GitInfo $gitInfo, Akio $akio) {
        $this->logger = $logger;
        $this->gitInfo = $gitInfo;
        $this->akio = $akio;
    }

    /**
     * @param mixed $rootPath
     */
    public function setRootPath($rootPath) {
        $this->rootPath = $rootPath . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
    }

    /**
     * @param $key
     */
    public function setHmacKey($key) {
        $this->hmacKey = $key;
    }

    public function deployAction(Request $request) {
        /** @var \Korobi\WebBundle\Document\User $user */
        $user = $this->getUser();
        $env = $this->getParameter('kernel.environment');
        $info = new DeploymentInfo($request, new Revision(), $user, $this->authChecker, $this->hmacKey, $this->rootPath, $env);
        $processor = new DeploymentProcessor($info, $this->logger, $this->container->get('kernel'), $this->akio,
            $this->get('doctrine_mongodb')->getManager(), $this->get("korobi.git_info"));
        $status = $processor->performDeployment();

        return new JsonResponse(['status' => $status]);
    }

    public function viewAction($id) {
        if (!$this->authChecker->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        /** @var Revision $deployment */
        $deployment = $this->get('doctrine_mongodb')
            ->getRepository('KorobiWebBundle:Revision')
            ->find($id);

        if ($deployment === null) {
            throw $this->createNotFoundException();
        }

        return $this->render('KorobiWebBundle:controller/internal:deployment.html.twig', [
            'doc' => $deployment,
        ]);
    }
}
