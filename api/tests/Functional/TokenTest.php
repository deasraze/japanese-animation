<?php

declare(strict_types=1);

namespace App\Tests\Functional;

/**
 * @internal
 */
final class TokenTest extends WebTestCase
{
    private const URI = '/token';

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures([
            AuthFixture::class,
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

    public function testSuccess(): void
    {
        $this->client()->request('POST', self::URI, server: ['CONTENT_TYPE' => 'application/json'], content: Json::encode([
            'username' => AuthFixture::userIdentifier(),
            'password' => 'password',
        ]));

        $this->assertResponseIsSuccessful();
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertArrayHasKey('token', $data = Json::decode($body));
        self::assertNotEmpty($data['token']);
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
}
