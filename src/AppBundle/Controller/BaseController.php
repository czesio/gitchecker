<?php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Repository\RepositoryHandler;

abstract class BaseController extends Controller
{
    /**
     * Create acces to git api client and its functions
     *
     * @param array $params
     * @return RepositoryHandler
     */
    protected function createRepoHandlerAccess(array $params)
    {
        $repoHandler = new RepositoryHandler($params
            , (($this->container->hasParameter('git_api_token') &&  !is_null($this->getParameter('git_api_token'))) ? $this->getParameter('git_api_token') : null)
        );
        return $repoHandler;
    }
}