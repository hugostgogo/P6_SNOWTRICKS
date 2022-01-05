<?php
// src/Controller/BlogController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="home")
     */
    public function list()
    {
        $number = random_int(0, 15);
        return $this->render('home.html.twig', [
            "random" => $number,
            "content" => "Salut !!!!"
        ]);
    }
}

?>