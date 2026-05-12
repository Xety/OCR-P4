<?php
/** @var \App\Core\Paginator $paginator */
/** @var string $baseUrl URL de base sans paramètre page (ex: '/account') */

if (! $paginator->hasPages()) {
    return;
}
?>
<nav class="pagination">
    <?php if ($paginator->hasPreviousPage()): ?>
        <a href="<?= e($baseUrl) ?>?page=<?= $paginator->getCurrentPage() - 1 ?>" class="pagination__link pagination__link--prev">&laquo; Précédent</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $paginator->getTotalPages(); $i++): ?>
        <a
            href="<?= e($baseUrl) ?>?page=<?= $i ?>"
            class="pagination__link<?= $i === $paginator->getCurrentPage() ? ' pagination__link--active' : '' ?>"
        ><?= $i ?></a>
    <?php endfor; ?>

    <?php if ($paginator->hasNextPage()): ?>
        <a href="<?= e($baseUrl) ?>?page=<?= $paginator->getCurrentPage() + 1 ?>" class="pagination__link pagination__link--next">Suivant &raquo;</a>
    <?php endif; ?>
</nav>