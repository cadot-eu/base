<?php
//Here for add your Code //end of your code

namespace  App\Controller;
//Here for add your Code //end of your code

use DateTime;
use App\Entity\Parametres;
use App\Form\ParametresType;
use App\Repository\ParametresRepository;
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

#[Route('/admin/parametres')]
class ParametresController extends AbstractController
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
    #[Route('/', name: 'parametres_index', methods: ['GET'])]
    public function index(ParametresRepository $parametresRepository, Request $request, PaginatorInterface $paginator): Response
    {
        //Here for add your Code //end of your code

        $dql = $parametresRepository->index($request->query->get('filterValue', ''),null, $request->query->get('sort'), $request->query->get('direction'),false);
        //Here for add your Code //end of your code

        return $this->render('/parametres/index.html.twig', [
            /*¤index_render¤*/
            'pagination' =>$paginator->paginate($dql, $request->query->getInt('page', 1))
        ]);
        //Here for add your Code //end of your code

    }
    //Here for add your Code //end of your code

    /* -------------------------------------------------------------------------- */
    /*                                   DELETED                                  */
    /* -------------------------------------------------------------------------- */
    #[Route('/deleted', name: 'parametres_deleted', methods: ['GET'])]
    public function deleted(ParametresRepository $parametresRepository, Request $request, PaginatorInterface $paginator): Response
    {
        //Here for add your Code //end of your code

         $dql = $parametresRepository->index($request->query->get('filterValue', ''),null, $request->query->get('sort', 'a.id'), $request->query->get('direction'),true);
      
        //Here for add your Code //end of your code

        return $this->render('/parametres/index.html.twig', [
            /*¤index_render¤*/
            'pagination' =>$paginator->paginate($dql,$request->query->getInt('page', 1),8)
        ]);
    }
    //Here for add your Code //end of your code

    /* -------------------------------------------------------------------------- */
    /*                                    CHAMP                                    */
    /* -------------------------------------------------------------------------- */
    /**
     * @Route("/champ/{id}/{type}/{valeur}", name="parametres_champ", methods={"GET"})
     */
    public function champ(Parametres $parametres, $type = null, $valeur = null): Response
    {
        //Here for add your Code //end of your code

        if ($type) {
            $method = 'set' . $type;
            $parametres->$method($valeur);
            $this->em->persist($parametres);
            $this->em->flush();
        }
        //Here for add your Code //end of your code

        return $this->redirectToRoute('parametres_index', [], Response::HTTP_SEE_OTHER);
    }
    //Here for add your Code //end of your code


    /* -------------------------------------------------------------------------- */
    /*                                NEW AND EDIT                                */
    /* -------------------------------------------------------------------------- */
    #[Route('/new', name: 'parametres_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'parametres_edit', methods: ['GET', 'POST'])]
    public function new(Request $request, FileUploader $fileUploader, Parametres $parametres = null, EntityManagerInterface $em): Response
    {
        //Here for add your Code //end of your code

        if (!$parametres) $parametres = new Parametres(); //for new
        //Here for add your Code //end of your code

        $form = $this->createForm(ParametresType::class, $parametres,['username'=>$this->getUser()->getEmail(),]);
        //Here for add your Code //end of your code

        $form->handleRequest($request);
        //Here for add your Code //end of your code

        if ($form->isSubmitted() && $form->isValid()) {
            //Here for add your Code //end of your code

            if ($request->files->get('parametres'))
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
                    //delete value
                    else
                    {
                         if($request->get("parametres_" . $name)=='à retirer')
                                {
                         $function = 'set' . $name;
                         $parametres->$function('');
                         }
                    }
                    //Here for add your Code //end of your code

                }
            //Here for add your Code //end of your code

            //TODO: par listener
            
            $em->persist($parametres);
            $em->flush();
            //Here for add your Code //end of your code

            return $this->redirectToRoute('parametres_index', [], Response::HTTP_SEE_OTHER);
        }
        //Here for add your Code //end of your code

        return $this->render('/parametres/new.html.twig', [
            //Here for add your Code //end of your code

            'parametres' => $parametres,
            'form' => $form->createView()
        ]);
    }
    //Here for add your Code //end of your code

    /* -------------------------------------------------------------------------- */
    /*                                    SHOW                                    */
    /* -------------------------------------------------------------------------- */
    #[Route('/{id}', name: 'parametres_show', methods: ['GET'])]
    public function show(Parametres $parametres): Response
    {
        //Here for add your Code //end of your code


    }
    //Here for add your Code //end of your code

    /* -------------------------------------------------------------------------- */
    /*                                    CLONE                                   */
    /* -------------------------------------------------------------------------- */
    #[Route('/{id}/clone', name: 'parametres_clone', methods: ['GET', 'POST'])]
    public function clone(Parametres $parametresc, EntityManagerInterface $em): Response
    {
        //Here for add your Code //end of your code

        $parametres = clone $parametresc;
        if (property_exists($parametres, 'slug')) {
            $parametres->setslug($parametresc->getslug().uniqid());
        }
        $parametres->setCreatedAt(new DateTime('now'));
        $em->persist($parametres);
        $em->flush();
        //Here for add your Code //end of your code

        return $this->redirectToRoute('parametres_index', [], Response::HTTP_SEE_OTHER);
    }
    //Here for add your Code //end of your code

    /* -------------------------------------------------------------------------- */
    /*                                   DELETE                                   */
    /* -------------------------------------------------------------------------- */
    #[Route('/{id}', name: 'parametres_delete', methods: ['POST'])]
    public function delete(Request $request, Parametres $parametres, EntityManagerInterface $em): Response
    {
        //Here for add your Code //end of your code

        if ($this->isCsrfTokenValid('delete' . $parametres->getId(), $request->request->get('_token')) ) {
            //Here for add your Code //end of your code

            if ($request->request->has('delete_delete')) {
                //Here for add your Code //end of your code

                $em->remove($parametres);
            }
            if ($request->request->has('delete_restore'))
                $parametres->setDeletedAt(null);
            if ($request->request->has('delete_softdelete'))
                $parametres->setDeletedAt(new DateTime('now'));
            //Here for add your Code //end of your code

            $em->flush();
        }
        //Here for add your Code //end of your code

        if ($request->request->has('delete_softdelete'))
            return $this->redirectToRoute('parametres_index', [], Response::HTTP_SEE_OTHER);
        else
            return $this->redirectToRoute('parametres_deleted', [], Response::HTTP_SEE_OTHER);
    }
    //Here for add your Code //end of your code

}
//Here for add your Code //end of your code

