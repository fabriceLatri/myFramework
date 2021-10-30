<?php

namespace Framework\Session;

interface SessionInterface
{
    /**
     * Recupere une information en session
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function get(string $key, $default);
    
    /**
     * Ajoute une information en session
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function set(string $key, $value): void;
    
    /**
     * Supprime une information en session
     *
     * @param  mixed $key
     * @return void
     */
    public function delete(string $key): void;
}
