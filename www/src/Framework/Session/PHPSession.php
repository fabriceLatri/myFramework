<?php

namespace Framework\Session;

class PHPSession
{
    /**
     * Assure que la session soit démarrée
     * @return void
     */
    private function ensureStatrted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
         * Recupere une information en session
         *
         * @param  string $key
         * @param  mixed $default
         * @return mixed
         */
    public function get(string $key, $default = null)
    {
        $this->ensureStatrted();

        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }

        return $default;
    }
    /**
     * Ajoute une information en session
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function set(string $key, $value): void
    {
        $this->ensureStatrted();

        $_SESSION[$key] = $value;
    }
    
    /**
     * Supprime une information en session
     *
     * @param  mixed $key
     * @return void
     */
    public function delete(string $key): void
    {
        $this->ensureStatrted();

        unset($_SESSION[$key]);
    }
}
