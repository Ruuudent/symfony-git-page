<?php
// src/Service/LabService.php
namespace App\Service;

use App\Entity\User;
use App\Interfaces\GitInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GitHubService implements GitInterface
{
    const gitAPI = 'https://api.github.com';

    private $client;
    private $userName;
    private $parameters;

    /**
     * LabService constructor.
     * @param HttpClientInterface $client
     * @param ParameterBagInterface $parameters
     */
    public function __construct(HttpClientInterface $client, ParameterBagInterface $parameters)
    {
        $this->client = $client;
        $this->parameters = $parameters;
    }

    public function _setUserName(string $name): void {
        $this->userName = $name;
    }

    public function _getUserName(): string {
        return $this->userName;
    }

    /**
     * @return string
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getUserData(): User
    {
        // Getting basic user information
        $userData = json_decode($this->client->request("GET", self::gitAPI . '/users/' . $this->_getUserName(),
            ['auth_basic' => $this->parameters->get('app.github.auth')])->getContent());

        //Getting Repos Information --- getting only 10 to limit de data for test only
        $reposData = json_decode($this->client->request("GET", $userData->repos_url . '?per_page=10',
            ['auth_basic' => $this->parameters->get('app.github.auth')])->getContent());
        $reposTotalNumber = $userData->public_repos;

        $reposLanguageDetails = [];

        //Parsing each repo for additional data
        foreach($reposData as $repo)
        {
            // Setting the json encode to associative to return in array format
            $languages = json_decode($this->client->request("GET", $repo->languages_url, ['auth_basic' => $this->parameters->get('app.github.auth')])->getContent(), true);
            $totalNumberOfLines = array_sum($languages);
            $languagePercentages = [];

            // Calculating percentage for language ussage
            foreach($languages as $languageName => $languageValue)
            {
                $languagePercentages[$languageName] = number_format( $languageValue / $totalNumberOfLines * 100, 4 );
            }

            // Also getting main Language
            $reposLanguageDetails[$repo->name] = [
                'main_language' => array_keys($languages, max($languages)),
                'language_percentages' => []
            ];
        }

        //Get other kind of information
        $profile = $userData->html_url;
        $website = $userData->blog;
        $repos = [
            'reposData' => $reposData,
            'reposLanguageDetails' => $reposLanguageDetails,
            'reposTotalNumber' => $reposTotalNumber,
        ];

        // if user would have been a real entity validations would be present here before creation
        $user = new User();
        $user->__setProfile($profile);
        $user->__setWebsite($website);
        $user->__setRepos($repos);

        return $user;
    }
}
