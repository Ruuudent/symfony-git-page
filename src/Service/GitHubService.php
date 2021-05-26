<?php
// src/Service/LabService.php
namespace App\Service;

use App\Entity\User;
use App\Interfaces\GitInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface as ClientExceptionInterfaceAlias;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GitHubService implements GitInterface
{
    const gitAPI = 'https://api.github.com';

    private $client;
    private $userName;
    private $parameters;
    private $userData;
    private $reposData;
    private $orgsData;

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

    /**
     * @param string $name
     */
    public function _setUserName(string $name): void
    {
        $this->userName = $name;
    }

    /**
     * @return string
     */
    public function _getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @throws ClientExceptionInterfaceAlias
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function _setUserData(): void
    {
        $this->userData = json_decode($this->client->request("GET", self::gitAPI . '/users/' . $this->_getUserName(),
            ['auth_basic' => $this->parameters->get('app.github.auth')])->getContent());
    }

    /**
     * @return \stdClass
     */
    public function _getUserData(): \stdClass
    {
        return $this->userData;
    }

    /**
     * @throws ClientExceptionInterfaceAlias
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function _setReposData(): void
    {
        $reposData = $reposLanguageDetails = [];

        $reposRaw = json_decode($this->client->request("GET", $this->_getUserData()->repos_url . '?per_page=10',
            ['auth_basic' => $this->parameters->get('app.github.auth')])->getContent());

        //Parsing each repo for additional data
        foreach ($reposRaw as $repo) {
            //Parsing repos in the format
            $reposData[$repo->name] = $repo;

            // Setting the json encode to associative to return in array format
            $languages = json_decode($this->client->request("GET", $repo->languages_url,
                ['auth_basic' => $this->parameters->get('app.github.auth')])->getContent(), true);
            $totalNumberOfLines = array_sum($languages);
            $languagePercentages = [];

            // Calculating percentage for language ussage
            foreach ($languages as $languageName => $languageValue) {
                $languagePercentages[$languageName] = number_format($languageValue / $totalNumberOfLines * 100, 4);
            }

            // Also getting main Language
            $reposLanguageDetails[$repo->name] = [
                'main_language' => array_keys($languages, max($languages)),
                'language_percentages' => $languagePercentages
            ];
        }

        $this->reposData = [
            'reposData' => $reposData,
            'reposLanguageDetails' => $reposLanguageDetails,
        ];
    }

    public function _getReposData(): array
    {
        return $this->reposData;
    }

    /**
     * @throws ClientExceptionInterfaceAlias
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function _setOrgsData(): void
    {
        $this->orgsData = json_decode($this->client->request("GET", $this->_getUserData()->organizations_url . '?per_page=10',
            ['auth_basic' => $this->parameters->get('app.github.auth')])->getContent(), true);
    }

    /**
     * @return array
     */
    public function _getOrgsData(): array
    {
        return $this->orgsData;
    }

    /**
     * @return User
     */
    public function getServiceData(): User
    {
        $userData = $this->_getUserData();
        $reposData = $this->_getReposData();
        $organizationsData = $this->_getOrgsData();

        // if user would have been a real entity validations would be present here before creation
        $user = new User();
        $user->__setName($userData->name);
        $user->__setProfile($userData->bio);
        $user->__setWebsite($userData->blog);
        $user->__setOrgs($organizationsData);
        $user->__setRepos($reposData);

        return $user;
    }
}
