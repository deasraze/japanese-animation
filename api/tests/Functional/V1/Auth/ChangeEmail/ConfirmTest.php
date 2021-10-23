<?php

declare(strict_types=1);

namespace App\Tests\Functional\V1\Auth\ChangeEmail;

use App\Tests\Functional\Json;
use App\Tests\Functional\UserIdentityMother;
use App\Tests\Functional\WebTestCase;

/**
 * @internal
 */
final class ConfirmTest extends WebTestCase
{
    private const URI = '/v1/auth/user/change/email/confirm';

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures([
            ConfirmFixture::class,
        ]);
    }

    public function testMethod(): void
    {
        $this->client()->request('GET', self::URI);

        $this->assertResponseStatusCodeSame(405);
    }

    public function testGuest(): void
    {
        $this->client()->request('POST', self::URI, content: Json::encode([
            'token' => ConfirmFixture::VALID,
        ]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testValid(): void
    {
        $this
            ->authorizedClient(UserIdentityMother::user()->build())
            ->request('POST', self::URI, content: Json::encode([
                'token' => ConfirmFixture::VALID,
            ]));

        $this->assertResponseIsSuccessful();
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([], Json::decode($body));
    }

    public function testEmpty(): void
    {
        $this
            ->authorizedClient(UserIdentityMother::user()->build())
            ->request('POST', self::URI, content: Json::encode([
                'token' => '',
            ]));

        $this->assertResponseStatusCodeSame(422);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'errors' => [
                'token' => 'This value should not be blank.',
            ],
        ], Json::decode($body));
    }

    public function testEmptyLang(): void
    {
        $this
            ->authorizedClient(UserIdentityMother::user()->build())
            ->request('POST', self::URI, server: ['HTTP_ACCEPT_LANGUAGE' => 'ru-RU;q=0.7,en-US;q=0.5'], content: Json::encode([
                'token' => '',
            ]));

        $this->assertResponseStatusCodeSame(422);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'errors' => [
                'token' => 'Значение не должно быть пустым.',
            ],
        ], Json::decode($body));
    }

    public function testInvalid(): void
    {
        $this
            ->authorizedClient(UserIdentityMother::user()->build())
            ->request('POST', self::URI, content: Json::encode([
                'token' => 'invalid-token',
            ]));

        $this->assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'message' => 'Token is invalid.',
        ], Json::decode($body));
    }

    public function testInvalidLang(): void
    {
        $this
            ->authorizedClient(UserIdentityMother::user()->build())
            ->request('POST', self::URI, server: ['HTTP_ACCEPT_LANGUAGE' => 'ru-RU'], content: Json::encode([
                'token' => 'invalid-token',
            ]));

        $this->assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'message' => 'Неверный токен.',
        ], Json::decode($body));
    }

    public function testExpired(): void
    {
        $this
            ->authorizedClient(UserIdentityMother::user()->build())
            ->request('POST', self::URI, content: Json::encode([
                'token' => ConfirmFixture::EXPIRED,
            ]));

        $this->assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'message' => 'Token is expired.',
        ], Json::decode($body));
    }

    public function testExpiredLang(): void
    {
        $this
            ->authorizedClient(UserIdentityMother::user()->build())
            ->request('POST', self::URI, server: ['HTTP_ACCEPT_LANGUAGE' => 'ru'], content: Json::encode([
                'token' => ConfirmFixture::EXPIRED,
            ]));

        $this->assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'message' => 'Время действия токена истекло.',
        ], Json::decode($body));
    }
}
