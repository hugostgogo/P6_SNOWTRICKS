<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\Tools\Pagination\Paginator;

use App\Entity\Trick;

class AppController extends AbstractController
{
    private $em;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->em = $doctrine->getManager();
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('app/index.html.twig', [
            'tricks' => $this->em->getRepository(Trick::class)->getPage(1),
            'pageIndex' => 1
        ]);
    }

    #[Route('/ajax/tricks/{page}', name: 'ajax_route_tricks')]
    public function tricks($page = 1): Response
    {
        return $this->render('tricks/ajaxResponse.html.twig', [
            'tricks' => $this->em->getRepository(Trick::class)->getPage($page),
            'pageIndex' => $page
        ]);
    }
}
