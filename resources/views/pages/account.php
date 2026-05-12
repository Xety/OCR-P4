<?php
/** @var \App\Entities\UserEntity $user */
/** @var array<int, \App\Entities\BookEntity> $books */
/** @var string $memberSince */
/** @var string|null $success */
/** @var string|null $error */
/** @var array<string, string> $old */
/** @var int $page */
/** @var int $totalPages */
/** @var int $totalBooks */
?>

<div class="page-header">
    <h2 class="page-header__title">Mon compte</h2>
</div>

<div class="account-top">

    <!-- Carte profil -->
    <div class="card account-profile">
        <div class="account-avatar">
            <img
                src="/images/icon-user.svg"
                alt="Avatar de <?= e($user->getName()) ?>"
                class="account-avatar__img"
            />
        </div>
        <a href="#" class="account-avatar__edit">modifier</a>

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
    </div>

    <!-- Carte formulaire -->
    <div class="card account-form">
        <h3 class="account-form__title">Vos informations personnelles</h3>

        <?php if (! empty($success)): ?>
            <div class="alert alert--success"><?= e($success) ?></div>
        <?php endif; ?>

        <?php if (! empty($error)): ?>
            <div class="alert alert--error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/account">
            <div class="form-group">
                <label for="email" class="form-label">Adresse email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-input"
                    value="<?= e($old['email'] ?? $user->getEmail()) ?>"
                    required
                />
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Mot de passe</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-input"
                    placeholder="Laisser vide pour ne pas modifier"
                />
            </div>

            <div class="form-group">
                <label for="name" class="form-label">Pseudo</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-input"
                    value="<?= e($old['name'] ?? $user->getName()) ?>"
                    required
                />
            </div>

            <button type="submit" class="btn btn--outline">Enregistrer</button>
        </form>
    </div>

</div>

<!-- Bibliothèque -->
<div class="account-books">
    <table class="table-books">
        <thead>
            <tr>
                <th class="table-books__th">Photo</th>
                <th class="table-books__th">Titre</th>
                <th class="table-books__th">Auteur</th>
                <th class="table-books__th">Description</th>
                <th class="table-books__th">Disponibilité</th>
                <th class="table-books__th">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($books)): ?>
                <tr>
                    <td colspan="6" class="table-books__empty">
                        Vous n'avez pas encore ajouté de livres.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($books as $book): ?>
                    <tr class="table-books__row">
                        <td class="table-books__td">
                            <?php if ($book->getPhoto() !== null): ?>
                                <img
                                    src="<?= e($book->getPhoto()) ?>"
                                    alt="<?= e($book->getTitle()) ?>"
                                    class="table-books__photo"
                                />
                            <?php else: ?>
                                <div class="table-books__photo table-books__photo--placeholder"></div>
                            <?php endif; ?>
                        </td>
                        <td class="table-books__td table-books__title"><?= e($book->getTitle()) ?></td>
                        <td class="table-books__td table-books__author"><?= e($book->getAuthor()) ?></td>
                        <td class="table-books__td table-books__desc">
                            <?= e(mb_strimwidth($book->getDescription(), 0, 90, '…')) ?>
                        </td>
                        <td class="table-books__td table-books__badge">
                            <span class="badge <?= $book->getIsAvailable() ? 'badge--available' : 'badge--unavailable' ?>">
                                <?= $book->getIsAvailable() ? 'disponible' : 'non dispo.' ?>
                            </span>
                        </td>
                        <td class="table-books__td table-books__actions">
                            <a href="/books/<?= $book->getId() ?>/edit" class="table-books__action">Éditer</a>
                            <a href="/books/<?= $book->getId() ?>/delete" class="table-books__action table-books__action--delete">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php if ($totalPages > 1): ?>
    <nav class="pagination">
        <?php if ($page > 1): ?>
            <a href="/account?page=<?= $page - 1 ?>" class="pagination__link">&laquo; Précédent</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a
                href="/account?page=<?= $i ?>"
                class="pagination__link<?= $i === $page ? ' pagination__link--active' : '' ?>"
            ><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="/account?page=<?= $page + 1 ?>" class="pagination__link">Suivant &raquo;</a>
        <?php endif; ?>
    </nav>
<?php endif; ?>