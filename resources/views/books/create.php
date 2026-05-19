<?php
/** @var array<string, string> $old */
/** @var string|null $error */
?>

<div class="page-header">
    <a href="/account" class="page-header__back">← retour</a>
    <h1 class="page-header__title">Ajouter un livre</h1>
</div>

<?php if (! empty($error)): ?>
    <div class="alert alert--error"><?= e($error) ?></div>
<?php endif; ?>

<form
    method="POST"
    action="/books"
    enctype="multipart/form-data"
    class="book-edit card"
>
    <!-- Colonne photo -->
    <div class="book-edit__photo-col">
        <p class="book-edit__col-label">Photo</p>

        <div class="book-edit__cover">
            <img
                src="https://placehold.co/400x520/EDE9E0/EDE9E0/png"
                alt="Couverture du livre"
                class="book-edit__img"
                id="book-edit-preview"
            />
        </div>

        <label for="photo" class="book-edit__photo-link">Ajouter une photo</label>
        <input
            type="file"
            id="photo"
            name="photo"
            accept="image/jpeg,image/png,image/webp"
            class="book-edit__file-input"
        />
    </div>

    <!-- Colonne formulaire -->
    <div class="book-edit__form-col">
        <div class="form-group">
            <label for="title" class="form-label">Titre</label>
            <input
                type="text"
                id="title"
                name="title"
                class="form-input"
                value="<?= e($old['title'] ?? '') ?>"
                required
            />
        </div>

        <div class="form-group">
            <label for="author" class="form-label">Auteur</label>
            <input
                type="text"
                id="author"
                name="author"
                class="form-input"
                value="<?= e($old['author'] ?? '') ?>"
                required
            />
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Commentaire</label>
            <textarea
                id="description"
                name="description"
                class="form-input book-edit__textarea"
                rows="10"
            ><?= e($old['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="is_available" class="form-label">Disponibilité</label>
            <select id="is_available" name="is_available" class="form-input book-edit__select">
                <option value="1" <?= ($old['is_available'] ?? '1') === '1' ? 'selected' : '' ?>>disponible</option>
                <option value="0" <?= ($old['is_available'] ?? '1') === '0' ? 'selected' : '' ?>>non disponible</option>
            </select>
        </div>

        <button type="submit" class="btn btn--primary book-edit__submit">Ajouter</button>
    </div>
</form>
