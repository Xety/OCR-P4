<?php
/** @var array<\App\Entities\BookEntity> $books */
/** @var string $search */
?>

<div class="books-header">
    <h1 class="books-header__title">Nos livres à l'échange</h1>

    <form class="books-search" action="/books" method="GET" role="search">
        <img src="/images/icons/search.png" alt="" class="books-search__icon" aria-hidden="true" />
        <input
            type="search"
            name="q"
            class="books-search__input"
            placeholder="Rechercher un livre"
            value="<?= e($search) ?>"
            aria-label="Rechercher un livre"
        />
    </form>
</div>

<?php if (empty($books)): ?>
    <p class="books-empty">Aucun livre trouvé<?= $search !== '' ? ' pour « ' . e($search) . ' »' : '' ?>.</p>
<?php else: ?>
    <div class="books-grid">
        <?php foreach ($books as $book): ?>
            <a href="/books/<?= $book->getId() ?>" class="book-card">
                <div class="book-card__cover">
                    <?php if (! empty($book->getPhoto())): ?>
                        <img
                            src="images/books/<?= e($book->getPhoto()) ?>"
                            alt="Couverture de <?= e($book->getTitle()) ?>"
                            class="book-card__img"
                        />
                    <?php else: ?>
                        <img
                            src="https://placehold.co/2731x4096/png"
                            alt="Couverture de <?= e($book->getTitle()) ?>"
                            class="book-card__img"
                        />
                    <?php endif; ?>

                    <?php if (! $book->getIsAvailable()): ?>
                        <span class="book-card__badge badge badge--unavailable">non dispo</span>
                    <?php endif; ?>
                </div>

                <div class="book-card__body">
                    <h2 class="book-card__title"><?= e($book->getTitle()) ?></h2>
                    <p class="book-card__author"><?= e($book->getAuthor()) ?></p>
                    <p class="book-card__seller">Vendu par : <span><?= e($book->getCreator()?->getName()) ?></span></p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

<?php endif; ?>
