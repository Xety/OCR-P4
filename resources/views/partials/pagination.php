<?php
/** @var \App\Core\Paginator $paginator */
/** @var string $baseUrl URL de base, avec ou sans paramètre existant (ex: '/account', '/books?q=roman') */

if (! $paginator->hasPages()) {
    return;
}

$sep = str_contains($baseUrl, '?') ? '&' : '?';
?>
<nav class="pagination">
    <?php if ($paginator->hasPreviousPage()): ?>
        <a href="<?= e($baseUrl . $sep . 'page=' . ($paginator->getCurrentPage() - 1)) ?>" class="pagination__link pagination__link--prev">&laquo; Précédent</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $paginator->getTotalPages(); $i++): ?>
        <a
            href="<?= e($baseUrl . $sep . 'page=' . $i) ?>"
            class="pagination__link<?= $i === $paginator->getCurrentPage() ? ' pagination__link--active' : '' ?>"
        ><?= $i ?></a>
    <?php endfor; ?>

    <?php if ($paginator->hasNextPage()): ?>
        <a href="<?= e($baseUrl . $sep . 'page=' . ($paginator->getCurrentPage() + 1)) ?>" class="pagination__link pagination__link--next">Suivant &raquo;</a>
    <?php endif; ?>
</nav>