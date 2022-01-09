<?php

namespace Framework\Twig;

use Twig\TwigFunction;
use Framework\Session\FlashService;
use Twig\Extension\AbstractExtension;

class FlashExtension extends AbstractExtension
{
    /**
     * @var FlashService
     */
    private $flash;

    public function __construct(FlashService $flash)
    {
        $this->flash = $flash;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('flash', [$this, 'getFlash'])
        ];
    }

    public function getFlash($type): ?string
    {
        return $this->flash->get($type);
    }
}
