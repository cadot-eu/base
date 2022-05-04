<?php

namespace App\Controller;

use App\Entity\Carousel;
use App\Repository\CarouselRepository;
use App\Repository\ArticleRepository;
use App\Repository\ParametresRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\ToolsHelper;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{

    #[Route('/', name: 'home_index')]
    public function index(ArticleRepository $articleRepository, CarouselRepository $carouselRepository, Request $request): Response
    {
        /* ------------------------------ article ------------------------------ */
        $articlePlusVu = $articleRepository->findBy(['deletedAt' => null, 'etat' => 'en ligne'], ['vues' => 'DESC']);
        $article = $articleRepository->findBy(['deletedAt' => null, 'etat' => 'en ligne'], ['updatedAt' => 'DESC'], 8, 0);
        /* ------------------------------- simulateurs ------------------------------ */
        return $this->render('home/index.html.twig', [
            /* ------------------------------ article ------------------------------ */
            'article' => [
                'dernier' => isset($article[0]) ? $article[0] : null,
                'articles' => $article,
                'best' => $articlePlusVu
            ],
            'carousels' => $carouselRepository->findBy(['deletedAt' => null, 'etat' => 'en ligne'], ['updatedAt' => 'DESC'])
        ]);
    }



    #[Route('/admin', name: 'admin_index')]
    public function admin(): Response
    {
        if ($this->getUser() == null)
            return $this->redirectToRoute('home_index');
        if ($this->getUser()->isVerified())
            return $this->render('admin/accountvalidated.html.twig', []);
        return $this->render('admin/accountnotvalidated.html.twig', []);
    }

    #[Route('/upload/{name}', name: 'upload')]
    public function upload(FileUploader $fileUploader, Request $request, string $name): Response
    {

        return new JsonResponse(['url' => '/' . $fileUploader->upload($request->files->get('upload'), $name . '/')]);
    }
}
