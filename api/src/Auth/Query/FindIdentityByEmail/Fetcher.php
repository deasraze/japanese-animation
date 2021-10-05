<?php

declare(strict_types=1);

namespace App\Auth\Query\FindIdentityByEmail;

use App\Auth\Entity\User\Status;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;

class Fetcher
{
    public function __construct(private Connection $connection)
    {
    }

    public function fetch(string $email): ?Identity
    {
        /** @var Result $stmt */
        $stmt = $this->connection->createQueryBuilder()
            ->select([
                'id',
                'email',
                'password_hash',
                'status',
                'role',
            ])
            ->from('auth_users')
            ->where('email = :email')
            ->setParameter('email', mb_strtolower($email))
            ->execute();

        /**
         * @var array{
         *     id: string,
         *     email: string,
         *     password_hash: string,
         *     status: string,
         *     role: string,
         * }|false
         */
        $row = $stmt->fetchAssociative();

        if (false === $row) {
            return null;
        }

        return new Identity(
            $row['id'],
            $row['email'],
            $row['password_hash'],
            $row['role'],
            $row['status'] === Status::ACTIVE
        );
    }
}
