<?php

declare(strict_types=1);

namespace App\Tests\Functional\Token;

use App\Tests\Functional\Json;
use App\Tests\Functional\WebTestCase;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;

/**
 * @internal
 */
final class TokenTest extends WebTestCase
{
    use ArraySubsetAsserts;

    private const URI = '/token';

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures([
            TokenFixture::class,
        ]);
    }

    public function testMethod(): void
    {
        $this->client()->request('GET', self::URI);

        $this->assertResponseStatusCodeSame(405);
    }

    public function testWithoutContent(): void
    {
        $this->client()->request('POST', self::URI, server: ['CONTENT_TYPE' => 'application/json']);

        $this->assertResponseStatusCodeSame(400);
    }

    public function testActiveUser(): void
    {
        $this->client()->request('POST', self::URI, server: ['CONTENT_TYPE' => 'application/json'], content: Json::encode([
            'username' => TokenFixture::activeUserEmail(),
            'password' => 'password',
        ]));

        $this->assertResponseIsSuccessful();
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertArrayHasKey('token', $data = Json::decode($body));
        self::assertNotEmpty($data['token']);
    }

    public function testWaitUser(): void
    {
        $this->client()->request('POST', self::URI, server: ['CONTENT_TYPE' => 'application/json'], content: Json::encode([
            'username' => TokenFixture::waitUserEmail(),
            'password' => 'password',
        ]));

        $this->assertResponseStatusCodeSame(401);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertStringContainsString('Your account is not active.', $body);
    }

    public function testWaitUserLang(): void
    {
        $this->client()->request(
            'POST',
            self::URI,
            server: [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT_LANGUAGE' => 'ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
            ],
            content: Json::encode([
                'username' => TokenFixture::waitUserEmail(),
                'password' => 'password',
            ])
        );

        $this->assertResponseStatusCodeSame(401);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertArraySubset([
            'message' => '???????? ?????????????? ???????????? ??????????????????.',
        ], Json::decode($body));
    }

    public function testInvalidUser(): void
    {
        $this->client()->request('POST', self::URI, server: ['CONTENT_TYPE' => 'application/json'], content: Json::encode([
            'username' => 'invalid-user@app.test',
            'password' => '',
        ]));

        $this->assertResponseStatusCodeSame(401);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertStringContainsString('Invalid credentials.', $body);
    }

    public function testInvalidUserLang(): void
    {
        $this->client()->request('POST', self::URI, server: ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT_LANGUAGE' => 'ru-RU'], content: Json::encode([
            'username' => 'invalid-user@app.test',
            'password' => '',
        ]));

        $this->assertResponseStatusCodeSame(401);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertArraySubset([
            'message' => '???????????????????????????????? ???????????????????????????????????? ????????????.',
        ], Json::decode($body));
    }
}
