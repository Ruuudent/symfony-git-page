<?php

namespace App\Interfaces;

use App\Entity\User;

Interface GitInterface
{
    public function _setUserName(string $name): void;
    public function _getUserName(): string;
    public function getServiceData(): User;
}
