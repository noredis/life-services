<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Mapper\UserDomainMapperInterface;
use App\Application\Request\ChangeEmailRequest;
use App\Application\Request\ChangePasswordRequest;
use App\Application\Request\EditPersonalInfoRequest;
use App\Application\Request\RegisterRequest;
use App\Application\Request\RequestResetPasswordRequest;
use App\Application\Request\UserRequestInterface;
use App\Application\Response\UserResponse;
use App\Application\Validator\UserValidatorInterface;
use App\Common\Failure\EmailIsBusyException;
use App\Common\Failure\PasswordResetNotFoundException;
use App\Common\Failure\TokenNotFoundException;
use App\Common\Failure\UserNotFoundException;
use App\Common\Failure\ValidationException;
use App\Domain\Entity\EmailVerification;
use App\Domain\Entity\PasswordReset;
use App\Domain\Entity\User;
use App\Domain\Interfaces\OutboxMessageRepositoryInterface;
use App\Domain\Interfaces\UserRepositoryInterface;
use App\Domain\UseCase\IsEmailAvailable;
use App\Infrastructure\Helper\EmailVerificationTokenGeneratorInterface;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService implements UserServiceInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserValidatorInterface $userValidator,
        private IsEmailAvailable $isEmailAvailable,
        private UserDomainMapperInterface $mapper,
        private UserRepositoryInterface $userRepository,
        private OutboxMessageRepositoryInterface $outboxMessageRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private EmailVerificationTokenGeneratorInterface $tokenGenerator,
    ) {
    }

    public function register(RegisterRequest $request): UserResponse
    {
        $this->validateRequest($request);
        $this->checkEmailForAvailability($request->email);

        $user = $this->mapper->mapRegisterRequestToEntity($request);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
        $user->changePassword($hashedPassword);
        $user->assignEmailVerification($this->newEmailVerification());
        $user = $this->save($user);

        return $this->mapper->mapUserFromEntity($user);
    }

    public function whoami(User $user): UserResponse
    {
        return $this->mapper->mapUserFromEntity($user);
    }

    public function editPersonalInfo(User $user, EditPersonalInfoRequest $request): UserResponse
    {
        $oldName = $user->getName();
        if ($oldName === $request->name) {
            return $this->mapper->mapUserFromEntity($user);
        }

        $this->validateRequest($request);

        $user->editPersonalInfo($request->name);
        $user = $this->save($user);

        return $this->mapper->mapUserFromEntity($user);
    }

    public function changeEmail(User $user, ChangeEmailRequest $request): UserResponse
    {
        $oldEmail = $user->getEmail();
        if ($oldEmail === $request->email) {
            return $this->mapper->mapUserFromEntity($user);
        }

        $this->validateRequest($request);
        $this->checkEmailForAvailability($request->email);

        $user->changeEmail($request->email, $this->newEmailVerification());
        $user = $this->save($user);

        return $this->mapper->mapUserFromEntity($user);
    }

    public function requestEmailVerification(User $user): void
    {
        $user->assignEmailVerification($this->newEmailVerification());
        $user = $this->save($user);
    }

    public function verifyEmail(string $token): UserResponse
    {
        $user = $this->userRepository->getByVerificationToken($token);
        if (!$user) {
            throw new TokenNotFoundException();
        }

        $user->verifyEmail();
        $user = $this->save($user);

        return $this->mapper->mapUserFromEntity($user);
    }

    public function changePassword(User $user, ChangePasswordRequest $request): UserResponse
    {
        $this->validateRequest($request);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $request->password);
        $user->changePassword($hashedPassword);
        $user = $this->save($user);

        return $this->mapper->mapUserFromEntity($user);
    }

    public function requestPasswordReset(RequestResetPasswordRequest $request): void
    {
        $this->validateRequest($request);

        $user = $this->userRepository->getByEmail($request->email);
        if (!$user) {
            throw new UserNotFoundException($request->email);
        }

        $user->assignPasswordReset($this->newPasswordReset());
        $user = $this->save($user);
    }

    public function resetPassword(string $token, ChangePasswordRequest $request): UserResponse
    {
        $user = $this->userRepository->getByPasswordResetToken($token);
        if (!$user) {
            throw new PasswordResetNotFoundException();
        }

        $user->resetPassword();

        return $this->changePassword($user, $request);
    }

    protected function checkEmailForAvailability(string $email): void
    {
        if (!($this->isEmailAvailable)($email)) {
            throw new EmailIsBusyException($email);
        }
    }

    protected function validateRequest(UserRequestInterface $request): void
    {
        $fields = $this->userValidator->validate($request);
        if ($fields) {
            throw new ValidationException($fields);
        }
    }

    protected function newEmailVerification(): EmailVerification
    {
        return new EmailVerification(
            token: $this->tokenGenerator->generate(),
            expiresIn: new DateTimeImmutable('+3 hours'),
        );
    }

    protected function newPasswordReset(): PasswordReset
    {
        return new PasswordReset(
            token: $this->tokenGenerator->generate(),
            expiresIn: new DateTimeImmutable('+3hours'),
        );
    }

    protected function save(User $user): User
    {
        return $this->em->wrapInTransaction(function () use ($user) {
            $user = $this->userRepository->save($user);

            $this->outboxMessageRepository->saveAll(
                $user->withdrawEvents(),
            );

            return $user;
        });
    }
}
