<?php
// src/Controller/PageController.php
namespace App\Controller;

use App\Service\GitHubService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PageController extends AbstractController
{
    private $gitHubService;

    public function __construct(GitHubService $gitHubService)
    {
        $this->gitHubService = $gitHubService;
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
        if(!$request->get('name')) throw new Exception('An Error has Occurred! Empty name provided.');
        else if($request->get('type') != "1" && $request->get('type') != "2") throw new Exception('An Error has Occurred! Bad user type provided.');

        $name = $request->get('name');
        $type = $request->get('type');

        switch ($type) {
            case 1: {
                $this->gitHubService->_setUserName($name);
                try {
                    $userData = $this->gitHubService->getServiceData();
                } catch (ClientExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface | TransportExceptionInterface $e) {
                    throw new Exception($e);
                }
            } break;
            case 2: {
              dd('no service currently created...');
            } break;
        }

        $profile = $userData->__getProfile();
        $website = $userData->__getWebsite();
        $repos = $userData->__getRepos();

        return $this->render('page/user.html.twig', [
            'profile' => $profile,
            'website' => $website,
            'repos' => $repos
        ]);
    }
}
