<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Service\FileUploader;
use App\Entity\Photo;
use App\Form\CommentType;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/tricks')]
class TricksController extends AbstractController
{
    private $em;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->em = $doctrine->getManager();
    }

    #[Route('/', name: 'app_tricks_index', methods: ['GET'])]
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('tricks/index.html.twig', [
            'tricks' => $this->em->getRepository(Trick::class)->getPage(1),
            'pageIndex' => 1
        ]);
    }

    #[Route('/new', name: 'app_tricks_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        TrickRepository $trickRepository,
        FileUploader $fileUploader,
        ManagerRegistry $doctrine
    ): Response {
        $em = $doctrine->getManager();

        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $photo = new Photo();
                $filename = $fileUploader->upload($photoFile);
                $photo->setPath("/uploads/photos/$filename")->setDescription("$filename");

                $em->persist($photo);

                $trick->setCover($photo);
            }

            $photoFiles = $form->get('photos')->getData();
            if ($photoFiles) {
                foreach ($photoFiles as $photoFile) {
                    $photo = new Photo();
                    $filename = $fileUploader->upload($photoFile);
                    $photo
                        ->setPath("/uploads/photos/$filename")
                        ->setDescription("$filename")
                        ->setTrick($trick);

                    $em->persist($photo);
                }
            }

            $categories = $form->get('category')->getData();
            if ($categories) {
                foreach ($categories as $category) {
                    $trick->addCategory($category);
                    $em->persist($category);
                }
            }

            $trickRepository->add($trick);
            return $this->redirectToRoute(
                'app_tricks_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('tricks/new.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tricks_show', methods: ['GET', 'POST'])]
    public function show(Trick $trick, Request $request): Response
    {
        $comment = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setAuthor($this->getUser());
            $trick->addComment($comment);

            $this->em->persist($comment);
            $this->em->persist($trick);
            $this->em->flush();
        }

        return $this->renderForm('tricks/show.html.twig', [
            'trick' => $trick,
            'commentForm' => $commentForm,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_tricks_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Trick $trick,
        TrickRepository $trickRepository
    ): Response {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickRepository->add($trick);
            return $this->redirectToRoute(
                'app_tricks_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('tricks/edit.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_tricks_delete', methods: ['GET'])]
    public function delete(
        Request $request,
        Trick $trick,
        TrickRepository $trickRepository
    ): Response {
        if (
            $this->isCsrfTokenValid(
                'delete' . $trick->getId(),
                $request->request->get('_token')
            )
        ) {
            $trickRepository->remove($trick);
        }

        return $this->redirectToRoute(
            'app_tricks_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}
