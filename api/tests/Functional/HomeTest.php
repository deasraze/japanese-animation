<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @internal
 */
final class HomeTest extends WebTestCase
{
    public function testMethod(): void
    {
        $client = $this->createClient();
        $client->request('POST', '/');

        $this->assertResponseStatusCodeSame(405);
    }

    public function testSuccess(): void
    {
        $client = $this->createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();

        self::assertNotFalse($content = $client->getResponse()->getContent());
        self::assertJson($content);

        self::assertEquals('{}', $content);
    }
}
