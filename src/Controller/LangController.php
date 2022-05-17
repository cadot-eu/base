<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LangController extends AbstractController
{
    #[Route('/admin/traduction', name: 'traduction_index')]
    public function index(Request $request): Response
    {
        $tab = [];
        if ($request->isMethod('post')) {
            $filesok = [];
            $filenot = [];
            foreach ($request->request->all() as $file => $langetvalues) {
                foreach ($langetvalues['base'] as $num => $champ) {
                    foreach ($langetvalues as $lang => $values) {
                        if ($lang != 'base' && isset($langetvalues[$lang][$num])) {
                            $res = substr($langetvalues[$lang][$num], 0, 2) == '__' ? substr($langetvalues[$lang][$num], 2) : $langetvalues[$lang][$num];
                            $tab[$lang][$champ] = $res;
                        }
                    }
                }

                foreach ($tab as $nomlang => $valeurs) {
                    $retour = file_put_contents('/app/translations/' . "$file.$nomlang" . '.json', json_encode($valeurs));
                    if ($retour) {
                        $filesok[] = $file;
                    } else {
                        $filenot[] = $file;
                    }
                }
            }
            if ($filesok) {
                $this->addFlash('success', 'Fichiers ' . implode(',', $filesok) . ' sauvegardé');
            } else {
                $this->addFlash('error', 'Fichiers ' . implode(',', $filesok) . 'non sauvegardé');
            }
        }

        $path    = '/app/translations/';
        $files = scandir($path);
        $files = array_diff(scandir($path), array('.', '..', '.gitignore'));
        $tab = array();
        foreach ($files as $file) {
            $parts = explode('.', $file);
            $tab[$parts[0]][$parts[1]] = json_decode(file_get_contents($path . $file), true);
            $listChamps = [];
            foreach (json_decode(file_get_contents($path . $file), true) as $champ => $value) {
                $listChamps[] = $champ;
            }
            $tab[$parts[0]]['base'] = $listChamps;
        }
        return $this->render('lang/index.html.twig', [
            'tab' => $tab
        ]);
    }
}
