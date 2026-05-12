<?php
/** @var string $uri L'URI demandée qui n'a pas été trouvée. */
?>

<div class="error-page">
    <div class="error-page__code">404</div>
    <h1 class="error-page__title">Page introuvable</h1>
    <p class="error-page__message">
        La page <code class="error-page__uri"><?= e($uri) ?></code> n'existe pas ou a été déplacée.
    </p>
    <a href="/" class="btn btn--primary">Retour à l'accueil</a>
</div>
