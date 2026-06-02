<?php
/** @var \App\Entities\UserEntity $user */
/** @var array<\App\Entities\BookEntity> $books */
/** @var string $memberSince */
/** @var int|null $authId */
?>

<div class="account-top user-top">

    <div class="card account-profile">
        <div class="account-avatar">
            <img
                src="<?= $user->getAvatar() !== null ? '/images/avatars/' . e($user->getAvatar()) : '/images/icon-user.svg' ?>"
                alt="Avatar de <?= e($user->getName()) ?>"
                class="account-avatar__img"
            />
        </div>

        <hr class="account-profile__sep" />

        <p class="account-profile__name"><?= e($user->getName()) ?></p>
        <p class="account-profile__since">Membre depuis <?= e($memberSince) ?></p>

        <div class="account-profile__library">
            <span class="account-profile__library-label">Bibliothèque</span>
            <span class="account-profile__library-count">
                <img src="/images/account/books.svg" alt="Icon Books" class="account-profile__book-icon" />
                <?= count($books) ?> livre<?= count($books) > 1 ? 's' : '' ?>
            </span>
        </div>

        <?php if ($authId !== null && $authId !== $user->getId()): ?>
            <form method="POST" action="/conversations">
                <input type="hidden" name="user_id" value="<?= $user->getId() ?>" />
                <button type="submit" class="btn btn--outline user-profile__cta">Écrire un message</button>
            </form>
        <?php elseif ($authId === null): ?>
            <a href="/login" class="btn btn--outline user-profile__cta">Écrire un message</a>
        <?php endif; ?>
    </div>

    <div class="account-books account-books--full">
        <table class="table-books">
            <thead>
                <tr>
                    <th class="table-books__th">Photo</th>
                    <th class="table-books__th">Titre</th>
                    <th class="table-books__th">Auteur</th>
                    <th class="table-books__th">Description</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($books)): ?>
                    <tr>
                        <td colspan="4" class="table-books__empty">
                            Cet utilisateur n'a pas encore ajouté de livres.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($books as $book): ?>
                        <tr class="table-books__row">
                            <td class="table-books__td">
                                <?php if ($book->getPhoto() !== null): ?>
                                    <img
                                        src="/images/books/<?= e($book->getPhoto()) ?>"
                                        alt="<?= e($book->getTitle()) ?>"
                                        class="table-books__photo"
                                    />
                                <?php else: ?>
                                    <img
                                        src="https://placehold.co/55x70/EDE9E0/9e9a93?text=."
                                        alt="Couverture de <?= e($book->getTitle()) ?>"
                                        class="table-books__photo"
                                    />
                                <?php endif; ?>
                            </td>
                            <td class="table-books__td table-books__title"><?= e($book->getTitle()) ?></td>
                            <td class="table-books__td table-books__author"><?= e($book->getAuthor()) ?></td>
                            <td class="table-books__td table-books__desc">
                                <?= e(mb_strimwidth($book->getDescription(), 0, 90, '…')) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>