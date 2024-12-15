<?php

namespace App\Controller;

use App\Entity\ReservChambre;
use App\Manager\Manager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboared/reservation')]
final class ValideResverController extends AbstractController
{
    #[Route('/', name: 'app_reservation_index', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
{
    // Récupérer le paramètre de recherche du email du reservation
    $searchTerm = $request->query->get('search', '');
    $sortBy = $request->query->get('sort_by', 'id');
    $sortOrder = $request->query->get('sort_order', 'asc');

    // Effectuer la recherche et le tri
    $queryBuilder = $entityManager->getRepository(ReservChambre::class)->createQueryBuilder('res');

    if ($searchTerm) {
        $queryBuilder->where('res.email LIKE :search')
            ->setParameter('search', '%' . $searchTerm . '%');
    }

    $reservations = $queryBuilder->getQuery()->getResult();

    return $this->render('page/admin/Entity/reservationchambre/index.html.twig', [
        'reservations' => $reservations,
        'searchTerm' => $searchTerm,
        'sortBy' => $sortBy,
        'sortOrder' => $sortOrder,  // Pass the sorting parameters to the template
    ]);
}

    #[Route('/verify-reservation', name: 'app_verify_reservation', methods: ['POST'])]
    public function verifyReservation(Request $request, EntityManagerInterface $entityManager, Manager $userManager): JsonResponse
    {
        $reservationId = $request->request->get('reservationId');
        $reservation = $entityManager->getRepository(ReservChambre::class)->find($reservationId);

        if (!$reservation) {
            return new JsonResponse(['success' => false, 'message' => 'Réservation introuvable.']);
        }

        // Update reservation status and chambre
        $reservation->setValidate(true);
        $chambre = $reservation->getChambre();
        
        if (!$chambre) {
            // Handle case if chambre is not found for the reservation
            return new JsonResponse(['success' => false, 'message' => 'Chambre non trouvée pour cette réservation.']);
        }
        
        if ($chambre->getPlacesDisponibles() <= 0) {
            return new JsonResponse(['success' => false, 'message' => 'La chambre sélectionnée n’a plus de places disponibles.']);
        }
        
         // Réduction du nombre de places disponibles
        $chambre->setPlacesDisponibles($chambre->getPlacesDisponibles() - 1);
        if ($chambre->getPlacesDisponibles() <= 0) {
               $chambre->setEstDisponible(false);
        }

        $userManager->sendMail($reservation);
        
        //flush
        $entityManager->flush();

        return new JsonResponse(['success' => true, 'message' => 'Réservation vérifiée avec succès.']);
    }
    #[Route('/delete/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, $id, EntityManagerInterface $entityManager): Response
    {
        $reservation = $entityManager->getRepository(ReservChambre::class)->findOneBy(['id' =>$id]);

        if (!$reservation) {
            throw $this->createNotFoundException('No Reservation Room found for id ' . $id);
        }

        if ($reservation->isValidate() == true){
            $chambre = $reservation->getChambre();

            $chambre->setPlacesDisponibles($chambre->getPlacesDisponibles() + 1);
            if ($chambre->getPlacesDisponibles() >= 0) {
                $chambre->setEstDisponible(true);
            }
        }

        $entityManager->remove($reservation);
        $entityManager->flush();
        
        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}


