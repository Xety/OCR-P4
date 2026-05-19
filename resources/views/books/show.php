<?php
/** @var \App\Entities\BookEntity $book */
/** @var int|null $authId */
?>

<div class="book-breadcrumb">
    <nav class="book-breadcrumb__nav" aria-label="Fil d'Ariane">
        <a href="/books" class="book-breadcrumb__link">Nos livres</a>
        <span class="book-breadcrumb__sep">&rsaquo;</span>
        <span class="book-breadcrumb__current"><?= e($book->getTitle()) ?></span>
    </nav>
</div>

<div class="book-split">

    <div class="book-split__image">
        <?php if (! empty($book->getPhoto())): ?>
            <img
                src="/images/books/<?= e($book->getPhoto()) ?>"
                alt="Couverture de <?= e($book->getTitle()) ?>"
                class="book-split__img"
            />
        <?php else: ?>
            <img
                src="https://placehold.co/900x1200/EDE9E0/9e9a93?text=."
                alt="Couverture de <?= e($book->getTitle()) ?>"
                class="book-split__img"
            />
        <?php endif; ?>
    </div>

    <div class="book-split__content">

        <h1 class="book-split__title"><?= e($book->getTitle()) ?></h1>
        <p class="book-split__author">par <?= e($book->getAuthor()) ?></p>

        <hr class="book-split__sep" />

        <?php if ($book->getDescription() !== ''): ?>
            <p class="book-split__label">Description</p>
            <div class="book-split__description"><?= nl2br(e($book->getDescription())) ?></div>
        <?php endif; ?>

        <p class="book-split__label">Propriétaire</p>
        <div class="book-split__owner">
            <img src="/images/icon-user.svg" alt="" class="book-split__owner-avatar" />
            <a class="book-split__owner-name" href="/users/<?= e($book->getUserId()) ?>"><?= e($book->getCreator()?->getName() ?? '') ?></a>

        </div>

        <?php if ($authId !== null && $authId !== (int) $book->getUserId()): ?>
            <form method="POST" action="/conversations">
                <input type="hidden" name="user_id" value="<?= $book->getUserId() ?>" />
                <button type="submit" class="btn btn--primary book-split__cta">Envoyer un message</button>
            </form>
        <?php elseif ($authId === null): ?>
            <a href="/login" class="btn btn--primary book-split__cta">Envoyer un message</a>
        <?php endif; ?>

    </div>
</div>
