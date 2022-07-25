<?php
//Here for add your Code //end of your code

namespace  App\Controller;
//Here for add your Code //end of your code

use DateTime;
use App\Entity\Compte;
use App\Form\CompteType;
use App\Repository\CompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Service\base\FileUploader;
use Knp\Component\Pager\PaginatorInterface;
//Here for add your Code //end of your code

#[Route('/admin/compte')]
class CompteController extends AbstractController
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
    #[Route('/', name: 'compte_index', methods: ['GET'])]
    public function index(CompteRepository $compteRepository, Request $request, PaginatorInterface $paginator): Response
    {
        //Here for add your Code //end of your code

        $dql   = "SELECT a FROM App:Compte a WHERE a.deletedAt is NULL";
        $query = $this->em->createQuery($dql);
        //Here for add your Code //end of your code

        return $this->render('/compte/index.html.twig', [
            /*¤index_render¤*/
            'pagination' =>$paginator->paginate($query,$request->query->getInt('page', 1),8)
        ]);
        //Here for add your Code //end of your code

    }
    //Here for add your Code //end of your code

    /* -------------------------------------------------------------------------- */
    /*                                   DELETED                                  */
    /* -------------------------------------------------------------------------- */
    #[Route('/deleted', name: 'compte_deleted', methods: ['GET'])]
    public function deleted(CompteRepository $compteRepository, Request $request, PaginatorInterface $paginator): Response
    {
        //Here for add your Code //end of your code

        $dql   = "SELECT a FROM App:Compte a WHERE a.deletedAt is not NULL";
        $query = $this->em->createQuery($dql);
        //Here for add your Code //end of your code

        return $this->render('/compte/index.html.twig', [
            /*¤index_render¤*/
            'pagination' =>$paginator->paginate($query,$request->query->getInt('page', 1),8)
        ]);
    }
    //Here for add your Code //end of your code

    /* -------------------------------------------------------------------------- */
    /*                                    CHAMP                                    */
    /* -------------------------------------------------------------------------- */
    /**
     * @Route("/champ/{id}/{type}/{valeur}", name="compte_champ", methods={"GET"})
     */
    public function champ(Compte $compte, $type = null, $valeur = null): Response
    {
        //Here for add your Code //end of your code

        if ($type) {
            $method = 'set' . $type;
            $compte->$method($valeur);
            $this->em->persist($compte);
            $this->em->flush();
        }
        //Here for add your Code //end of your code

        return $this->redirectToRoute('compte_index', [], Response::HTTP_SEE_OTHER);
    }
    //Here for add your Code //end of your code


    /* -------------------------------------------------------------------------- */
    /*                                NEW AND EDIT                                */
    /* -------------------------------------------------------------------------- */
    #[Route('/new', name: 'compte_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'compte_edit', methods: ['GET', 'POST'])]
    public function new(Request $request, FileUploader $fileUploader, Compte $compte = null, EntityManagerInterface $em): Response
    {
        //Here for add your Code //end of your code

        if (!$compte) $compte = new Compte(); //for new
        //Here for add your Code //end of your code

        $form = $this->createForm(CompteType::class, $compte);
        //Here for add your Code //end of your code

        $form->handleRequest($request);
        //Here for add your Code //end of your code

        if ($form->isSubmitted() && $form->isValid()) {
            //Here for add your Code //end of your code

            if ($request->files->get('compte'))
                foreach ($request->files->get('compte') as $name => $data) {
                    $fichier = $form->get($name)->getData();
                    //Here for add your Code //end of your code

                    if ($fichier) {
                        //Here for add your Code //end of your code

                        if (get_class($fichier) == 'Doctrine\Common\Collections\ArrayCollection' || get_class($fichier) == "Doctrine\ORM\PersistentCollection") {
                            $fichierName = [];
                            foreach ($fichier as $num => $fiche) {
                                if ($data[$num][key($data[$num])] != null) {
                                    $class = explode('\\', get_class($fiche));
                                    $fichierName = $fileUploader->upload($data[$num][key($data[$num])], "compte/$name/" . key($data[$num]) . '/');
                                    $functionE = 'set' . ucfirst(key($data[$num]));
                                    $fiche->$functionE($fichierName);
                                    $function = 'add' . end($class);
                                    $compte->$function($fiche);
                                }
                            }
                        } else {
                           
                                $fichierName = $fileUploader->upload($fichier, "compte/$name/");
                                $function = 'set' . $name;
                                $compte->$function($fichierName);
                        }
                        //Here for add your Code //end of your code

                    }
                    //delete value
                    else
                    {
                         if($request->get("compte_" . $name)=='à retirer')
                                {
                         $function = 'set' . $name;
                         $compte->$function('');
                         }
                    }
                    //Here for add your Code //end of your code

                }
            //Here for add your Code //end of your code

            //TODO: par listener
            if ($compte->getcreatedAt() == 'null') $compte->setCreatedAt(new DateTime('now'));
            $compte->setUpdatedAt(new DateTime('now'));
            $em->persist($compte);
            $em->flush();
            //Here for add your Code //end of your code

            return $this->redirectToRoute('compte_index', [], Response::HTTP_SEE_OTHER);
        }
        //Here for add your Code //end of your code

        return $this->render('/compte/new.html.twig', [
            //Here for add your Code //end of your code

            'compte' => $compte,
            'form' => $form->createView()
        ]);
    }
    //Here for add your Code //end of your code

    /* -------------------------------------------------------------------------- */
    /*                                    SHOW                                    */
    /* -------------------------------------------------------------------------- */
    #[Route('/{id}', name: 'compte_show', methods: ['GET'])]
    public function show(Compte $compte): Response
    {
        //Here for add your Code //end of your code


    }
    //Here for add your Code //end of your code

    /* -------------------------------------------------------------------------- */
    /*                                    CLONE                                   */
    /* -------------------------------------------------------------------------- */
    #[Route('/{id}/clone', name: 'compte_clone', methods: ['GET', 'POST'])]
    public function clone(Compte $comptec, EntityManagerInterface $em): Response
    {
        //Here for add your Code //end of your code

        $compte = clone $comptec;
        if (property_exists($compte, 'slug')) {
            $compte->setslug($comptec->getslug().uniqid());
        }
        $compte->setCreatedAt(new DateTime('now'));
        $em->persist($compte);
        $em->flush();
        //Here for add your Code //end of your code

        return $this->redirectToRoute('compte_index', [], Response::HTTP_SEE_OTHER);
    }
    //Here for add your Code //end of your code

    /* -------------------------------------------------------------------------- */
    /*                                   DELETE                                   */
    /* -------------------------------------------------------------------------- */
    #[Route('/{id}', name: 'compte_delete', methods: ['POST'])]
    public function delete(Request $request, Compte $compte, EntityManagerInterface $em): Response
    {
        //Here for add your Code //end of your code

        if ($this->isCsrfTokenValid('delete' . $compte->getId(), $request->request->get('_token'))) {
            //Here for add your Code //end of your code

            if ($request->request->has('delete_delete')) {
                //Here for add your Code //end of your code

                $em->remove($compte);
            }
            if ($request->request->has('delete_restore'))
                $compte->setDeletedAt(null);
            if ($request->request->has('delete_softdelete'))
                $compte->setDeletedAt(new DateTime('now'));
            //Here for add your Code //end of your code

            $em->flush();
        }
        //Here for add your Code //end of your code

        if ($request->request->has('delete_softdelete'))
            return $this->redirectToRoute('compte_index', [], Response::HTTP_SEE_OTHER);
        else
            return $this->redirectToRoute('compte_deleted', [], Response::HTTP_SEE_OTHER);
    }
    //Here for add your Code //end of your code

}
//Here for add your Code //end of your code

