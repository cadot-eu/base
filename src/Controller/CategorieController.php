<?php

//Here for add your Code //end of your code
namespace  App\Controller;

//Here for add your Code //end of your code
use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use App\Service\FileUploader;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//Here for add your Code //end of your code
/**
 * @Route("admin/categorie")
 */ class CategorieController extends AbstractController
{
    //Here for add your Code //end of your code
    /* -------------------------------------------------------------------------- */
    /*                                    INDEX                                   */
    /* -------------------------------------------------------------------------- */
    /**
     * @Route("/", name="categorie_index", methods={"GET"})
     */
    public function index(CategorieRepository $categorieRepository, Request $request): Response
    {
        //Here for add your Code //end of your code
        $page = $request->query->get("page") != null ? $request->query->get("page") : 1;
        $maxi = count($categorieRepository->findBy([
            'deletedAt' => null,
        ]));
        if ($page * 10 > $maxi) {
            $page = round($maxi / 10, 0);
        }
        $tri = $request->query->get("tri") != null ? [
            $request->query->get("tri") => $request->query->get("ordre") ?: 'ASC',
        ] : [];
        $categories = $categorieRepository->findBy([
            'deletedAt' => null,
        ], $tri, 10, ($page - 1) * 10);
        //Here for add your Code //end of your code
        return $this->render('/categorie/index.html.twig', [
            /*¤index_render¤*/
            'categories' => $categories,
            'pagesMaxi' => $maxi,
        ]);
        //Here for add your Code //end of your code
    }

    //Here for add your Code //end of your code
    /* -------------------------------------------------------------------------- */
    /*                                   DELETED                                  */
    /* -------------------------------------------------------------------------- */
    /**
     * @Route("/deleted", name="categorie_deleted", methods={"GET"})
     */
    public function deleted(CategorieRepository $categorieRepository, Request $request): Response
    {
        //Here for add your Code //end of your code
        $tabCategories = [];
        foreach ($categorieRepository->findAll() as $categorie) {
            if ($categorie->getDeletedAt() != null) {
                $tabCategories[] = $categorie;
            }
        }
        $page = $request->query->get("page") != null ? $request->query->get("page") : 1;
        $maxi = count($tabCategories);
        if ($page * 10 > $maxi) {
            $page = round($maxi / 10, 0);
        }
        $tri = $request->query->get("tri") != null ? [
            $request->query->get("tri") => $request->query->get("ordre") ?: 'ASC',
        ] : [];
        $categories = array_slice($tabCategories, ($page - 1) * 10, 10);

        //Here for add your Code //end of your code
        return $this->render('/categorie/index.html.twig', [
            //Here for add your Code //end of your code
            'categories' => $tabCategories,
            'pagesMaxi' => $maxi,
        ]);
    }

    //Here for add your Code //end of your code
    /* -------------------------------------------------------------------------- */
    /*                                NEW AND EDIT                                */
    /* -------------------------------------------------------------------------- */
    /**
     * @Route("/new", name="categorie_new", methods={"GET","POST"})
     *  @Route("/{id}/edit", name="categorie_edit", methods={"GET","POST"})
     */
    public function new(Request $request, FileUploader $fileUploader, Categorie $categorie = null, EntityManagerInterface $em): Response
    {
        //Here for add your Code //end of your code
        if (! $categorie) {
            $categorie = new Categorie();
        } //for new
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        //Here for add your Code //end of your code
        if ($form->isSubmitted() && $form->isValid()) {
            //Here for add your Code //end of your code
            if ($request->files->get('categorie')) {
                foreach ($request->files->get('categorie') as $name => $data) {
                    $fichier = $form->get($name)->getData();
                    //Here for add your Code //end of your code
                    if ($fichier) {
                        //Here for add your Code //end of your code
                        if (get_class($fichier) == 'Doctrine\Common\Collections\ArrayCollection' || get_class($fichier) == "Doctrine\ORM\PersistentCollection") {
                            $fichierName = [];
                            foreach ($fichier as $num => $fiche) {
                                if ($data[$num][key($data[$num])] != null) {
                                    $class = explode('\\', get_class($fiche));
                                    $fichierName = $fileUploader->upload($data[$num][key($data[$num])], "categorie/$name/" . key($data[$num]) . '/');
                                    $functionE = 'set' . ucfirst(key($data[$num]));
                                    $fiche->$functionE($fichierName);
                                    $function = 'add' . end($class);
                                    $categorie->$function($fiche);
                                }
                            }
                        } else {
                            $fichierName = $fileUploader->upload($fichier, "categorie/$name/");
                            $function = 'set' . $name;
                            $categorie->$function($fichierName);
                        }
                        //Here for add your Code //end of your code
                    }
                    //Here for add your Code //end of your code
                }
            }
            //Here for add your Code //end of your code
            /* -------------------------------- language -------------------------------- */
            // $list = $request->request->get('categorie');
            // $tab = [];
            // foreach ($list as $name => $data) {
            //     if (strpos($name, '_LANGUAGE_') !== false) {
            //         $exp = explode('_LANGUAGE_', $name);
            //         $tab[$exp[0]][$exp[1]] = $data;
            //     }
            // }
            // foreach ($tab as $name => $value) {
            //     $set = 'set' . ucfirst($name);
            //     $categorie->$set(json_encode($value));
            // }
            //Here for add your Code //end of your code
            //TODO: par listener
            if ($categorie->getcreatedAt() == 'null') {
                $categorie->setCreatedAt(new DateTime('now'));
            }
            $categorie->setUpdatedAt(new DateTime('now'));
            $em->persist($categorie);
            $em->flush();
            //Here for add your Code //end of your code
            return $this->redirectToRoute('categorie_index');
        }
        //Here for add your Code //end of your code
        return $this->render('/categorie/new.html.twig', [
            //Here for add your Code //end of your code
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);
    }

    //Here for add your Code //end of your code
    /* -------------------------------------------------------------------------- */
    /*                                    SHOW                                    */
    /* -------------------------------------------------------------------------- */
    /**
     * @Route("/{id}", name="categorie_show", methods={"GET"})
     */
    public function show(Categorie $categorie): Response
    {
        //Here for add your Code //end of your code
    }

    //Here for add your Code //end of your code
    /* -------------------------------------------------------------------------- */
    /*                                    CLONE                                   */
    /* -------------------------------------------------------------------------- */
    /**
     * @Route("/{id}/clone", name="categorie_clone", methods={"GET","POST"})
     */
    public function clone(Categorie $categoriec, EntityManagerInterface $em): Response
    {
        //Here for add your Code //end of your code
        $categorie = clone $categoriec;
        if (property_exists($categorie, 'slug')) {
            $sfunc = 'set' . ucfirst('nom');
            $gfunc = 'get' . ucfirst('nom');
            $categorie->$sfunc($categorie->$gfunc() . uniqid());
            $categorie->setslug();
        }
        $em = $this->getDoctrine()->getManager();
        $categorie->setCreatedAt(new DateTime('now'));
        $em->persist($categorie);
        $em->flush();
        //Here for add your Code //end of your code
        return $this->redirectToRoute('categorie_index');
    }

    //Here for add your Code //end of your code
    /* -------------------------------------------------------------------------- */
    /*                                   DELETE                                   */
    /* -------------------------------------------------------------------------- */
    /**
     * @Route("/{id}", name="categorie_delete", methods={"POST"})
     */
    public function delete(Request $request, Categorie $categorie, EntityManagerInterface $em): Response
    {
        //Here for add your Code //end of your code
        if ($this->isCsrfTokenValid('delete' . $categorie->getId(), $request->request->get('_token'))) {
            //Here for add your Code //end of your code
            if ($request->request->has('delete_delete')) {
                //Here for add your Code //end of your code
                $em->remove($categorie);
            }
            if ($request->request->has('delete_restore')) {
                $categorie->setDeletedAt(null);
            }
            if ($request->request->has('delete_softdelete')) {
                $categorie->setDeletedAt(new DateTime('now'));
            }
            //Here for add your Code //end of your code
            $em->flush();
        }
        //Here for add your Code //end of your code
        if ($request->request->has('delete_softdelete')) {
            return $this->redirectToRoute('categorie_index');
        } else {
            return $this->redirectToRoute('categorie_deleted');
        }
    }
    //Here for add your Code //end of your code
}
//Here for add your Code //end of your code
