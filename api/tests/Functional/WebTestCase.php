<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

abstract class WebTestCase extends BaseWebTestCase
{
    private ?KernelBrowser $client = null;
    private ?MailerClient $mailer = null;

    protected function tearDown(): void
    {
        $this->client = null;

        parent::tearDown();
    }

    protected function client(): KernelBrowser
    {
        if (null === $this->client) {
            $this->client = $this->createClient();
        }

        return $this->client;
    }

    protected function mailer(): MailerClient
    {
        if (null === $this->mailer) {
            $this->mailer = new MailerClient();
        }

        return $this->mailer;
    }

    /**
     * @param array<int|string, string> $fixtures
     */
    protected function loadFixtures(array $fixtures): void
    {
        $container = $this->client()->getContainer();
        $loader = new Loader();

        foreach ($fixtures as $class) {
            /** @var AbstractFixture $fixture */
            $fixture = $container->get($class);
            $loader->addFixture($fixture);
        }

        $em = $container->get(EntityManagerInterface::class);

        $executor = new ORMExecutor($em, new ORMPurger());
        $executor->execute($loader->getFixtures());
    }
}
