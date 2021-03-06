<?php

declare(strict_types=1);

namespace App\Tests\Functional\V1\Auth\Block;

use App\Tests\Functional\Json;
use App\Tests\Functional\UserIdentityMother;
use App\Tests\Functional\WebTestCase;

/**
 * @internal
 */
final class BlockTest extends WebTestCase
{
    private const URI = '/v1/auth/users/%s/block';

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures([
            BlockFixture::class,
        ]);
    }

    public function testGuest(): void
    {
        $this->client()->request('PUT', sprintf(self::URI, BlockFixture::ACTIVE));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testUser(): void
    {
        $this
            ->authorizedClient(UserIdentityMother::user()->build())
            ->request('PUT', sprintf(self::URI, BlockFixture::ACTIVE));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testMethod(): void
    {
        $this
            ->authorizedClient(UserIdentityMother::admin()->build())
            ->request('POST', sprintf(self::URI, BlockFixture::ACTIVE));

        $this->assertResponseStatusCodeSame(405);
    }

    public function testSuccess(): void
    {
        $this
            ->authorizedClient(UserIdentityMother::admin()->build())
            ->request('PUT', sprintf(self::URI, BlockFixture::ACTIVE));

        $this->assertResponseIsSuccessful();
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([], Json::decode($body));
    }

    public function testAlreadyBlocked(): void
    {
        $this
            ->authorizedClient(UserIdentityMother::admin()->build())
            ->request('PUT', sprintf(self::URI, BlockFixture::BLOCKED));

        $this->assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'message' => 'User is already blocked.',
        ], Json::decode($body));
    }

    public function testAlreadyBlockedLang(): void
    {
        $this
            ->authorizedClient(UserIdentityMother::admin()->build())
            ->request('PUT', sprintf(self::URI, BlockFixture::BLOCKED), server: [
                'HTTP_ACCEPT_LANGUAGE' => 'ru-RU,ru;q=0.8,en-US,en;q=0.9',
            ]);

        $this->assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'message' => '???????????????????????? ?????? ????????????????????????.',
        ], Json::decode($body));
    }

    public function testBlockYourself(): void
    {
        $identity = UserIdentityMother::admin()->build();

        $this
            ->authorizedClient($identity)
            ->request('PUT', sprintf(self::URI, $identity->getId()));

        $this->assertResponseStatusCodeSame(400);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'message' => 'Unable block to yourself.',
        ], Json::decode($body));
    }

    public function testBlockYourselfLang(): void
    {
        $identity = UserIdentityMother::admin()->build();

        $this
            ->authorizedClient($identity)
            ->request('PUT', sprintf(self::URI, $identity->getId()), server: [
                'HTTP_ACCEPT_LANGUAGE' => 'en-US;q=0.8,ru-RU;q=0.9',
            ]);

        $this->assertResponseStatusCodeSame(400);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'message' => '???????????? ?????????????????????????? ???????????? ????????.',
        ], Json::decode($body));
    }
}
