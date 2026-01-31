<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Request\ChangeEmailRequest;
use App\Application\Request\ChangePasswordRequest;
use App\Application\Request\EditPersonalInfoRequest;
use App\Application\Request\RegisterRequest;
use App\Application\Request\RequestResetPasswordRequest;
use App\Application\Response\UserResponse;
use App\Domain\Entity\User;

interface UserServiceInterface
{
    public function register(RegisterRequest $request): UserResponse;
    public function whoami(User $user): UserResponse;
    public function editPersonalInfo(User $user, EditPersonalInfoRequest $request): UserResponse;
    public function changeEmail(User $user, ChangeEmailRequest $request): UserResponse;
    public function requestEmailVerification(User $user): void;
    public function verifyEmail(string $token): UserResponse;
    public function changePassword(User $user, ChangePasswordRequest $request): UserResponse;
    public function requestPasswordReset(RequestResetPasswordRequest $request): void;
    public function resetPassword(string $token, ChangePasswordRequest $request): UserResponse;
}
