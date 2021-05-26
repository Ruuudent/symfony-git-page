<?php

namespace App\Interfaces;

use App\Entity\User;

Interface GitInterface
{
    // username
    public function _setUserName(string $name): void;
    public function _getUserName(): string;

    // userdata
    public function _setUserData(): void;
    public function _getUserData(): \stdClass;

    // reposdata
    public function _setReposData(): void;
    public function _getReposData(): array;

    // orgsdata
    public function _setOrgsData(): void;
    public function _getOrgsData(): array;

    public function getServiceData(): User;
}
