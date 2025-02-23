<?php

namespace App\Controller\Admin\Setup;

use App\Entity\User;
use App\Form\Admin\AdminConfigType;
use App\Form\Admin\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Yaml\Yaml;

final class SetupController extends AbstractController
{
    #[Route('/setup', name: 'admin_setup')]
    public function index(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface      $entityManager
    ): Response {
        $existingAdmin = $entityManager->getRepository(User::class)->findOneByRole('ROLE_ADMIN');
        if ($existingAdmin) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_ADMIN']);

            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('admin_config');
        }

        return $this->render('admin/setup/setup/index.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/config', name: 'admin_config')]
    public function config(
        Request               $request,
        CacheClearerInterface $cacheClearer
    ): Response | RedirectResponse
    {
        // Get admin config path
        $configFile = $this->getParameter('kernel.project_dir').'/config/admin_config.yaml';
        $configData = [];
        if (file_exists($configFile)) {
            $configData = Yaml::parseFile($configFile);
        }
        $currentParameters = $configData['parameters'] ?? ['admin_path' => '/admin'];

        // generate form
        $form = $this->createForm(AdminConfigType::class, $currentParameters);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // update the path with new value
            $yamlContent = Yaml::dump(['parameters' => $data]);
            file_put_contents($configFile, $yamlContent);

            $cacheClearer->clear($this->getParameter('kernel.cache_dir'));
            $this->addFlash('success', 'Configuration mise à jour et cache vidé.');

            // Generate the path with the new URL
            $newPrefix = $data['admin_path'];
            $host      = $request->getSchemeAndHttpHost();
            $newUrl    = $host . $newPrefix . '/config';

            return new RedirectResponse($newUrl);
        }

        return $this->render('admin/config.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
