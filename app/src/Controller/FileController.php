<?php

namespace App\Controller;

use App\Domain\UserRepository;
use App\Entity\Image;
use App\Form\ImageConvertType;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Twig\Attribute\Template;

#[Route('/file', name: 'file_')]
class FileController extends AbstractController
{

    #[Route('/', name: 'index')]
    #[Template('file/index.html.twig')]
    public function index(Request $request, ImageRepository $imageRepository)
    {
        $imageEntity = new Image();
        $form = $this->createForm(ImageType::class, $imageEntity);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $image */
            $image = $form->get('filename')->getData();
            if ($image) {
                $newFilename = md5(uniqid('', true));
                $imageEntity->setLabel($image->getClientOriginalName());
                $imageEntity->setFilename($newFilename);
                $imageEntity->setExtension($image->guessExtension());
                $imageEntity->setMime($image->getMimeType());
                $image->move(
                    Image::UPLOAD_DIR . '/' . $newFilename[0],
                    $newFilename
                );
                $imageRepository->save($imageEntity);
            }

            return $this->redirectToRoute('file_index');
        }

        return [
            'form' => $form,
            'files' => $imageRepository->findAll()
        ];
    }

    #[Route('/{id}', name: 'detail')]
    #[Template('file/detail.html.twig')]
    public function detail($id, Request $request, ImageRepository $imageRepository, DataManager $dataManager, FilterManager $filterManager)
    {
        $imageEntity = $imageRepository->find($id);
        $form = $this->createForm(ImageConvertType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $path = str_replace('/var/www/app/var', '', $imageEntity->getPath());
            $image = $dataManager->find($form->getData()['scale'], $path);
            $image = $filterManager->applyFilter($image, $form->getData()['scale']);
            file_put_contents($imageEntity->getPath(), $image->getContent());
            $imagick = new \Imagick($imageEntity->getPath());
            $format = $form->getData()['type'];
            $imagick->setImageFormat($format);
            file_put_contents($imageEntity->getPath(), $imagick->getImageBlob());
            $imageEntity->setMime($imagick->getImageMimeType());
            $imageEntity->setExtension($format);
            $imageRepository->save($imageEntity);
        }

        return [
            'file' => $imageEntity,
            'form' => $form,
        ];
    }

    #[Route('/{id}/src', name: 'src', methods: 'GET')]
    public function src($id, ImageRepository $imageRepository): BinaryFileResponse
    {
        $file = $imageRepository->find($id);

        return new BinaryFileResponse($file->getPath());
    }

}
