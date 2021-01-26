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

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile_index")
     * @param UserRepository $userRepository
     * @return Response
     */
    public function index(UserRepository $userRepository): Response
    {
        $user = $userRepository->findOneBy([
            'email' => $this->getUser()->getUsername()
        ]);

        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/profile/edit", name="profile_edit")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param FileUploader $fileUploader
     * @return Response
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

                $html = $this->renderView('profile/edit.html.twig', [
                    'user' => $user,
                ]);

                return new JsonResponse([
                    'html'    => $html,
                    'message' => 'Success!'
                ], 200);
            }
        }

        $html = $this->renderView('profile/editForm.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);

        return new JsonResponse([
            'html' => $html,
            'message' => 'Success!'
        ], 200);
    }

    /**
     * @Route("/profile/deleteImage", name="profile_deleteImage")
     * @param UserRepository $userRepository
     * @param FileUploader $fileUploader
     * @return Response
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
