<?php

namespace Framework\Actions;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Rajoute des méthodes liées à l'utilisation du routeur
 *
 * Trait RouterAwareAction
 * @package Framework\Actions
 */
trait RouterAwareAction
{
    /**
     * renvoie une réponse de redirection
     *
     * @param  mixed $path
     * @param  mixed $params
     * @return ResponseInterface
     */
    public function redirect(string $path, array $params = []): ResponseInterface
    {
        $redirectUri = $this->router->generateUri($path, $params);
        return new Response(301, [
            'location' => $redirectUri
        ]);
    }
}
