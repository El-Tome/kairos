<?php

namespace App\Services\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;

final class AdminSetupListener
{
    private EntityManagerInterface $em;
    private RouterInterface        $router;

    public function __construct(EntityManagerInterface $em, RouterInterface $router)
    {
        $this->em     = $em;
        $this->router = $router;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        // Only process the main request, ignore sub-requests
        if (!$event->isMainRequest()) {
            return;
        }

        // Only handle HTML requests, ignore other formats (like JSON, XML)
        $request = $event->getRequest();
        if ('html' !== $request->getRequestFormat()) {
            return;
        }

        // Skip processing for setup page and dev tools routes
        if (
            in_array(
                $request->attributes->get('_route'),
                ['admin_setup_account' , '_wdt', '_profiler', '_error']
            )
        ) {
            return;
        }

        // Check if an admin user exists in the database
        $admin = $this->em->getRepository(User::class)->findOneByRole('ROLE_ADMIN');
        if (!$admin) {
            // If no admin exists, redirect to the setup page
            $response = new RedirectResponse($this->router->generate('admin_setup_account'));
            $event->setResponse($response);
        }
    }
}
