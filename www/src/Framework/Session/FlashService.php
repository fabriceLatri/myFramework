<?php

namespace Framework\Session;

class FlashService
{

    /**
     * @var SessionInterface
     */
    private $session;

    private $sessionKey = 'flash';

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function success(string $message)
    {
        $flash = $this->session->get($this->sessionKey, []);
        $flash['success'] = $message;
        $this->session->set($this->sessionKey, $flash);
    }

    public function get(string $type): ?string
    {
        $flash = $this->session->get($this->sessionKey, []);
        if (array_key_exists($type, $flash)) {
            return $flash[$type];
        }
        return null;
    }
}
