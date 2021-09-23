<?php

declare(strict_types=1);

namespace App\Tests\Functional\V1\Auth\Join;

use App\Tests\Functional\WebTestCase;
use App\Tests\Functional\Json;
use Ramsey\Uuid\Uuid;

/**
 * @internal
 */
final class ConfirmTest extends WebTestCase
{
    private const URI = '/v1/auth/join/confirm';

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

        self::assertResponseStatusCodeSame(405);
    }

    public function testSuccess(): void
    {
        $this->client()->request('POST', self::URI, content: Json::encode([
            'token' => ConfirmFixture::VALID,
        ]));

        self::assertResponseIsSuccessful();
        self::assertJson($data = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([], Json::decode($data));
    }

    public function testExpired(): void
    {
        $this->client()->request('POST', self::URI, content: Json::encode([
            'token' => ConfirmFixture::EXPIRED,
        ]));

        self::assertResponseStatusCodeSame(409);
        self::assertJson($data = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'message' => 'Token is expired.',
        ], Json::decode($data));
    }

    public function testEmpty(): void
    {
        $this->client()->request('POST', self::URI, content: Json::encode([]));

        self::assertResponseStatusCodeSame(422);
        self::assertJson($data = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'errors' => [
                'token' => 'This value should not be blank.',
            ],
        ], Json::decode($data));
    }

    public function testNotExisting(): void
    {
        $this->client()->request('POST', self::URI, content: Json::encode([
            'token' => Uuid::uuid4()->toString(),
        ]));

        self::assertResponseStatusCodeSame(409);
        self::assertJson($data = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'message' => 'Token is invalid.',
        ], Json::decode($data));
    }
}
