<?php
use App\Core\Auth;
?>
<header class="header">
    <a href="/" class="header__brand">
        <img src="/images/logo.svg" alt="Logo de TomTroc" class="header__logo" />
    </a>

    <nav class="header__nav" id="header-nav">
        <a href="/" class="header__nav-link">Accueil</a>
        <a href="/users" class="header__nav-link">Nos livres à l'échange</a>
    </nav>

    <div class="header__actions" id="header-actions">
        <a href="#" class="header__action">
            <img src="/images/icon-messagerie.svg" alt="IconMessagerie" />
            Messagerie
            <span class="header__badge">1</span>
        </a>
        <a href="#" class="header__action">
            <img src="/images/icon-user.svg" alt="Icon User" />
            Mon compte
        </a>
        <?php if (Auth::isAuthenticated()): ?>
            <a href="/logout" class="header__action">Déconnexion</a>
        <?php else: ?>
        <a href="/login" class="header__action">Connexion</a>
        <?php endif; ?>
    </div>

    <button class="header__burger" id="header-burger" aria-label="Ouvrir le menu" aria-expanded="false">
        <span></span>
        <span></span>
        <span></span>
    </button>
</header>