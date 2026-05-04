<?php

/** @var string|null $error */
/** @var string      $title */

?>

<div class="auth-split">

    <div class="auth-split__panel">
        <div class="auth-split__form">

            <h1 class="auth-split__title">Connexion</h1>

            <?php if (isset($error)): ?>
                <div class="alert alert--error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="/login">
                <input type="hidden" name="_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                <div class="form-group">
                    <label class="form-label" for="email">Adresse email</label>
                    <input
                        class="form-input"
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        required
                        autofocus
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

                <button type="submit" class="btn btn--primary">Se connecter</button>
            </form>

            <p class="auth-split__footer">
                Pas de compte ? <a href="/register" class="auth-split__link">Inscrivez-vous</a>
            </p>

        </div>
    </div>

    <div class="auth-split__image">
        <img src="/images/login/login-signup_image.png" alt="Bibliothèque de livres">
    </div>

</div>
