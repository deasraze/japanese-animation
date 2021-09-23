<?php

declare(strict_types=1);

namespace App\Tests\Functional;

/**
 * @internal
 */
final class HomeTest extends WebTestCase
{
    public function testMethod(): void
    {
        $this->client()->request('POST', '/');

        $this->assertResponseStatusCodeSame(405);
    }

    public function testSuccess(): void
    {
        $this->client()->request('GET', '/');

        $this->assertResponseIsSuccessful();

        self::assertNotFalse($content = $this->client()->getResponse()->getContent());
        self::assertJson($content);

        self::assertEquals('{}', $content);
    }
}
