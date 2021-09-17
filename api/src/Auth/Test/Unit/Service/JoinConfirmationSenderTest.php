<?php

declare(strict_types=1);

namespace App\Auth\Test\Unit\Service;

use App\Auth\Entity\User\Email;
use App\Auth\Entity\User\Token;
use App\Auth\Service\JoinConfirmationSender;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

/**
 * @internal
 * @covers \App\Auth\Service\JoinConfirmationSender
 */
final class JoinConfirmationSenderTest extends TestCase
{
    public function testSuccess(): void
    {
        $to = new Email('user@app.test');
        $token = new Token(Uuid::uuid4()->toString(), new DateTimeImmutable());

        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::once())->method('send')
            ->willReturnCallback(static function (TemplatedEmail $email) use ($to, $token): int {
                self::assertEquals('auth/join/confirm.html.twig', $email->getHtmlTemplate());
                self::assertEquals('Join Confirmation', $email->getSubject());
                self::assertEquals(['token' => $token], $email->getContext());
                self::assertCount(1, $address = $email->getTo());
                self::assertInstanceOf(Address::class, $first = reset($address));
                self::assertEquals($to->getValue(), $first->getAddress());
                self::assertEmpty($first->getName());

                return 1;
            });

        $sender = new JoinConfirmationSender($mailer);

        $sender->send($to, $token);
    }
}
