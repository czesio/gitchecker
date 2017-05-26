<?php
namespace AppBundle\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Github\Api\Repository;
use Github\Client;
use AppBundle\Form\GihubPackageComparisionFormType;
use AppBundle\Repository\RepositoryHandler;

class RepoController extends Controller
{
    /**
     *
     * @Route("/web", name="web_repo_index")
     */
    public function indexAction(Request $request)
    {
        return $this->redirectToRoute('web_repo_new');
    }

    /**
     *
     * @Route("/web/new", name="web_repo_new")
     */
    public function newComparisionAction(Request $request)
    {
        $form = $this->createForm(
            GihubPackageComparisionFormType::class,
            null,
            array('attr'=> array('class'=>'form-comparision'))
        );
        $form->handleRequest($request);
        $comparisionData = array();
        $isComparisionDataSet = false;
        if ($form->isValid()) {
            $data = $form->getData();
            $repoHandler = new RepositoryHandler(
                $data,
                ($this->container->hasParameter('git_api_token') ? $this->getParameter('git_api_token') : null)
            );
            $comparisionData = $repoHandler->getComparisionData();
            $isComparisionDataSet = true;
        }
        return $this->render('web/new.html.twig', array(
            'form' => $form->createView(),
            'isComparisionDataSet' => $isComparisionDataSet,
            'comparisionData' => $comparisionData
        ));
    }

}