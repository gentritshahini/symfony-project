<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AdminKeyMiddlewareListener
{
    public function onKernelController(ControllerEvent $event)
    {
        $request = $event->getRequest();

        // Check if it's an admin route
        if (str_starts_with($request->getPathInfo(), '/admin')) {
            // Retrieve the admin_key cookie
            $adminKey = $request->cookies->get('admin_key');

            // If the admin_key is missing or incorrect, deny access
            if ($adminKey !== 'secret-key') {
                throw new AccessDeniedHttpException('Forbidden');
            }
        }
    }
}
