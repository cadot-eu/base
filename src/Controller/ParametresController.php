<?php

//Here for add your Code //end of your code
namespace  App\Controller;

//Here for add your Code //end of your code
use App\Entity\Parametres;
use App\Form\ParametresType;
use App\Repository\ParametresRepository;
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
 * @Route("admin/parametres")
 */ class ParametresController extends AbstractController
{
    //Here for add your Code //end of your code
    protected $em;

    public function __construct(
        EntityManagerInterface $em
        //Here for add your Code //end of your code
    ) {
        $this->em = $em;
        //Here for add your Code //end of your code
    }

    //Here for add your Code //end of your code
    /* -------------------------------------------------------------------------- */
    /*                                    INDEX                                   */
    /* -------------------------------------------------------------------------- */
    /**
     * @Route("/", name="parametres_index", methods={"GET"})
     */
    public function index(ParametresRepository $parametresRepository, Request $request): Response
    {
        //Here for add your Code //end of your code
        $page = $request->query->get("page") != null ? $request->query->get("page") : 1;
        $maxi = count($parametresRepository->findBy([
            'deletedAt' => null,
        ]));
        if ($maxi > 10 && $page * 10 > $maxi) {
            $page = round($maxi / 10, 0);
        }
        $tri = $request->query->get("tri") != null ? [
            $request->query->get("tri") => $request->query->get("ordre") ?: 'ASC',
        ] : [
            'nom' => 'ASC',
        ];
        $parametress = $parametresRepository->findBy([
            'deletedAt' => null,
        ], $tri, 10, ($page - 1) * 10);
        //Here for add your Code //end of your code
        return $this->render('/parametres/index.html.twig', [
            /*¤index_render¤*/
            'parametress' => $parametress,
            'pagesMaxi' => $maxi,
        ]);
        //Here for add your Code //end of your code
    }

    //Here for add your Code //end of your code
    /* -------------------------------------------------------------------------- */
    /*                                   DELETED                                  */
    /* -------------------------------------------------------------------------- */
    /**
     * @Route("/deleted", name="parametres_deleted", methods={"GET"})
     */
    public function deleted(ParametresRepository $parametresRepository, Request $request): Response
    {
        //Here for add your Code //end of your code
        $tabParametress = [];
        foreach ($parametresRepository->findAll() as $parametres) {
            if ($parametres->getDeletedAt() != null) {
                $tabParametress[] = $parametres;
            }
        }
        $page = $request->query->get("page") != null ? $request->query->get("page") : 1;
        $maxi = count($tabParametress);
        if ($page * 10 > $maxi) {
            $page = round($maxi / 10, 0);
        }
        $tri = $request->query->get("tri") != null ? [
            $request->query->get("tri") => $request->query->get("ordre") ?: 'ASC',
        ] : [
            'nom' => 'ASC',
        ];
        $parametress = array_slice($tabParametress, ($page - 1) * 10, 10);

        //Here for add your Code //end of your code
        return $this->render('/parametres/index.html.twig', [
            //Here for add your Code //end of your code
            'parametress' => $tabParametress,
            'pagesMaxi' => $maxi,
        ]);
    }

    //Here for add your Code //end of your code
    /* -------------------------------------------------------------------------- */
    /*                                    ETAT                                    */
    /* -------------------------------------------------------------------------- */
    /**
     * @Route("/etat/{id}/{type}/{valeur}", name="parametres_etat", methods={"GET"})
     */
    public function etat(Parametres $parametres, $type = null, $valeur = null): Response
    {
        //Here for add your Code //end of your code
        if ($type) {
            $method = 'set' . $type;
            $parametres->$method($valeur);
            $this->em->persist($parametres);
            $this->em->flush();
        }
        //Here for add your Code //end of your code
        return $this->redirectToRoute('parametres_index');
    }
    //Here for add your Code //end of your code

    /* -------------------------------------------------------------------------- */
    /*                                NEW AND EDIT                                */
    /* -------------------------------------------------------------------------- */
    /**
     * @Route("/new", name="parametres_new", methods={"GET","POST"})
     *  @Route("/{id}/edit", name="parametres_edit", methods={"GET","POST"})
     */
    public function new(Request $request, FileUploader $fileUploader, Parametres $parametres = null, EntityManagerInterface $em): Response
    {
        //Here for add your Code //end of your code
        if (! $parametres) {
            $parametres = new Parametres();
        } //for new
        //Here for add your Code //end of your code
        $form = $this->createForm(ParametresType::class, $parametres);
        //Here for add your Code //end of your code
        $form->handleRequest($request);
        //Here for add your Code //end of your code
        if ($form->isSubmitted() && $form->isValid()) {
            //Here for add your Code //end of your code
            if ($request->files->get('parametres')) {
                foreach ($request->files->get('parametres') as $name => $data) {
                    $fichier = $form->get($name)->getData();
                    //Here for add your Code //end of your code
                    if ($fichier) {
                        //Here for add your Code //end of your code
                        if (get_class($fichier) == 'Doctrine\Common\Collections\ArrayCollection' || get_class($fichier) == "Doctrine\ORM\PersistentCollection") {
                            $fichierName = [];
                            foreach ($fichier as $num => $fiche) {
                                if ($data[$num][key($data[$num])] != null) {
                                    $class = explode('\\', get_class($fiche));
                                    $fichierName = $fileUploader->upload($data[$num][key($data[$num])], "parametres/$name/" . key($data[$num]) . '/');
                                    $functionE = 'set' . ucfirst(key($data[$num]));
                                    $fiche->$functionE($fichierName);
                                    $function = 'add' . end($class);
                                    $parametres->$function($fiche);
                                }
                            }
                        } else {
                            $fichierName = $fileUploader->upload($fichier, "parametres/$name/");
                            $function = 'set' . $name;
                            $parametres->$function($fichierName);
                        }
                        //Here for add your Code //end of your code
                    }
                    //Here for add your Code //end of your code
                }
            }
            //Here for add your Code //end of your code
            /* -------------------------------- language -------------------------------- */
            // $list = $request->request->get('parametres');
            // $tab = [];
            // foreach ($list as $name => $data) {
            //     if (strpos($name, '_LANGUAGE_') !== false) {
            //         $exp = explode('_LANGUAGE_', $name);
            //         $tab[$exp[0]][$exp[1]] = $data;
            //     }
            // }
            // foreach ($tab as $name => $value) {
            //     $set = 'set' . ucfirst($name);
            //     $parametres->$set(json_encode($value));
            // }
            //Here for add your Code //end of your code
            //TODO: par listener
            if ($parametres->getcreatedAt() == 'null') {
                $parametres->setCreatedAt(new DateTime('now'));
            }
            $parametres->setUpdatedAt(new DateTime('now'));
            $em->persist($parametres);
            $em->flush();
            //Here for add your Code //end of your code
            return $this->redirectToRoute('parametres_index');
        }
        //Here for add your Code //end of your code
        return $this->render('/parametres/new.html.twig', [
            //Here for add your Code //end of your code
            'parametres' => $parametres,
            'form' => $form->createView(),
        ]);
    }

    //Here for add your Code //end of your code
    /* -------------------------------------------------------------------------- */
    /*                                    SHOW                                    */
    /* -------------------------------------------------------------------------- */
    /**
     * @Route("/{id}", name="parametres_show", methods={"GET"})
     */
    public function show(Parametres $parametres): Response
    {
        //Here for add your Code //end of your code
    }

    //Here for add your Code //end of your code
    /* -------------------------------------------------------------------------- */
    /*                                    CLONE                                   */
    /* -------------------------------------------------------------------------- */
    /**
     * @Route("/{id}/clone", name="parametres_clone", methods={"GET","POST"})
     */
    public function clone(Parametres $parametresc, EntityManagerInterface $em): Response
    {
        //Here for add your Code //end of your code
        $parametres = clone $parametresc;
        if (property_exists($parametres, 'slug')) {
            $sfunc = 'set' . ucfirst('');
            $gfunc = 'get' . ucfirst('');
            $parametres->$sfunc($parametres->$gfunc() . uniqid());
            $parametres->setslug();
        }
        $em = $this->getDoctrine()->getManager();
        $parametres->setCreatedAt(new DateTime('now'));
        $em->persist($parametres);
        $em->flush();
        //Here for add your Code //end of your code
        return $this->redirectToRoute('parametres_index');
    }

    //Here for add your Code //end of your code
    /* -------------------------------------------------------------------------- */
    /*                                   DELETE                                   */
    /* -------------------------------------------------------------------------- */
    /**
     * @Route("/{id}", name="parametres_delete", methods={"POST"})
     */
    public function delete(Request $request, Parametres $parametres, EntityManagerInterface $em): Response
    {
        //Here for add your Code //end of your code
        if ($this->isCsrfTokenValid('delete' . $parametres->getId(), $request->request->get('_token'))) {
            //Here for add your Code //end of your code
            if ($request->request->has('delete_delete')) {
                //Here for add your Code //end of your code
                $em->remove($parametres);
            }
            if ($request->request->has('delete_restore')) {
                $parametres->setDeletedAt(null);
            }
            if ($request->request->has('delete_softdelete')) {
                $parametres->setDeletedAt(new DateTime('now'));
            }
            //Here for add your Code //end of your code
            $em->flush();
        }
        //Here for add your Code //end of your code
        if ($request->request->has('delete_softdelete')) {
            return $this->redirectToRoute('parametres_index');
        } else {
            return $this->redirectToRoute('parametres_deleted');
        }
    }
    //Here for add your Code //end of your code
}
//Here for add your Code //end of your code
