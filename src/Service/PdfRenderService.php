<?php

namespace App\Service;

use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Response;

class PdfRenderService
{
    private $pdfBinaryPath;
    private $bootstrapPath;
    private $cssPath;

    public function __construct(
        string $pdfBinaryPath = '/usr/local/bin/wkhtmltopdf',
        string $bootstrapPath = '/app/assets/vendor/bootstrap/dist/css/bootstrap.min.css',
        string $cssPath = '/app/assets/styles/app.css'
    ) {
        $this->pdfBinaryPath = $pdfBinaryPath;
        $this->bootstrapPath = $bootstrapPath;
        $this->cssPath = $cssPath;
    }

    /**
     * Retourne le CSS combiné (bootstrap + custom + perso)
     * @param string|null $customCssPerso CSS personnalisé qui écrase tout si fourni
     * @return string
     */
    public function getCssContent(?string $customCssPerso = null): string
    {
        if ($customCssPerso !== null) {
            return $customCssPerso;
        }
        $bootstrapCss = file_exists($this->bootstrapPath) ? file_get_contents($this->bootstrapPath) : '';
        $customCss = file_exists($this->cssPath) ? file_get_contents($this->cssPath) : '';
        return $bootstrapCss . "\n" . $customCss;
    }

    /**
     * Génère un PDF à partir d'un HTML et retourne la réponse HTTP
     *
     * @param string $html Le contenu HTML à convertir
     * @param string $filename Le nom du fichier PDF à télécharger
     * @param array $options Options supplémentaires pour Snappy
     * @return Response
     */
    public function getPdfResponse(string $html, string $filename, array $options = []): Response
    {
        $snappy = new Pdf($this->pdfBinaryPath);
        // Options par défaut
        $defaultOptions = [
            'disable-smart-shrinking' => true,
            'no-stop-slow-scripts' => true,
            'minimum-font-size' => 8,
            'print-media-type' => true,
            'enable-local-file-access' => true,
        ];
        $snappy->setOptions(array_merge($defaultOptions, $options));
        $pdfContent = $snappy->getOutputFromHtml($html);
        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
        return $response;
    }
}
