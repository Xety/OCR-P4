<?php
/**
 * @var \App\Entities\ConversationEntity   $conversation  Conversation active (avec otherUser)
 * @var \App\Entities\ConversationEntity[] $conversations Toutes les conversations (sidebar)
 * @var \App\Entities\ConversationMessageEntity[] $messages
 * @var int $authId
 */
?>

<div class="messaging messaging--conversation">

    <aside class="messaging__sidebar">
        <h1 class="messaging__title">Messagerie</h1>

        <ul class="messaging__list">
            <?php foreach ($conversations as $conv): ?>
                <?php $isActive = $conv->getId() === $conversation->getId(); ?>
                <li>
                    <a href="/conversations/<?= $conv->getId() ?>" class="conversation-item<?= $isActive ? ' conversation-item--active' : '' ?>">
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

    <div class="messaging__panel">

        <a href="/messages" class="messaging__back">← retour</a>

        <div class="messaging__panel-header">
            <div class="conversation-item__avatar" aria-hidden="true">
                <?= e(mb_strtoupper(mb_substr($conversation->getOtherUser()?->getName() ?? '?', 0, 1))) ?>
            </div>
            <span class="messaging__panel-name"><?= e($conversation->getOtherUser()?->getName() ?? '') ?></span>
        </div>

        <div class="messaging__messages" id="messages-container">
            <?php foreach ($messages as $message): ?>
                <?php $isMine = $message->getSenderId() === $authId; ?>
                <div class="message <?= $isMine ? 'message--mine' : 'message--theirs' ?>">
                    <?php if (! $isMine): ?>
                        <div class="message__avatar" aria-hidden="true">
                            <?= e(mb_strtoupper(mb_substr($message->getSender()?->getName() ?? '', 0, 1))) ?>
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
