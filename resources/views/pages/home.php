<section class="hero--wrap">
    <div class="hero">
        <div class="hero__content">
            <h1 class="hero__title">Rejoignez nos lecteurs passionnés</h1>
            <p class="hero__text">
                Donnez une nouvelle vie à vos livres en les échangeant avec d'autres amoureux de la
                lecture. Nous croyons en la magie du partage de connaissances et d'histoires à
                travers les livres.
            </p>
            <a href="/books" class="btn btn--primary hero__cta">Découvrir</a>
        </div>

        <div class="hero__image-wrap">
            <img
                src="/images/home/image-home.jpg"
                alt="Vendeur de livres"
                class="hero__img"
            />
            <span class="hero__caption">Hamza</span>
        </div>
    </div>
</section>

<section class="home-latest">
    <div class="home-latest__inner">
        <h2 class="home-latest__title">Les derniers livres ajoutés</h2>

        <div class="books-grid">
            <?php foreach ($latestBooks as $book): ?>
                <a href="/books/<?= $book->getId() ?>" class="book-card">
                    <div class="book-card__cover">
                        <?php if (! empty($book->getPhoto())): ?>
                            <img
                                src="/images/books/<?= e($book->getPhoto()) ?>"
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
                    </div>

                    <div class="book-card__body">
                        <h3 class="book-card__title"><?= e($book->getTitle()) ?></h3>
                        <p class="book-card__author"><?= e($book->getAuthor()) ?></p>
                        <p class="book-card__seller">Vendu par : <span><?= e($book->getUserName()) ?></span></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="home-latest__footer">
            <a href="/books" class="btn btn--primary">Voir tous les livres</a>
        </div>
    </div>
</section>

<section class="home-how">
    <div class="home-how__inner">
        <h2 class="home-how__title">Comment ça marche ?</h2>
        <p class="home-how__subtitle">
            Échanger des livres avec TomTroc c'est simple et amusant !
            Suivez ces étapes pour commencer :
        </p>

        <div class="home-how__grid">
            <div class="home-how__card">
                Inscrivez-vous gratuitement sur notre plateforme.
            </div>
            <div class="home-how__card">
                Ajoutez les livres que vous souhaitez échanger à votre profil.
            </div>
            <div class="home-how__card">
                Parcourez les livres disponibles chez d'autres membres.
            </div>
            <div class="home-how__card">
                Proposez un échange et discutez avec d'autres passionnés de lecture.
            </div>
        </div>

        <div class="home-how__footer">
            <a href="/books" class="btn btn--outline">Voir tous les livres</a>
        </div>
    </div>
</section>

<section class="home-values">
    <div class="home-values__banner">
    <picture>
        <source media="(max-width: 768px)" srcset="/images/home/image-valeurs-mobile.png" />
        <img src="/images/home/image-valeurs.png" alt="Une librairie" class="home-values__banner-img" />
    </picture>
</div>

    <div class="home-values__body">
        <div class="home-values__text">
            <h2 class="home-values__title">Nos valeurs</h2>
            <p class="home-values__para">
                Chez Tom Troc, nous mettons l'accent sur le partage, la découverte et la
                communauté. Nos valeurs sont ancrées dans notre passion pour les livres et notre
                désir de créer des liens entre les lecteurs. Nous croyons en la puissance des
                histoires pour rassembler les gens et inspirer des conversations enrichissantes.
            </p>
            <p class="home-values__para">
                Notre association a été fondée avec une conviction profonde : chaque livre mérite
                d'être lu et partagé.
            </p>
            <p class="home-values__para">
                Nous sommes passionnés par la création d'une plateforme conviviale qui permet aux
                lecteurs de se connecter, de partager leurs découvertes littéraires et d'échanger
                des livres qui attendent patiemment sur les étagères.
            </p>
            <div class="home-values__footer">
                <p class="home-values__signature">L'équipe Tom Troc</p>
                <div class="home-values__deco">
                    <img src="/images/home/coeur.svg" alt="" class="home-values__heart" aria-hidden="true" />
                </div>
            </div>
        </div>
    </div>
</section>