<?php

declare(strict_types=1);

namespace App\Tests\Functional\V1\Auth\Join;

use App\Tests\Functional\Json;
use App\Tests\Functional\WebTestCase;

/**
 * @internal
 */
final class RequestTest extends WebTestCase
{
    private const URI = '/v1/auth/join';

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

        $this->client()->request('POST', self::URI, content: Json::encode([
            'email' => 'test-user@app.test',
            'nickname' => 'TestUser',
            'password' => 'password-hash',
        ]));

        self::assertResponseStatusCodeSame(201);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([], Json::decode($body));

        self::assertTrue($this->mailer()->hasEmailSentTo('test-user@app.test'));
    }

    public function testEmailExisting(): void
    {
        $this->client()->request('POST', self::URI, content: Json::encode([
            'email' => 'existing@app.test',
            'nickname' => 'TestUser',
            'password' => 'password-hash',
        ]));

        self::assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'message' => 'Email is already used.',
        ], Json::decode($body));
    }

    public function testNicknameExisting(): void
    {
        $this->client()->request('POST', self::URI, content: Json::encode([
            'email' => 'test-user@app.test',
            'nickname' => 'existing',
            'password' => 'password-hash',
        ]));

        self::assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'message' => 'Nickname is already used.',
        ], Json::decode($body));
    }

    public function testNotValid(): void
    {
        $this->client()->request('POST', self::URI, content: Json::encode([
            'email' => 'not-email',
            'nickname' => 'inv@alid*',
            'password' => 'short',
        ]));

        self::assertResponseStatusCodeSame(422);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'errors' => [
                'email' => 'This value is not a valid email address.',
                'nickname' => 'This value should be of type alnum.',
                'password' => 'This value is too short. It should have 8 characters or more.',
            ],
        ], Json::decode($body));
    }

    public function testEmpty(): void
    {
        $this->client()->request('POST', self::URI, content: Json::encode([]));

        self::assertResponseStatusCodeSame(422);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'errors' => [
                'email' => 'This value should not be blank.',
                'nickname' => 'This value should not be blank.',
                'password' => 'This value should not be blank.',
            ],
        ], Json::decode($body));
    }
}
