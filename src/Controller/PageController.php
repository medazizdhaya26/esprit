<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{
    #[Route('/', name: 'app_main_page')]
    public function index(): Response
    {
        return $this->render('page/index.html.twig');
    }
    #[Route('/home', name: 'app_homepage')]
    public function home(): Response
    {
        return $this->render('page/home.html.twig');
    }
    #[Route('/Contact', name: 'app_page_contact')]
    public function contact(): Response
    {
        return $this->render('page/contact.html.twig');
    }
    #[Route('/Events/', name: 'app_page_event')]
    public function event(): Response
    {
        return $this->render('page/Events/index.html.twig');
    }
    #[Route('/login', name: 'app_page_login')]
    public function login(): Response
    {
        return $this->render('page/login.html.twig');
    }
    #[Route('/Events/Events_details', name: 'app_page_Event_details')]
    public function details(): Response
    {
        return $this->render('page/Events/Events_details.html.twig');
    }

    #[Route('/dashboared/', name: 'app_page_Dashboared')]
    public function dashboared(): Response
    {
        return $this->render('page/admin/index.html.twig');
    }

}
