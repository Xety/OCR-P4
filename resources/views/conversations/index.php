<?php
/** @var \App\Entities\ConversationEntity[] $conversations */
?>

<div class="messaging messaging--list">

    <aside class="messaging__sidebar">
        <h1 class="messaging__title">Messagerie</h1>

        <ul class="messaging__list">
            <?php if ($conversations === []): ?>
                <li class="messaging__empty-list">Aucune conversation pour l'instant.</li>
            <?php endif; ?>

            <?php foreach ($conversations as $conv): ?>
                <li>
                    <a href="/conversations/<?= $conv->getId() ?>" class="conversation-item">
                        <div class="conversation-item__avatar" aria-hidden="true">
                            <?= e(mb_strtoupper(mb_substr($conv->getOtherUser()?->getName(), 0, 1))) ?>
                        </div>
                        <div class="conversation-item__body">
                            <div class="conversation-item__head">
                                <span class="conversation-item__name"><?= e($conv->getOtherUser()?->getName()) ?></span>
                                <?php if ($conv->getLastMessage()?->getCreatedAt() !== null): ?>
                                    <span class="conversation-item__time">
                                        <?php
                                        $now    = new DateTimeImmutable();
                                        $lastAt = $conv->getLastMessage()->getCreatedAt();
                                        echo $lastAt->format('Y-m-d') === $now->format('Y-m-d')
                                            ? e($lastAt->format('H:i'))
                                            : e($lastAt->format('d.m'));
                                        ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <?php if ($conv->getLastMessage() !== null): ?>
                                <p class="conversation-item__preview"><?= e($conv->getLastMessage()->getContent()) ?></p>
                            <?php endif; ?>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <div class="messaging__panel messaging__panel--empty">
        <img src="/images/icon-messagerie.svg" alt="" class="messaging__empty-icon" />
        <p>Sélectionnez une conversation</p>
    </div>

</div>
