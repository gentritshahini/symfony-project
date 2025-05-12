<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    private $requestStack;

    // Inject the RequestStack service
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    #[Route('/toggle-cookie', name: 'cookie_toggle')]
    public function toggleCookie(): Response
    {
        $cookieValue = $this->requestStack->getCurrentRequest()->cookies->get('admin_key');

        if ($cookieValue) {
            $cookie = new Cookie('admin_key', '', time() - 3600, '/', null, false, true);
            $response = new Response('Admin key cookie removed');
        } else {
            $cookie = new Cookie('admin_key', 'secret-key', time() + 3600, '/', null, false, true);
            $response = new Response('Admin key cookie set');
        }

        $response->headers->setCookie($cookie);

        return $response;
    }
}
