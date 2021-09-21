<?php

declare(strict_types=1);

namespace App\Auth\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use DomainException;

class UserRepository
{
    private EntityManagerInterface $em;
    /**
     * @var EntityRepository<User>
     */
    private EntityRepository $repo;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(User::class);
    }

    public function get(Id $id): User
    {
        $user = $this->repo->find($id->getValue());

        if (null === $user) {
            throw new DomainException('User is not found.');
        }

        return $user;
    }

    public function getByEmail(Email $email): User
    {
        $user = $this->repo->findOneBy(['email' => $email->getValue()]);

        if (null === $user) {
            throw new DomainException('User is not found.');
        }

        return $user;
    }

    public function findByJoinConfirmToken(string $token): ?User
    {
        return $this->repo->findOneBy(['joinConfirmToken.value' => $token]);
    }

    public function findByResetPasswordToken(string $token): ?User
    {
        return $this->repo->findOneBy(['resetPasswordToken.value' => $token]);
    }

    public function findByNewEmailToken(string $token): ?User
    {
        return $this->repo->findOneBy(['newEmailToken.value' => $token]);
    }

    public function hasByEmail(Email $email): bool
    {
        return $this->repo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->where('t.email = :email')
            ->setParameter('email', $email->getValue())
            ->getQuery()->getSingleScalarResult() > 0;
    }

    public function hasByNickname(Name $name): bool
    {
        return $this->repo->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->where('t.name.nickname = :nickname')
            ->setParameter('nickname', $name->getNickname())
            ->getQuery()->getSingleScalarResult() > 0;
    }

    public function add(User $user): void
    {
        $this->em->persist($user);
    }

    public function remove(User $user): void
    {
        $this->em->remove($user);
    }
}
