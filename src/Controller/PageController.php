<?php
// src/Controller/PageController.php
namespace App\Controller;

use App\Builder\GitBuilder;
use App\Service\GitHubService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PageController extends AbstractController
{
    private $gitBuilder;
    private $gitHubService;

    /**
     * PageController constructor.
     * @param GitHubService $gitHubService
     * @param GitBuilder $gitBuilder
     */
    public function __construct(GitHubService $gitHubService, GitBuilder $gitBuilder)
    {
        $this->gitHubService = $gitHubService;
        $this->gitBuilder = $gitBuilder;
    }

    /**
     * @return Response
     * @throws Exception
     */
    public function index(): Response
    {
        return $this->render('page/index.html.twig');
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function getUserData(Request $request): Response
    {
        if (!$request->get('name')) throw new Exception('An Error has Occurred! Empty name provided.');
        else if ($request->get('type') != "1" && $request->get('type') != "2") throw new Exception('An Error has Occurred! Bad user type provided.');

        $name = $request->get('name');
        $type = $request->get('type');

        switch ($type) {
            case 1:
                {
                    $userData = $this->gitBuilder->build($this->gitHubService, $name);
                }
                break;
            case 2:
                {
                    /*$userData = $this->gitBuilder->build($this->gitLavService, $name);*/
                    dd('no service currently created...');
                }
                break;
        }

        return $this->render('page/user.html.twig', $userData->__getUserData());
    }
}
