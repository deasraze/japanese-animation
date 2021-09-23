<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;

/**
 * @internal
 */
final class NotFoundTest extends WebTestCase
{
    use ArraySubsetAsserts;

    public function testNotFound(): void
    {
        $this->client()->request('GET', '/not-found', server: ['HTTP_ACCEPT' => 'application/json']);

        self::assertResponseStatusCodeSame(404);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());

        $data = Json::decode($body);

        self::assertArraySubset([
            'title' => 'An error occurred',
        ], $data);
    }
}
