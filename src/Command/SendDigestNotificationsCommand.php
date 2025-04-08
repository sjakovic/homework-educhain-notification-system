<?php

namespace App\Command;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

#[AsCommand(
    name: 'app:send-digest-notifications',
    description: 'Sends daily or weekly digest notifications to users',
)]
class SendDigestNotificationsCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private MailerInterface $mailer,
        private Environment $twig,
        private TranslatorInterface $translator,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->em->getConnection()->fetchAllAssociative(
            'SELECT DISTINCT user_id FROM notification WHERE digest = true AND sent = false'
        );

        foreach ($users as $userRow) {
            $userId = $userRow['user_id'];

            $notifications = $this->em->getRepository(Notification::class)->findBy([
                'userId' => $userId,
                'digest' => true,
                'sent' => false,
            ]);

            if (count($notifications) === 0) {
                continue;
            }

            // 🔁 Simulated — later fetch from user profile
            $language = 'es';
            $emailAddress = 'student@example.com';

            // 🧠 Translated subject
            $subject = $this->translator->trans(
                'notification.subject.digest',
                [],
                'messages',
                $language
            );

            $html = $this->twig->render("emails/notifications/digest.$language.html.twig", [
                'notifications' => $notifications,
            ]);

            $email = (new Email())
                ->from(new Address('no-reply@educhain.com', 'Educhain'))
                ->to($emailAddress)
                ->subject($subject)
                ->html($html);

            $this->mailer->send($email);

            foreach ($notifications as $n) {
                $n->setSent(true);
            }
        }

        $this->em->flush();
        $output->writeln('✅ Digest notifications sent successfully.');

        return Command::SUCCESS;
    }
}
