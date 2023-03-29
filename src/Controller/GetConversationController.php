<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Conversation;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class GetConversationController extends AbstractController
{
    public function __invoke(Conversation $conversation): Conversation
    {
        /** @var User $user */
        $user = $this->getUser();
        if (in_array($conversation, $user->getOwnedConversation()->getValues()) || in_array($conversation, $user->getGuestConversation()->getValues())){
            return $conversation;
        } else {
            throw new AccessDeniedHttpException();
        }
    }
}