<?php
/**
 * @var \App\Entities\ConversationEntity   $conversation  Conversation active (avec otherUser)
 * @var \App\Entities\ConversationEntity[] $conversations Toutes les conversations (sidebar)
 * @var \App\Entities\ConversationMessageEntity[] $messages
 * @var int $authId
 */
?>

<div class="messaging messaging--conversation">

    <?php $activeConversationId = $conversation->getId(); ?>
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <div class="messaging__panel">

        <a href="/conversations" class="messaging__back">← retour</a>

        <div class="messaging__panel-header">
            <div class="conversation-item__avatar" aria-hidden="true">
                <img src="<?= $conversation->getOtherUser()?->getAvatar() !== null ? '/images/avatars/' . e($conversation->getOtherUser()?->getAvatar()) : '/images/icon-user.svg' ?>" alt="" class="conversation-item__avatar-img" />
            </div>
            <span class="messaging__panel-name"><?= e($conversation->getOtherUser()?->getName() ?? '') ?></span>
        </div>

        <div class="messaging__messages" id="messages-container">
            <?php foreach ($messages as $message): ?>
                <?php $isMine = $message->getSenderId() === $authId; ?>
                <div class="message <?= $isMine ? 'message--mine' : 'message--theirs' ?>">
                    <?php if (! $isMine): ?>
                        <div class="message__avatar" aria-hidden="true">
                            <img src="<?= $message->getSender()?->getAvatar() !== null ? '/images/avatars/' . e($message->getSender()?->getAvatar()) : '/images/icon-user.svg' ?>" alt="" class="message__avatar-img" />
                        </div>
                    <?php endif; ?>
                    <div class="message__content">
                        <?php if ($message->getCreatedAt() !== null): ?>
                            <span class="message__time"><?= e($message->getCreatedAt()->format('d.m H:i')) ?></span>
                        <?php endif; ?>
                        <div class="message__bubble"><?= nl2br(e($message->getContent())) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <form class="messaging__form" action="/conversations/<?= $conversation->getId() ?>/messages" method="POST">
            <input
                type="text"
                name="content"
                class="messaging__input"
                placeholder="Tapez votre message ici"
                autocomplete="off"
                required
            />
            <button type="submit" class="btn btn--primary messaging__send">Envoyer</button>
        </form>

    </div>

</div>
