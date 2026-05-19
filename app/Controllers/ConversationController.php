<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Redirect;
use App\Core\Request;
use App\Entities\ConversationEntity;
use App\Entities\ConversationMessageEntity;
use App\Repositories\ConversationMessageRepository;
use App\Repositories\ConversationRepository;
use App\Validation\Rules\Required;
use App\Validation\Validator;

final class ConversationController extends AbstractController
{
    /**
     * Affiche la liste des conversations de l'utilisateur courant.
     */
    public function index(Request $request): string
    {
        $this->requireAuth();

        $authData = Auth::user();
        $convoRepo = new ConversationRepository($this->db);
        $conversations = $convoRepo->findAllForUser((int) $authData['id']);

        return $this->view->render('conversations/index', [
            'title' => 'Messagerie',
            'mainClass' => 'main--full',
            'conversations' => $conversations,
        ]);
    }

    /**
     * Affiche une conversation et ses messages.
     */
    public function show(Request $request): string
    {
        $this->requireAuth();

        $authData = Auth::user();
        $authId = (int) $authData['id'];
        $convoId = (int) ($request->params['id'] ?? 0);

        $convoRepo = new ConversationRepository($this->db);
        $conversations = $convoRepo->findAllForUser($authId);

        // On obtient la conversation active
        $conversation = null;
        foreach ($conversations as $conv) {
            if ($conv->getId() === $convoId) {
                $conversation = $conv;
                break;
            }
        }

        if (! $conversation instanceof ConversationEntity) {
            Redirect::to('/conversations');
        }

        $messageRepo = new ConversationMessageRepository($this->db);
        $messages    = $messageRepo->findByConversationId($conversation->getId());

        return $this->view->render('conversations/show', [
            'title' => 'Messagerie',
            'mainClass' => 'main--full',
            'conversations' => $conversations,
            'conversation' => $conversation,
            'messages' => $messages,
            'authId' => $authId,
        ]);
    }

    /**
     * Envoie un message dans une conversation.
     */
    public function store(Request $request): string
    {
        $this->requireAuth();

        $authData = Auth::user();
        $authId = (int) $authData['id'];
        $convoId = (int) ($request->params['id'] ?? 0);

        // Vérifier que la conversation existe et que l'utilisateur en fait partie
        $convoRepo = new ConversationRepository($this->db);
        $conversation = $convoRepo->find($convoId);

        if (! $conversation instanceof ConversationEntity) {
            Redirect::to('/conversations');
        }

        // L'utilisateur doit être soit le créateur, soit le destinataire de la conversation
        if ($conversation->getCreatorId() !== $authId && $conversation->getReceiverId() !== $authId) {
            Redirect::to('/conversations');
        }

        $validator = new Validator($request->body, [
            'content' => [new Required()],
        ]);

        if ($validator->fails()) {
            Redirect::to('/conversations/' . $convoId);
        }

        $message = new ConversationMessageEntity([
            'conversationId' => $convoId,
            'senderId' => $authId,
            'content' => trim($request->body['content'] ?? ''),
        ]);

        $messageRepo = new ConversationMessageRepository($this->db);
        $messageRepo->create($message);

        Redirect::to('/conversations/' . $convoId);
    }

    /**
     * Crée ou ouvre une conversation avec un autre utilisateur puis redirige.
     */
    public function create(Request $request): string
    {
        $this->requireAuth();

        $authData = Auth::user();
        $authId = (int) $authData['id'];
        $targetId = (int) ($request->body['user_id'] ?? 0);

        // Impossible de s'envoyer un message à soi-même
        if ($targetId === 0 || $targetId === $authId) {
            Redirect::to('/conversations');
        }

        $convoRepo = new ConversationRepository($this->db);
        $conversation = $convoRepo->findOrCreateBetween($authId, $targetId);

        Redirect::to('/conversations/' . $conversation->getId());
    }
}
