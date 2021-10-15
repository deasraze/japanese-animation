<?php

declare(strict_types=1);

namespace App\Tests\Functional\V1\Auth\Remove;

use App\Tests\Functional\Json;
use App\Tests\Functional\UserIdentityMother;
use App\Tests\Functional\WebTestCase;

/**
 * @internal
 */
final class RemoveTest extends WebTestCase
{
    private const URI = '/v1/auth/users/%s/delete';

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures([
            RemoveFixture::class,
        ]);
    }

    public function testMethod(): void
    {
        $this->client()->request('POST', sprintf(self::URI, RemoveFixture::WAIT));

        $this->assertResponseStatusCodeSame(405);
    }

    public function testGuest(): void
    {
        $this->client()->request('DELETE', sprintf(self::URI, RemoveFixture::WAIT));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testUser(): void
    {
        $this
            ->authorizedClient(UserIdentityMother::user()->build())
            ->request('DELETE', sprintf(self::URI, RemoveFixture::WAIT));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testSuccess(): void
    {
        $this
            ->authorizedClient(UserIdentityMother::admin()->build())
            ->request('DELETE', sprintf(self::URI, RemoveFixture::WAIT));

        $this->assertResponseStatusCodeSame(204);
    }

    public function testActiveUser(): void
    {
        $this
            ->authorizedClient(UserIdentityMother::admin()->build())
            ->request('DELETE', sprintf(self::URI, RemoveFixture::ACTIVE));

        $this->assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'message' => 'Unable to remove active user.',
        ], Json::decode($body));
    }

    public function testActiveUserLang(): void
    {
        $this
            ->authorizedClient(UserIdentityMother::admin()->build())
            ->request('DELETE', sprintf(self::URI, RemoveFixture::ACTIVE), server: [
                'HTTP_ACCEPT_LANGUAGE' => 'en;q=0.7,ru;q=0.9',
            ]);

        $this->assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'message' => 'Нельзя удалить активного пользователя.',
        ], Json::decode($body));
    }
}
