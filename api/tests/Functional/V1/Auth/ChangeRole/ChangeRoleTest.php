<?php

declare(strict_types=1);

namespace App\Tests\Functional\V1\Auth\ChangeRole;

use App\Auth\Entity\User\Id;
use App\Auth\Entity\User\Role;
use App\Tests\Functional\Json;
use App\Tests\Functional\UserIdentityMother;
use App\Tests\Functional\WebTestCase;

/**
 * @internal
 */
final class ChangeRoleTest extends WebTestCase
{
    private const URI = '/v1/auth/users/%s/role';

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures([
            ChangeRoleFixture::class,
        ]);
    }

    public function testMethod(): void
    {
        $this->client()->request('POST', sprintf(self::URI, ChangeRoleFixture::USER));

        $this->assertResponseStatusCodeSame(405);
    }

    public function testGuest(): void
    {
        $this->client()->request('PUT', sprintf(self::URI, ChangeRoleFixture::USER), content: Json::encode([
            'role' => Role::ADMIN,
        ]));

        $this->assertResponseStatusCodeSame(401);
    }

    public function testUser(): void
    {
        $this
            ->authorizedClient(UserIdentityMother::user()->build())
            ->request('PUT', sprintf(self::URI, ChangeRoleFixture::USER), content: Json::encode([
                'role' => Role::ADMIN,
            ]));

        $this->assertResponseStatusCodeSame(403);
    }

    public function testSuccess(): void
    {
        $this
            ->authorizedClient(UserIdentityMother::admin()->build())
            ->request('PUT', sprintf(self::URI, ChangeRoleFixture::USER), content: Json::encode([
                'role' => Role::ADMIN,
            ]));

        $this->assertResponseIsSuccessful();
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([], Json::decode($body));
    }

    public function testFakeId(): void
    {
        $this
            ->authorizedClient(UserIdentityMother::admin()->build())
            ->request('PUT', sprintf(self::URI, ChangeRoleFixture::USER), content: Json::encode([
                'id' => Id::generate()->getValue(),
                'role' => Role::ADMIN,
            ]));

        $this->assertResponseIsSuccessful();
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([], Json::decode($body));
    }

    public function testEmpty(): void
    {
        $this
            ->authorizedClient(UserIdentityMother::admin()->build())
            ->request('PUT', sprintf(self::URI, ChangeRoleFixture::USER), content: '{}');

        $this->assertResponseStatusCodeSame(422);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'errors' => [
                'role' => 'This value should not be blank.',
            ],
        ], Json::decode($body));
    }

    public function testEmptyLang(): void
    {
        $this
            ->authorizedClient(UserIdentityMother::admin()->build())
            ->request(
                'PUT',
                sprintf(self::URI, ChangeRoleFixture::USER),
                server: ['HTTP_ACCEPT_LANGUAGE' => 'ru-RU;q=0.9,en-US;q=0.5'],
                content: '{}'
            );

        $this->assertResponseStatusCodeSame(422);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'errors' => [
                'role' => 'Значение не должно быть пустым.',
            ],
        ], Json::decode($body));
    }

    public function testYourself(): void
    {
        $this
            ->authorizedClient(
                UserIdentityMother::admin()
                    ->withId(ChangeRoleFixture::ADMIN)
                    ->build()
            )
            ->request('PUT', sprintf(self::URI, ChangeRoleFixture::ADMIN), content: Json::encode([
                'role' => Role::USER,
            ]));

        $this->assertResponseStatusCodeSame(400);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'message' => 'Unable change role to yourself.',
        ], Json::decode($body));
    }

    public function testYourselfLang(): void
    {
        $this
            ->authorizedClient(
                UserIdentityMother::admin()
                    ->withId(ChangeRoleFixture::ADMIN)
                    ->build()
            )
            ->request(
                'PUT',
                sprintf(self::URI, ChangeRoleFixture::ADMIN),
                server: ['HTTP_ACCEPT_LANGUAGE' => 'ru-RU'],
                content: Json::encode(['role' => Role::USER])
            );

        $this->assertResponseStatusCodeSame(400);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'message' => 'Нельзя изменить свою роль.',
        ], Json::decode($body));
    }

    public function testAlready(): void
    {
        $this
            ->authorizedClient(UserIdentityMother::admin()->build())
            ->request('PUT', sprintf(self::URI, ChangeRoleFixture::USER), content: Json::encode([
                'role' => Role::USER,
            ]));

        $this->assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'message' => 'Role is already same.',
        ], Json::decode($body));
    }

    public function testAlreadyLang(): void
    {
        $this
            ->authorizedClient(UserIdentityMother::admin()->build())
            ->request(
                'PUT',
                sprintf(self::URI, ChangeRoleFixture::USER),
                server: ['HTTP_ACCEPT_LANGUAGE' => 'ru'],
                content: Json::encode(['role' => Role::USER])
            );

        $this->assertResponseStatusCodeSame(409);
        self::assertJson($body = (string) $this->client()->getResponse()->getContent());
        self::assertEquals([
            'message' => 'Роль совпадает с текущей.',
        ], Json::decode($body));
    }
}
