<?php

/** @var string|null $error */
/** @var string $title */
/** @var array<string> $old Valeurs précédentes (name, email) */

$old = $old ?? [];

?>

<div class="auth-split">

    <div class="auth-split__panel">
        <div class="auth-split__form">

            <h1 class="auth-split__title">Inscription</h1>

            <?php if (isset($error)): ?>
                <div class="alert alert--error"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="/register">

                <div class="form-group">
                    <label class="form-label" for="name">Pseudo</label>
                    <input
                        class="form-input"
                        type="text"
                        id="name"
                        name="name"
                        value="<?= e($old['name'] ?? '') ?>"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Adresse email</label>
                    <input
                        class="form-input"
                        type="email"
                        id="email"
                        name="email"
                        value="<?= e($old['email'] ?? '') ?>"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Mot de passe</label>
                    <input
                        class="form-input"
                        type="password"
                        id="password"
                        name="password"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirmer le mot de passe</label>
                    <input
                        class="form-input"
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                    >
                </div>

                <button type="submit" class="btn btn--primary">S'inscrire</button>
            </form>

            <p class="auth-split__footer">
                Déjà inscrit ? <a href="/login" class="auth-split__link">Connectez-vous</a>
            </p>

        </div>
    </div>

    <div class="auth-split__image">
        <img src="/images/login/login-signup_image.png" alt="Bibliothèque de livres">
    </div>

</div>
