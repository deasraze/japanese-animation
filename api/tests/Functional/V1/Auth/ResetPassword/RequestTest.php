<?php

declare(strict_types=1);

namespace App\Tests\Functional\V1\Auth\ResetPassword;

use App\Tests\Functional\Json;
use App\Tests\Functional\WebTestCase;

/**
 * @internal
 */
final class RequestTest extends WebTestCase
{
    private const URI = '/v1/auth/password/reset/request';

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures([
            RequestFixture::class,
        ]);
    }

    public function testMethod(): void
    {
        $this->client()->request('GET', self::URI);

        self::assertResponseStatusCodeSame(405);
    }

    public function testSuccess(): void
    {
        $this->mailer()->clear();

        $client = $this->client();
        $client->request('POST', self::URI, content: Json::encode([
            'email' => 'existing@app.test',
        ]));

        self::assertResponseStatusCodeSame(201);
        self::assertJson($body = (string) $client->getResponse()->getContent());

        self::assertEquals([], Json::decode($body));
        self::assertTrue($this->mailer()->hasEmailSentTo('existing@app.test'));
    }

    public function testNotExists(): void
    {
        $client = $this->client();
        $client->request('POST', self::URI, content: Json::encode([
            'email' => 'not-exists@app.test',
        ]));

        self::assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'User is not found.',
        ], $data);
    }

    public function testNotExistsLang(): void
    {
        $client = $this->client();
        $client->request('POST', self::URI, server: ['HTTP_ACCEPT_LANGUAGE' => 'en-US;q=0.7,ru-RU;q=0.9'], content: Json::encode([
            'email' => 'not-exists@app.test',
        ]));

        self::assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Пользователь не найден.',
        ], $data);
    }

    public function testNotActive(): void
    {
        $client = $this->client();
        $client->request('POST', self::URI, content: Json::encode([
            'email' => 'wait@app.test',
        ]));

        self::assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'User is not active.',
        ], $data);
    }

    public function testNotActiveLang(): void
    {
        $client = $this->client();
        $client->request('POST', self::URI, server: ['HTTP_ACCEPT_LANGUAGE' => 'ru-RU'], content: Json::encode([
            'email' => 'wait@app.test',
        ]));

        self::assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals([
            'message' => 'Пользователь не активен.',
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
                'email' => 'This value should not be blank.',
            ],
        ], $data);
    }

    public function testNotValid(): void
    {
        $client = $this->client();
        $client->request('POST', self::URI, content: Json::encode([
            'email' => 'not-email',
        ]));

        self::assertResponseStatusCodeSame(422);
        self::assertJson($body = (string) $client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals([
            'errors' => [
                'email' => 'This value is not a valid email address.',
            ],
        ], $data);
    }

    public function testNotValidLang(): void
    {
        $client = $this->client();
        $client->request('POST', self::URI, server: ['HTTP_ACCEPT_LANGUAGE' => 'ru'], content: Json::encode([
            'email' => 'not-email',
        ]));

        self::assertResponseStatusCodeSame(422);
        self::assertJson($body = (string) $client->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertEquals([
            'errors' => [
                'email' => 'Значение адреса электронной почты недопустимо.',
            ],
        ], $data);
    }
}
