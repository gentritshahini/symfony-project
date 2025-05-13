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
            $response = $this->redirectToRoute('homepage');
        } else {
            $cookie = new Cookie('admin_key', 'secret-key', time() + 3600, '/', null, false, true);
            $response = $this->redirectToRoute('admin_news_index');
        }

        $response->headers->setCookie($cookie);

        return $response;
    }
}
