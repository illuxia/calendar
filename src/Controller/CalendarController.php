<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\ReservationTime;
use App\Entity\Reservation;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class CalendarController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('calendar/index.html.twig');
    }

    /**
     * @Route("/day/{day}/month/{month}/year/{year}", name="reservations")
     */
    public function showReservations($day, $month, $year): JsonResponse
    {
        $date = null;
        try {
            $date = date( 'Y-m-d', strtotime( $month . ' ' . $day. ', ' . $year ) );
            if (substr($date, 0, 4) == '1970') {
                return new JsonResponse(['message' => 'Wrong date!'], 400);
            }
        } 
        catch (Exception $e) {
            return new JsonResponse(['message' => 'Wrong date!'], 400);
        }
        
        $reservationTimes = $this->getDoctrine()->getRepository(ReservationTime::class)->findAll();
        $reservationRecords = $this->getDoctrine()->getRepository(Reservation::class)->findAllReservationsForDate($date);

        $reservationHours = [];
        foreach($reservationTimes as $hour) {
            $reservationHours[] = $hour->getTime()->format('H:i:s');
        }

        $reservations = [];
        foreach($reservationRecords as $reservation) {
            $reservations[] = $reservation->getDate()->format('H:i:s');
        }

        return new JsonResponse(array('reservationHours' => $reservationHours,
                                      'reservations' => $reservations));
    }

    /**
     * @Route("/reservation/save", name="save_reservation")
     */
    public function saveReservation(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        try {
            $submittedToken = $request->request->get('token');
            if (!$this->isCsrfTokenValid('make-reservation', $submittedToken)) {
                return new JsonResponse(['message' => 'Invalid token!'], 400);
            }

            $date = date( 'Y-m-d', strtotime( $request->request->get('month') . ' ' . $request->request->get('day') . ', ' . $request->request->get('year') ) );
            $time = date( 'H:i:s', strtotime($request->request->get('time')) );
            $datetime = new \DateTime(date('Y-m-d H:i:s', strtotime("$date $time")));

            $reservation = new Reservation();
            $reservation->setName($request->request->get('name'));
            $reservation->setEmail($request->request->get('email'));
            $reservation->setDate($datetime);

            $errors = $validator->validate($reservation);

            if (count($errors) > 0) {
                $errorsList = [];
                foreach($errors as $error) {
                    $errorsList[] = $error->getMessage();
                }
                return new JsonResponse($errorsList, 400);
            }
            
            $entityManager->persist($reservation);
            $entityManager->flush();
            return new JsonResponse(['message' => 'Reservation success!'], 200);

        } 
        catch (UniqueConstraintViolationException $e) {
            return new JsonResponse(['message' => 'Reservation for this day and time already exist!'], 500);
        } 
        catch (Exception $e) {
            return new JsonResponse(['message' => 'Failed to save reservation to database'], 500);
        }
    }
}
