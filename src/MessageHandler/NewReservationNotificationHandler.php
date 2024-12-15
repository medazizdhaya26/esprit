<?php

namespace App\MessageHandler;

use App\Entity\Notification;
use App\Entity\ReservChambre;
use App\Entity\User;
use App\Message\NewReservationNotificationMessage;
use Doctrine\ORM\EntityManagerInterface;
use ErrorException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class NewReservationNotificationHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $entityManager;
   
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        
    }

    /**
     * @param NewBugAddNotificationMessage $message
     * @return void
     * @throws ErrorException
     */
    public function __invoke(NewReservationNotificationMessage $message): void
    {
        $em = $this->entityManager;
        $reservChambre = $this->entityManager->getRepository(ReservChambre::class)->findOneBy(['id' => $message->getReservationId()]);

        $usersWithRoleAdmins = $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%"ROLE_ADMIN"%') // Match the role in the roles array
            ->getQuery()
            ->getResult();
           
        foreach($usersWithRoleAdmins as $usersWithRoleAdmin)
        {
            $notification = new Notification();
            $notification->setType('New Reservation #' . $reservChambre->getId());
            $notification->setIdType($message->getReservationId());
            $notification->setMessage('A new reservation has been made: Reservation #' . $reservChambre->getId());
            $notification->setRead(false);
            $notification->setIdUser($usersWithRoleAdmin->getId());
            $em->persist($notification);
        }

        $em->flush();
    }
}