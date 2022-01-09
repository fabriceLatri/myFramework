<?php

namespace Framework\Session;

use Framework\Session\SessionInterface;

class ArraySession implements SessionInterface
{
    /**
     * @var array
     */
    private $session;

    /**
         * Recupere une information en session
         *
         * @param  string $key
         * @param  mixed $default
         * @return mixed
         */
    public function get(string $key, $default = null)
    {

        if (array_key_exists($key, $this->session)) {
            return $this->session[$key];
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

        $this->session[$key] = $value;
    }
    
    /**
     * Supprime une information en session
     *
     * @param  mixed $key
     * @return void
     */
    public function delete(string $key): void
    {

        unset($this->session[$key]);
    }
}
