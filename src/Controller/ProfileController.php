<?php

namespace App\Controller;

use App\Form\ProfileFormType;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("/", name="profile_index")
     */
    public function index(): Response
    {
        return $this->render('profile/index.html.twig');
    }

    /**
     * @Route("/edit", name="profile_edit")
     */
    public function edit(Request $request, UserRepository $userRepository, FileUploader $fileUploader): Response
    {
        $user = $userRepository->findOneBy([
            'email' => $this->getUser()->getUsername()
        ]);

        $form = $this->createForm(ProfileFormType::class);
        $form->handleRequest($request);

        if ($request->isMethod('POST')) {
            if ($form->isValid()) {
                $file = $form['image']->getData();
                $fileName = null;
                $pathFolder = $user->getEmail();

                if ($file) {
                    $fileName = $fileUploader->upload($pathFolder, $file);
                }

                if ($user->getImage() && !$file) {
                    $fileName = $user->getImage();
                } elseif ($user->getImage()) {
                    $fileUploader->delete($pathFolder . '/' . $user->getImage());
                }

                $user->setFirstName($form->get('firstName')->getData());
                $user->setLastName($form->get('lastName')->getData());
                $user->setBirthday($form->get('birthday')->getData());
                $user->setImage($fileName);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                $profileInformation = $this->renderView('profile/profileInformation.html.twig', [
                    'user' => $user,
                ]);

                $editForm = $this->renderView('profile/editForm.html.twig', [
                    'form' => $form->createView(),
                    'user' => $user
                ]);

                return new JsonResponse([
                    'profileInformation' => $profileInformation,
                    'editForm'           => $editForm,
                    'message'            => 'formValid'
                ], 200);
            }
        }

        $profileInformation = $this->renderView('profile/profileInformation.html.twig', [
            'user' => $user
        ]);

        $editForm = $this->renderView('profile/editForm.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);

        return new JsonResponse([
            'profileInformation' => $profileInformation,
            'editForm'           => $editForm,
            'message'            => 'Ok'
        ], 200);
    }

    /**
     * @Route("/deleteImage", name="profile_deleteImage")
     */
    public function deleteImage(UserRepository $userRepository, FileUploader $fileUploader): Response
    {
        $user = $userRepository->findOneBy([
            'email' => $this->getUser()->getUsername()
        ]);

        $fileUploader->delete($user->getEmail() . '/' . $user->getImage());

        $user->setImage(null);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse([
            'message' => 'Success!'
        ], 200);
    }
}
