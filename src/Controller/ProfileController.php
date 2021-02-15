<?php

namespace App\Controller;

use App\Form\ProfileFormType;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController
{
    /**
     * @Route("", name="profile_index")
     */
    public function index(): Response
    {
        return $this->render('profile/index.html.twig');
    }

    /**
     * @Route("/profileInformation", name="profile_information")
     */
    public function profileInformation(): Response
    {
        $profileInformation = $this->renderView('profile/profileInformation.html.twig', [
            'user' => $this->getUser()
        ]);

        return new JsonResponse([
            'profileInformation' => $profileInformation,
        ]);
    }

    /**
     * @Route("/edit", name="profile_edit")
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(ProfileFormType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['image']->getData();
            $pathFolder = $this->getUser()->getEmail();

            if ($this->getUser()->getImage() && $file) {
                $fileUploader->delete("{$pathFolder}/{$this->getUser()->getImage()}");
            }

            if ($file) {
                $fileName = $fileUploader->upload($pathFolder, $file);
                $this->getUser()->setImage($fileName);
            }

            $entityManager->flush();

            return new JsonResponse();
        }

        $editForm = $this->renderView('profile/editForm.html.twig', [
            'form' => $form->createView(),
            'user' => $this->getUser()
        ]);

        return new JsonResponse([
            'editForm' => $editForm
        ]);
    }

    /**
     * @Route("/deleteImage", name="profile_deleteImage")
     */
    public function deleteImage(EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $pathToImage = "{$this->getUser()->getEmail()}/{$this->getUser()->getImage()}";
        $fileUploader->delete($pathToImage);

        $this->getUser()->setImage(null);
        $entityManager->flush();

        return new JsonResponse();
    }
}
