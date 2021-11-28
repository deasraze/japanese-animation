<?php

declare(strict_types=1);

namespace App\Tests\Functional\V1\Auth\ResetPassword;

use App\Tests\Functional\Json;
use App\Tests\Functional\WebTestCase;

/**
 * @internal
 */
final class ResetTest extends WebTestCase
{
    public const URI = '/v1/auth/password/reset/confirm';

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures([
            ResetFixture::class,
        ]);
    }

    public function testMethod(): void
    {
        $this->client()->request('GET', self::URI);

        self::assertResponseStatusCodeSame(405);
    }

    public function testSuccess(): void
    {
        $client = $this->client();
        $client->request('POST', self::URI, content: Json::encode([
            'token' => ResetFixture::VALID,
            'password' => 'new-password',
        ]));

        self::assertResponseIsSuccessful();
        self::assertJson($body = (string) $client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals([], $data);
    }

    public function testExpiredToken(): void
    {
        $client = $this->client();
        $client->request('POST', self::URI, content: Json::encode([
            'token' => ResetFixture::EXPIRED,
            'password' => 'new-password',
        ]));

        self::assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Token is expired.',
        ], $data);
    }

    public function testExpiredTokenLang(): void
    {
        $client = $this->client();
        $client->request('POST', self::URI, server: ['HTTP_ACCEPT_LANGUAGE' => 'ru;q=0.9,en;q=0.7,fr;q=0.8'], content: Json::encode([
            'token' => ResetFixture::EXPIRED,
            'password' => 'new-password',
        ]));

        self::assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Время действия токена истекло.',
        ], $data);
    }

    public function testInvalidToken(): void
    {
        $client = $this->client();
        $client->request('POST', self::URI, content: Json::encode([
            'token' => 'invalid-token',
            'password' => 'new-password',
        ]));

        self::assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Token is invalid.',
        ], $data);
    }

    public function testInvalidTokenLang(): void
    {
        $client = $this->client();
        $client->request('POST', self::URI, server: ['HTTP_ACCEPT_LANGUAGE' => 'ru-RU'], content: Json::encode([
            'token' => 'invalid-token',
            'password' => 'new-password',
        ]));

        self::assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Неверный токен.',
        ], $data);
    }

    public function testIncorrectPassword(): void
    {
        $client = $this->client();
        $client->request('POST', self::URI, content: Json::encode([
            'token' => ResetFixture::VALID,
            'password' => 'short',
        ]));

        self::assertResponseStatusCodeSame(422);
        self::assertJson($body = (string) $client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals([
            'errors' => [
                'password' => ['This value is too short. It should have 8 characters or more.'],
            ],
        ], $data);
    }

    public function testIncorrectPasswordLang(): void
    {
        $client = $this->client();
        $client->request('POST', self::URI, server: ['HTTP_ACCEPT_LANGUAGE' => 'ru'], content: Json::encode([
            'token' => ResetFixture::VALID,
            'password' => 'short',
        ]));

        self::assertResponseStatusCodeSame(422);
        self::assertJson($body = (string) $client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals([
            'errors' => [
                'password' => ['Значение слишком короткое. Должно быть равно 8 символам или больше.'],
            ],
        ], $data);
    }

    public function testEmpty(): void
    {
        $client = $this->client();
        $client->request('POST', self::URI, content: Json::encode([]));

        self::assertResponseStatusCodeSame(422);
        self::assertJson($body = (string) $client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals([
            'errors' => [
                'token' => ['This value should not be blank.'],
                'password' => ['This value should not be blank.'],
            ],
        ], $data);
    }

    public function testEmptyLang(): void
    {
        $client = $this->client();
        $client->request('POST', self::URI, server: ['HTTP_ACCEPT_LANGUAGE' => 'ru'], content: Json::encode([]));

        self::assertResponseStatusCodeSame(422);
        self::assertJson($body = (string) $client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals([
            'errors' => [
                'token' => ['Значение не должно быть пустым.'],
                'password' => ['Значение не должно быть пустым.'],
            ],
        ], $data);
    }
}
