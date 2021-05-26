<?php

namespace App\Builder;

use App\Entity\User;
use App\Interfaces\GitInterface;

class GitBuilder
{
    /**
     * @param GitInterface $gitInterface
     * @param string $name
     * @return User
     */
    public function build(GitInterface $gitInterface, string $name): User
    {
        $gitInterface->_setUserName($name);
        $gitInterface->_setUserData();
        $gitInterface->_setReposData();
        $gitInterface->_setOrgsData();

        return $gitInterface->getServiceData();
    }
}
