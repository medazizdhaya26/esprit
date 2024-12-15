<?php
namespace App\Command;

use App\Entity\Evenement;
use App\Entity\Reservation;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:send-event-emails',
    description: 'Fetch all events and send reminder emails to participants',
)]
class SendEventEmailsCommand extends Command
{
    private $mailer;
    private $entityManager;

    public function __construct(MailerInterface $mailer, EntityManagerInterface $entityManager)
    {
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Fetch all events and send emails to all participants.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $tomorrow = new \DateTimeImmutable('+1 day');

        $events = $this->entityManager->getRepository(Evenement::class)->findBy(['valider' => true,
            'date_debut' => $tomorrow,
        ]);

        if (empty($events)) {
            $io->success('No validated events found.');
            return Command::SUCCESS;
        }

        foreach ($events as $event) {
            $reservations = $event->getReservations();

            foreach ($reservations as $reservation) {
                if (!$reservation->getEmail()) {
                    continue;
                }


                try {
                    $email = (new Email())
                        ->from('azizdhaya26@gmail.com')
                        ->to($reservation->getEmail())
                        ->subject('Reminder: Upcoming Event ' . $event->getTitreevets())
                        ->html(
                            '<html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Event Reminder</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 0;
                        padding: 0;
                        background-color: #f4f4f4;
                        color: #333;
                    }
                    .container {
                        width: 100%;
                        max-width: 600px;
                        margin: 20px auto;
                        background-color: #fff;
                        border-radius: 8px;
                        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                        padding: 20px;
                    }
                    .header {
                        background-color: #a11515;
                        color: white;
                        text-align: center;
                        padding: 10px 0;
                        border-radius: 8px 8px 0 0;
                    }
                    .content {
                        padding: 20px;
                    }
                    .content p {
                        line-height: 1.6;
                    }
                    .footer {
                        text-align: center;
                        font-size: 12px;
                        color: #888;
                        padding-top: 20px;
                    }
                    .event-details {
                        background-color: #f9f9f9;
                        padding: 15px;
                        border-radius: 8px;
                        margin-top: 20px;
                        box-shadow: 0 1px 6px rgba(0, 0, 0, 0.1);
                    }
                    .event-details h3 {
                        margin-top: 0;
                    }
                    .event-details .label {
                        font-weight: bold;
                        color: #333;
                    }
                    .event-details .value {
                        color: #555;
                    }
                    .button {
                        background-color: #28a745;
                        color: white;
                        padding: 10px 20px;
                        text-decoration: none;
                        border-radius: 5px;
                        display: inline-block;
                        margin-top: 20px;
                    }
                    .button:hover {
                        background-color: #218838;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>Event Reminder: ' . $event->getTitreevets() . '</h2>
                    </div>
                    <div class="content">
                        <p>Dear ' . $reservation->getNom() . ',</p>
                        <p>This is a friendly reminder for the upcoming event <strong>' . $event->getTitreevets() . '</strong>.</p>
                        
                        <div class="event-details">
                            <h3>Event Details</h3>
                            <p><span class="label">Description:</span> <span class="value">' . $event->getDescription() . '</span></p>
                            <p><span class="label">Start Date:</span> <span class="value">' . $event->getDateDebut()->format('l, F j, Y') . '</span></p>
                            <p><span class="label">Location:</span> <span class="value">' . $event->getLieu() . '</span></p>
                        </div>
                        
                        <p>We look forward to seeing you there!</p>

                        <a href="#" class="button">RSVP Now</a>
                    </div>
                    <div class="footer">
                        <p>If you have any questions, feel free to contact us at <a href="mailto:support@yourdomain.com">admin@esprit.com</a>.</p>
                        <p>&copy; ' . date('Y') . ' ESPRIT. All rights reserved.</p>
                    </div>
                </div>
            </body>
        </html>'
                        );

                    // Send the email
                    $this->mailer->send($email);
                    $io->success('Email sent to: ' . $reservation->getEmail());
                } catch (\Exception $e) {
                    $io->error('Failed to send email to ' . $reservation->getEmail() . ': ' . $e->getMessage());
                }
            }
        }

        return Command::SUCCESS;
    }
}
