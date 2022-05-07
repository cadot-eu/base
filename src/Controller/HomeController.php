<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\base\FixtureHelper;
use App\Service\base\FileUploader;

class HomeController extends AbstractController
{

    #[Route('/', name: 'home_index')]
    public function index(Request $request, FixtureHelper $fixtureHelper): Response
    {
        /* ------------------------------- simulateurs ------------------------------ */
        return $this->render('home/index.html.twig', ['testhelper' => $fixtureHelper::generate(['type' => 'phrase'])]);
    }



    #[Route('/admin', name: 'admin_index')]
    public function admin(): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('home_index');
        }
        if ($this->getUser()->isVerified()) {
            return $this->render('admin/accountvalidated.html.twig', []);
        }
        return $this->render('admin/accountnotvalidated.html.twig', []);
    }

    //ajax
    #[Route('/upload/{name}', name: 'upload')]
    public function upload(FileUploader $fileUploader, Request $request, string $name): Response
    {

        return new JsonResponse(['url' => '/' . $fileUploader->upload($request->files->get('upload'), $name . '/')]);
    }
}
