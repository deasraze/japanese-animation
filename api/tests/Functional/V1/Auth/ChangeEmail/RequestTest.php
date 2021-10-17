<?php

declare(strict_types=1);

namespace App\Tests\Functional\V1\Auth\ChangeEmail;

use App\Auth\Entity\User\Id;
use App\Tests\Functional\Json;
use App\Tests\Functional\UserIdentityMother;
use App\Tests\Functional\WebTestCase;

/**
 * @internal
 */
final class RequestTest extends WebTestCase
{
    private const URI = '/v1/auth/user/change/email/request';

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures([
            RequestFixture::class,
        ]);
    }

    public function testMethod(): void
    {
        $this->client()->request('PUT', self::URI);

        $this->assertResponseStatusCodeSame(405);
    }

    public function testGuest(): void
    {
        $this->client()->request('POST', self::URI, content: Json::encode([
            'email' => 'new-email@app.test',
        ]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testSuccess(): void
    {
        $this
            ->authorizedClient(
                UserIdentityMother::user()
                    ->withId(RequestFixture::ACTIVE)
                    ->build()
            )
            ->request('POST', self::URI, content: Json::encode([
                'email' => 'new-email@app.test',
            ]));

        $this->assertResponseStatusCodeSame(201);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([], Json::decode($body));
    }

    public function testFakeId(): void
    {
        $this
            ->authorizedClient(
                UserIdentityMother::user()
                    ->withId(RequestFixture::ACTIVE)
                    ->build()
            )
            ->request('POST', self::URI, content: Json::encode([
                'id' => Id::generate()->getValue(),
                'email' => 'new-email@app.test',
            ]));

        $this->assertResponseStatusCodeSame(201);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([], Json::decode($body));
    }

    public function testEmpty(): void
    {
        $this
            ->authorizedClient(
                UserIdentityMother::user()
                    ->withId(RequestFixture::ACTIVE)
                    ->build()
            )
            ->request('POST', self::URI, content: Json::encode([
                'email' => '',
            ]));

        $this->assertResponseStatusCodeSame(422);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'errors' => [
                'email' => 'This value should not be blank.',
            ],
        ], Json::decode($body));
    }

    public function testEmptyLang(): void
    {
        $this
            ->authorizedClient(
                UserIdentityMother::user()
                    ->withId(RequestFixture::ACTIVE)
                    ->build()
            )
            ->request('POST', self::URI, server: ['HTTP_ACCEPT_LANGUAGE' => 'ru;q=0.7,en;q=0.6'], content: Json::encode([
                'email' => '',
            ]));

        $this->assertResponseStatusCodeSame(422);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'errors' => [
                'email' => 'Значение не должно быть пустым.',
            ],
        ], Json::decode($body));
    }

    public function testNotValid(): void
    {
        $this
            ->authorizedClient(
                UserIdentityMother::user()
                    ->withId(RequestFixture::ACTIVE)
                    ->build()
            )
            ->request('POST', self::URI, content: Json::encode([
                'email' => 'not-email',
            ]));

        $this->assertResponseStatusCodeSame(422);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'errors' => [
                'email' => 'This value is not a valid email address.',
            ],
        ], Json::decode($body));
    }

    public function testNotValidLang(): void
    {
        $this
            ->authorizedClient(
                UserIdentityMother::user()
                    ->withId(RequestFixture::ACTIVE)
                    ->build()
            )
            ->request('POST', self::URI, server: ['HTTP_ACCEPT_LANGUAGE' => 'ru-RU'], content: Json::encode([
                'email' => 'not-email',
            ]));

        $this->assertResponseStatusCodeSame(422);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'errors' => [
                'email' => 'Значение адреса электронной почты недопустимо.',
            ],
        ], Json::decode($body));
    }

    public function testWait(): void
    {
        $this
            ->authorizedClient(
                UserIdentityMother::user()
                    ->withId(RequestFixture::WAIT)
                    ->build()
            )
            ->request('POST', self::URI, content: Json::encode([
                'email' => 'new-email@app.test',
            ]));

        $this->assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'message' => 'User is not active.',
        ], Json::decode($body));
    }

    public function testWaitLang(): void
    {
        $this
            ->authorizedClient(
                UserIdentityMother::user()
                    ->withId(RequestFixture::WAIT)
                    ->build()
            )
            ->request('POST', self::URI, server: ['HTTP_ACCEPT_LANGUAGE' => 'ru'], content: Json::encode([
                'email' => 'new-email@app.test',
            ]));

        $this->assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'message' => 'Пользователь не активен.',
        ], Json::decode($body));
    }

    public function testAlready(): void
    {
        $this
            ->authorizedClient(
                UserIdentityMother::user()
                    ->withId(RequestFixture::ACTIVE)
                    ->build()
            )
            ->request('POST', self::URI, content: Json::encode([
                'email' => 'active@app.test',
            ]));

        $this->assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'message' => 'Email is already used.',
        ], Json::decode($body));
    }

    public function testAlreadyLang(): void
    {
        $this
            ->authorizedClient(
                UserIdentityMother::user()
                    ->withId(RequestFixture::ACTIVE)
                    ->build()
            )
            ->request('POST', self::URI, server: ['HTTP_ACCEPT_LANGUAGE' => 'ru'], content: Json::encode([
                'email' => 'active@app.test',
            ]));

        $this->assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'message' => 'Электронная почта уже используется.',
        ], Json::decode($body));
    }
}
