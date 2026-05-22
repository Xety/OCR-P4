<?php
/**
 * @var \App\Entities\ConversationEntity[] $conversations
 * @var int|null $activeConversationId  Optionnel — null sur la page liste
 */
$activeConversationId ??= null;
?>
<aside class="messaging__sidebar">
    <h1 class="messaging__title">Messagerie</h1>

    <ul class="messaging__list">
        <?php if ($conversations === []): ?>
            <li class="messaging__empty-list">Aucune conversation pour l'instant.</li>
        <?php endif; ?>

        <?php foreach ($conversations as $conv): ?>
            <li>
                <a href="/conversations/<?= $conv->getId() ?>" class="conversation-item<?= $conv->getId() === $activeConversationId ? ' conversation-item--active' : '' ?>">
                    <div class="conversation-item__avatar" aria-hidden="true">
                        <img src="<?= $conv->getOtherUser()?->getAvatar() !== null ? '/images/avatars/' . e($conv->getOtherUser()?->getAvatar()) : '/images/icon-user.svg' ?>" alt="" class="conversation-item__avatar-img" />
                    </div>
                    <div class="conversation-item__body">
                        <div class="conversation-item__head">
                            <span class="conversation-item__name"><?= e($conv->getOtherUser()->getName()) ?></span>
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