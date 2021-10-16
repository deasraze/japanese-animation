<?php

declare(strict_types=1);

namespace App\Tests\Functional\V1\Auth;

use App\Tests\Functional\Json;
use App\Tests\Functional\UserIdentityMother;
use App\Tests\Functional\WebTestCase;

/**
 * @internal
 */
final class UserTest extends WebTestCase
{
    private const URI = '/v1/auth/user';

    public function testMethod(): void
    {
        $this->client()->request('POST', self::URI);

        $this->assertResponseStatusCodeSame(405);
    }

    public function testGuest(): void
    {
        $this->client()->request('GET', self::URI);

        $this->assertResponseStatusCodeSame(401);
    }

    public function testUser(): void
    {
        $this
            ->authorizedClient(
                UserIdentityMother::user()
                    ->withId($id = '00000000-0000-0000-0000-000000000001')
                    ->build()
            )
            ->request('GET', self::URI);

        $this->assertResponseIsSuccessful();
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'id' => $id,
            'role' => 'ROLE_USER',
        ], Json::decode($body));
    }

    public function testAdmin(): void
    {
        $this
            ->authorizedClient(
                UserIdentityMother::admin()
                    ->withId($id = '00000000-0000-0000-0000-000000000001')
                    ->build()
            )
            ->request('GET', self::URI);

        $this->assertResponseIsSuccessful();
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'id' => $id,
            'role' => 'ROLE_ADMIN',
        ], Json::decode($body));
    }
}
