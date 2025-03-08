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
    #[Route('/setup/admin_account', name: 'admin_setup_account')]
    public function setupAdminAccount(
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

            return $this->redirectToRoute('admin_setup');
        }

        return $this->render('admin/setup/adminAccount.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/setup', name: 'admin_setup')]
    public function setupPanel(): Response
    {
        return $this->render('admin/setup/panel.html.twig');
    }

    #[Route('/setup/admin_path', name: 'admin_setup_path')]
    public function setupPath(
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
            $this->addFlash('success', 'Configuration update and cache clear.');

            // Generate the path with the new URL
            $newPrefix = $data['admin_path'];
            $host      = $request->getSchemeAndHttpHost();
            $newUrl    = $host . $newPrefix . '/setup';

            return new RedirectResponse($newUrl);
        }

        return $this->render('admin/setup/setupPath.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/setup/lang', name: 'admin_config_lang')]
    public function setupLang()
    {

    }
}
