<?php

namespace Samples\Chat\Client;

interface ClientInterface
{
    /**
     * @param string $username
     * @param string $password
     * @return mixed
     */
    public function login(string $username, string $password);

    /**
     * Start a conversation with a list of participants
     * @param array $participantIds
     * @return mixed
     */
    public function startConversation(array $participantIds);

    /**
     * @param string $content
     * @return mixed
     */
    public function sendMessage(string $content);

    /**
     * Add a new participant to the conversation
     * @param int $userId
     * @return mixed
     */
    public function addParticipant(int $userId);

    /**
     * Poll for new messages
     * @return mixed
     */
    public function poll();

    /**
     * Display the chat messages
     * @return mixed
     */
    public function display();
}