<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Response\ProfileResponse;
use Symfony\Component\Security\Core\User\UserInterface;

interface ProfileServiceInterface
{
    public function whoami(UserInterface $user): ProfileResponse;
}
