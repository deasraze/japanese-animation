<?php

declare(strict_types=1);

namespace App\Security\Jwt;

use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidPayloadException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;

class JWTUserIdentity implements JWTUserInterface
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        private string $id,
        private string $email,
        private array $roles,
    ) {
    }

    public static function createFromPayload($username, array $payload): self
    {
        if (!\array_key_exists('id', $payload)) {
            throw new InvalidPayloadException('id');
        }

        /** @var string[] $roles */
        $roles = $payload['roles'];

        return new self(
            (string) $payload['id'],
            $username,
            $roles
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword(): ?string
    {
        return null;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
    }
}
