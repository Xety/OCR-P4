<?php

declare(strict_types=1);

namespace App\Core;

use App\Contracts\RendererInterface;
use RuntimeException;

/**
 * Moteur de rendu de vues PHP avec layout principal.
 */
final class View implements RendererInterface
{
    private string $viewsPath;

    public function __construct(private readonly string $basePath)
    {
        $this->viewsPath = $this->basePath . '/resources/views';
    }

    /**
     * Rend une vue dans le layout principal et retourne le HTML.
     *
     * @param  string $view Chemin relatif de la vue (ex: 'users/index')
     * @param  array $data Variables transmises à la vue
     *
     * @return string Contenu HTML rendu de la vue
     *
     * @throws RuntimeException si le fichier de vue est introuvable
     */
    public function render(string $view, array $data = []): string
    {
        $viewFile = $this->viewsPath . '/' . str_replace('.', '/', $view) . '.php';

        if (! file_exists($viewFile)) {
            throw new RuntimeException("Vue introuvable : {$viewFile}");
        }

        // Rend le contenu de la vue
        $content = $this->renderTemplate($viewFile, $data);

        // Injecte le contenu dans le layout principal
        $layoutFile = $this->viewsPath . '/layout.php';

        return $this->renderTemplate($layoutFile, array_merge($data, ['content' => $content]));
    }

    /**
     * Capture la sortie d'un template PHP en injectant les variables.
     *
     * @param string $file Chemin du fichier demandé par le controller
     * @param array $data Les variables à extraire que le controller à envoyé à la vue
     *
     * @throws RuntimeException Si le fichier de template est introuvable
     *
     * @return string Contenu rendu du template
     */
    private function renderTemplate(string $file, array $data = []): string
    {
        if(file_exists($file)) {
            extract($data); // On transforme les diverses variables stockées dans le tableau "params" en véritables variables qui pourront être lues dans le template.

            ob_start();
            require $file;

            return ob_get_clean() ?: '';
        } else {
            throw new RuntimeException("Fichier de template introuvable : {$file}");
        }
    }
}
