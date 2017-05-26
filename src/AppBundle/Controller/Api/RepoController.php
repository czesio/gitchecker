<?php
namespace AppBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Repository\RepositoryHandler;

class RepoController extends Controller
{
    /**
     *
     * @Route("/api/show", name="api_repo_show")
     * @Method("GET")
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Return results of comparision two git repositories",
     *  parameters={
     *      {"name"="repoNameOne", "dataType"="string", "required"=true, "description"="first repo to check"
     *          , "format"="repo_user/repo_name or http://github.com/repo_user/repo_name"},
     *      {"name"="repoNameTwo", "dataType"="string", "required"=true, "description"="second repo to check"
     *          , "format"="repo_user/repo_name or http://github.com/repo_user/repo_name"},
     *  },
     *  output="json"
     * )
     *
     */
    public function showAction(Request $request)
    {
        $repoNameOne = $request->get('repoNameOne');
        $repoNameTwo = $request->get('repoNameTwo');
        $params = array($repoNameOne, $repoNameTwo);
        if (strlen($repoNameTwo) && strlen($repoNameOne)) {
            $repoHandler = new RepositoryHandler($params
                , ($this->container->hasParameter('git_api_token') ? $this->getParameter('git_api_token') : null)
            );

            $comparisionData = $repoHandler->getComparisionData();
            if (empty($comparisionData)) {
                throw $this->createNotFoundException('Sorry, no repos at all');
            }
            return $this->prepareResponse($comparisionData);
        } else {
            throw $this->createNotFoundException('There is some repo name missing');
        }
    }


    /**
     * @param $comparisionData
     * @return Response
     */
    private function prepareResponse($comparisionData)
    {
        $serializer = $this->container->get('jms_serializer');
        $json = $serializer->serialize($comparisionData, 'json');
        $statusCode = 200;
        return new Response($json, $statusCode, array(
            'Content-Type' => 'application/json'
        ));
    }
}