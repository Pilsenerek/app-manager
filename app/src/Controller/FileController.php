<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use App\Service\ImageService;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Loggable\Entity\LogEntry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Attribute\Template;

#[Route('/file', name: 'file_')]
class FileController extends AbstractController
{

    #[Route('/', name: 'index')]
    #[Template('file/index.html.twig')]
    public function index(Request $request, ImageRepository $imageRepository, ImageService $imageService)
    {
        $form = $this->createForm(ImageType::class, new Image());
        if ($imageService->upload($form, $request)) {

            return $this->redirectToRoute('file_index');
        }

        return [
            'form' => $form,
            'files' => $imageRepository->findAll()
        ];
    }

    #[Route('/{id}', name: 'detail')]
    #[Template('file/detail.html.twig')]
    public function detail($id, ImageRepository $imageRepository, EntityManagerInterface $em) : array
    {
        $history = $em->getRepository(LogEntry::class)->getLogEntries($imageRepository->find($id));

        return [
            'file' => $imageRepository->find($id),
            'history' => $history,
        ];
    }

    #[Route('src/{id}/{name?}', name: 'src', methods: 'GET')]
    public function src($id, $name, ImageRepository $imageRepository): BinaryFileResponse
    {

        return new BinaryFileResponse($imageRepository->find($id)->getPath());
    }

    #[Route('name/{name}', name: 'name', methods: 'GET')]
    public function name($name): BinaryFileResponse
    {

        return new BinaryFileResponse(Image::UPLOAD_DIR . '/' . $name[0] . '/' . $name);
    }

}
