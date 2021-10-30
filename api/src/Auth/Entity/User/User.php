<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use App\Auth\Service\PasswordHasher;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'auth_users')]
class User
{
    #[ORM\Id, ORM\Column(type: 'auth_user_id')]
    private Id $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $date;

    #[ORM\Column(type: 'auth_user_email', unique: true)]
    private Email $email;

    #[ORM\Embedded(class: Name::class)]
    private Name $name;

    #[ORM\Column(type: 'auth_user_status', length: 16)]
    private Status $status;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $passwordHash = null;

    #[ORM\Embedded(class: Token::class)]
    private ?Token $joinConfirmToken = null;

    #[ORM\Embedded(class: Token::class)]
    private ?Token $resetPasswordToken = null;

    #[ORM\Column(type: 'auth_user_email', nullable: true)]
    private ?Email $newEmail = null;

    #[ORM\Embedded(class: Token::class)]
    private ?Token $newEmailToken = null;

    #[ORM\Column(type: 'auth_user_role', length: 16)]
    private Role $role;

    /** @var Collection<array-key, UserNetwork> */
    private Collection $networks;

    private function __construct(Id $id, DateTimeImmutable $date, Email $email, Name $name, Status $status)
    {
        $this->id = $id;
        $this->date = $date;
        $this->email = $email;
        $this->name = $name;
        $this->status = $status;
        $this->role = Role::user();
        $this->networks = new ArrayCollection();
    }

    public static function joinByNetwork(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        Name $name,
        Network $network
    ): self {
        $user = new self($id, $date, $email, $name, Status::active());

        $user->networks->add(new UserNetwork($user, $network));

        return $user;
    }

    public static function requestJoinByEmail(
        Id $id,
        DateTimeImmutable $date,
        Email $email,
        Name $name,
        string $passwordHash,
        Token $token,
    ): self {
        $user = new self($id, $date, $email, $name, Status::wait());

        $user->passwordHash = $passwordHash;
        $user->joinConfirmToken = $token;

        return $user;
    }

    public function confirmJoin(string $token, DateTimeImmutable $date): void
    {
        if (null === $this->joinConfirmToken) {
            throw new DomainException('Confirmation is not required.');
        }

        $this->joinConfirmToken->validate($token, $date);

        $this->status = Status::active();
        $this->joinConfirmToken = null;
    }

    public function attachNetwork(Network $network): void
    {
        foreach ($this->networks as $existing) {
            if ($existing->getNetwork()->isEqualTo($network)) {
                throw new DomainException('Network is already attached.');
            }
        }

        $this->networks->add(new UserNetwork($this, $network));
    }

    public function requestResetPassword(Token $token, DateTimeImmutable $date): void
    {
        if (!$this->isActive()) {
            throw new DomainException('User is not active.');
        }

        if (null !== $this->resetPasswordToken && !$this->resetPasswordToken->isExpiredTo($date)) {
            throw new DomainException('Password reset is already requested.');
        }

        $this->resetPasswordToken = $token;
    }

    public function resetPassword(string $token, string $passwordHash, DateTimeImmutable $date): void
    {
        if (null === $this->resetPasswordToken) {
            throw new DomainException('Reset password was not requested.');
        }

        $this->resetPasswordToken->validate($token, $date);
        $this->resetPasswordToken = null;
        $this->passwordHash = $passwordHash;
    }

    public function requestEmailChanging(Email $new, Token $token, DateTimeImmutable $date): void
    {
        if (!$this->isActive()) {
            throw new DomainException('User is not active.');
        }

        if ($this->email->isEqualTo($new)) {
            throw new DomainException('Email is already same.');
        }

        if (null !== $this->newEmailToken && !$this->newEmailToken->isExpiredTo($date)) {
            throw new DomainException('Email changing is already requested.');
        }

        $this->newEmail = $new;
        $this->newEmailToken = $token;
    }

    public function confirmEmailChanging(string $token, DateTimeImmutable $date): void
    {
        if (null === $this->newEmail || null === $this->newEmailToken) {
            throw new DomainException('Email changing was not requested.');
        }

        $this->newEmailToken->validate($token, $date);
        $this->email = $this->newEmail;
        $this->newEmail = null;
        $this->newEmailToken = null;
    }

    public function changePassword(string $current, string $new, PasswordHasher $hasher): void
    {
        if (null === $this->passwordHash) {
            throw new DomainException('User does not have an old password.');
        }

        if (!$hasher->verify($this->passwordHash, $current)) {
            throw new DomainException('Current password is incorrect.');
        }

        $this->passwordHash = $hasher->hash($new);
    }

    public function changeRole(Role $role): void
    {
        if ($this->role->isEqualTo($role)) {
            throw new DomainException('Role is already same.');
        }

        $this->role = $role;
    }

    public function block(): void
    {
        if ($this->isBlocked()) {
            throw new DomainException('User is already blocked.');
        }

        $this->status = Status::blocked();
    }

    public function remove(): void
    {
        if ($this->isActive()) {
            throw new DomainException('Unable to remove active user.');
        }
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function isWait(): bool
    {
        return $this->status->isWait();
    }

    public function isBlocked(): bool
    {
        return $this->status->isBlocked();
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function getJoinConfirmToken(): ?Token
    {
        return $this->joinConfirmToken;
    }

    public function getResetPasswordToken(): ?Token
    {
        return $this->resetPasswordToken;
    }

    public function getNewEmail(): ?Email
    {
        return $this->newEmail;
    }

    public function getNewEmailToken(): ?Token
    {
        return $this->newEmailToken;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    /**
     * @return array<array-key, Network>
     */
    public function getNetworks(): array
    {
        return $this
            ->networks->map(static fn (UserNetwork $userNetwork) => $userNetwork->getNetwork())
            ->toArray();
    }

    #[ORM\PostLoad]
    public function checkEmbeds(): void
    {
        if (null !== $this->joinConfirmToken && $this->joinConfirmToken->isEmpty()) {
            $this->joinConfirmToken = null;
        }

        if (null !== $this->resetPasswordToken && $this->resetPasswordToken->isEmpty()) {
            $this->resetPasswordToken = null;
        }

        if (null !== $this->newEmailToken && $this->newEmailToken->isEmpty()) {
            $this->newEmailToken = null;
        }
    }
}
