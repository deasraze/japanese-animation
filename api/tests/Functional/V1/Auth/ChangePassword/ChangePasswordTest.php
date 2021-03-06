<?php

declare(strict_types=1);

namespace App\Tests\Functional\V1\Auth\ChangePassword;

use App\Auth\Entity\User\Id;
use App\Tests\Functional\Json;
use App\Tests\Functional\UserIdentityMother;
use App\Tests\Functional\WebTestCase;

/**
 * @internal
 */
final class ChangePasswordTest extends WebTestCase
{
    private const URI = '/v1/auth/user/change/password';

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures([
            ChangePasswordFixture::class,
        ]);
    }

    public function testMethod(): void
    {
        $this->client()->request('POST', self::URI);

        $this->assertResponseStatusCodeSame(405);
    }

    public function testGuest(): void
    {
        $this->client()->request('PUT', self::URI, content: '{}');

        $this->assertResponseStatusCodeSame(401);
    }

    public function testUser(): void
    {
        $this
            ->authorizedClient(
                UserIdentityMother::user()
                    ->withId(ChangePasswordFixture::VIA_EMAIL)
                    ->build()
            )
            ->request('PUT', self::URI, content: Json::encode([
                'current' => 'password',
                'new' => 'new-password',
            ]));

        $this->assertResponseIsSuccessful();
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([], Json::decode($body));
    }

    public function testAdmin(): void
    {
        $this
            ->authorizedClient(
                UserIdentityMother::admin()
                    ->withId(ChangePasswordFixture::VIA_EMAIL)
                    ->build()
            )
            ->request('PUT', self::URI, content: Json::encode([
                'current' => 'password',
                'new' => 'new-password',
            ]));

        $this->assertResponseIsSuccessful();
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([], Json::decode($body));
    }

    public function testFakeId(): void
    {
        $this
            ->authorizedClient(
                UserIdentityMother::user()
                    ->withId(ChangePasswordFixture::VIA_EMAIL)
                    ->build()
            )
            ->request('PUT', self::URI, content: Json::encode([
                'id' => Id::generate()->getValue(),
                'current' => 'password',
                'new' => 'new-password',
            ]));

        $this->assertResponseIsSuccessful();
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([], Json::decode($body));
    }

    public function testEmpty(): void
    {
        $this
            ->authorizedClient(
                UserIdentityMother::user()
                    ->withId(ChangePasswordFixture::VIA_EMAIL)
                    ->build()
            )
            ->request('PUT', self::URI, content: Json::encode([
                'current' => '',
                'new' => '',
            ]));

        $this->assertResponseStatusCodeSame(422);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'errors' => [
                'current' => ['This value should not be blank.'],
                'new' => ['This value should not be blank.'],
            ],
        ], Json::decode($body));
    }

    public function testEmptyLang(): void
    {
        $this
            ->authorizedClient(
                UserIdentityMother::user()
                    ->withId(ChangePasswordFixture::VIA_EMAIL)
                    ->build()
            )
            ->request('PUT', self::URI, server: ['HTTP_ACCEPT_LANGUAGE' => 'ru-RU;q=0.8,en-US;q=0.5'], content: Json::encode([
                'current' => '',
                'new' => '',
            ]));

        $this->assertResponseStatusCodeSame(422);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'errors' => [
                'current' => ['???????????????? ???? ???????????? ???????? ????????????.'],
                'new' => ['???????????????? ???? ???????????? ???????? ????????????.'],
            ],
        ], Json::decode($body));
    }

    public function testShort(): void
    {
        $this
            ->authorizedClient(
                UserIdentityMother::user()
                    ->withId(ChangePasswordFixture::VIA_EMAIL)
                    ->build()
            )
            ->request('PUT', self::URI, content: Json::encode([
                'current' => 'short',
                'new' => 'short',
            ]));

        $this->assertResponseStatusCodeSame(422);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'errors' => [
                'current' => ['This value is too short. It should have 8 characters or more.'],
                'new' => ['This value is too short. It should have 8 characters or more.'],
            ],
        ], Json::decode($body));
    }

    public function testShortLang(): void
    {
        $this
            ->authorizedClient(
                UserIdentityMother::user()
                    ->withId(ChangePasswordFixture::VIA_EMAIL)
                    ->build()
            )
            ->request('PUT', self::URI, server: ['HTTP_ACCEPT_LANGUAGE' => 'ru-RU'], content: Json::encode([
                'current' => 'short',
                'new' => 'short',
            ]));

        $this->assertResponseStatusCodeSame(422);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'errors' => [
                'current' => ['???????????????? ?????????????? ????????????????. ???????????? ???????? ?????????? 8 ???????????????? ?????? ????????????.'],
                'new' => ['???????????????? ?????????????? ????????????????. ???????????? ???????? ?????????? 8 ???????????????? ?????? ????????????.'],
            ],
        ], Json::decode($body));
    }

    public function testWrongCurrent(): void
    {
        $this
            ->authorizedClient(
                UserIdentityMother::admin()
                    ->withId(ChangePasswordFixture::VIA_EMAIL)
                    ->build()
            )
            ->request('PUT', self::URI, content: Json::encode([
                'current' => 'wrong-password',
                'new' => 'new-password',
            ]));

        $this->assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'message' => 'Current password is incorrect.',
        ], Json::decode($body));
    }

    public function testWrongCurrentLang(): void
    {
        $this
            ->authorizedClient(
                UserIdentityMother::user()
                    ->withId(ChangePasswordFixture::VIA_EMAIL)
                    ->build()
            )
            ->request('PUT', self::URI, server: ['HTTP_ACCEPT_LANGUAGE' => 'ru'], content: Json::encode([
                'current' => 'wrong-password',
                'new' => 'new-password',
            ]));

        $this->assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'message' => '???????????????? ?????????????? ????????????.',
        ], Json::decode($body));
    }

    public function testByNetwork(): void
    {
        $this
            ->authorizedClient(
                UserIdentityMother::user()
                    ->withId(ChangePasswordFixture::VIA_NETWORK)
                    ->build()
            )
            ->request('PUT', self::URI, content: Json::encode([
                'current' => 'any-password',
                'new' => 'new-password',
            ]));

        $this->assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'message' => 'User does not have an old password.',
        ], Json::decode($body));
    }

    public function testByNetworkLang(): void
    {
        $this
            ->authorizedClient(
                UserIdentityMother::user()
                    ->withId(ChangePasswordFixture::VIA_NETWORK)
                    ->build()
            )
            ->request('PUT', self::URI, server: ['HTTP_ACCEPT_LANGUAGE' => 'ru'], content: Json::encode([
                'current' => 'any-password',
                'new' => 'new-password',
            ]));

        $this->assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'message' => '?? ???????????????????????? ?????? ?????????????? ????????????.',
        ], Json::decode($body));
    }
}
