<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

interface UserRepository
{
    public function getByEmail(Email $email): User;

    public function hasByEmail(Email $email): bool;

    public function hasByNickname(Name $name): bool;

    public function findByJoinConfirmToken(string $token): ?User;

    public function findByResetPasswordToken(string $token): ?User;

    public function add(User $user): void;
}
