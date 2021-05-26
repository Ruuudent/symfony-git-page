<?php

namespace App\Entity;

class User {
    private $profile, $website, $repos, $name, $orgs;

    public function __setName(string $name): void {
        $this->name = $name;
    }

    public function __getName(): string {
        return $this->name;
    }

    public function __setProfile(string $profile): void {
        $this->profile = $profile;
    }

    public function __getProfile(): string {
        return $this->profile;
    }

    public function __setWebsite(string $website): void {
        $this->website = $website;
    }

    public function __getWebsite(): string {
        return $this->website;
    }

    public function __setOrgs(array $orgs): void {
        $this->orgs = $orgs;
    }

    public function __getOrgs(): array {
        return $this->orgs;
    }

    public function __setRepos(array $repos): void {
        $this->repos = $repos;
    }

    public function __getRepos(): array {
        return $this->repos;
    }

    public function __getUserData(): array {
        return [
            'profile' => self::__getProfile(),
            'website' => self::__getWebsite(),
            'orgs' => self::__getOrgs(),
            'repos' => self::__getRepos()
        ];
    }
}
