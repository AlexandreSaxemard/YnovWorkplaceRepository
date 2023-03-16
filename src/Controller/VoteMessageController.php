<?php

namespace App\Controller;

use App\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VoteMessageController extends AbstractController
{
    public function __construct() {}


    public function __invoke(Message $message) : Message
    {
        if ($message->getOwner() === $this->getUser()) {
            throw new \Exception('You cannot vote for your own message');
        }
        $message->setNumberVotes($message->getNumberVotes() + 1);
        return $message;
    }
}

