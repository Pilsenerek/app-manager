<?php

namespace App\Service;

use App\Entity\Image;
use App\Form\ImageType;
use App\Message\ImageConversion;
use App\Repository\ImageRepository;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\MessageBusInterface;

class ImageService
{

    public function __construct(
        private MessageBusInterface $bus,
        private ImageRepository     $imageRepository,
        private DataManager $dataManager,
        private FilterManager $filterManager
    )
    {
    }

    public function upload(FormInterface $form, Request $request): bool
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageEntity = $form->getData();
            /** @var UploadedFile $image */
            $image = $form->get('filename')->getData();
            if ($image) {
                $newFilename = $this->generateFileDiscName();
                $imageEntity->setLabel($image->getClientOriginalName());
                $imageEntity->setFilename($newFilename);
                $imageEntity->setExtension($image->guessExtension());
                $imageEntity->setMime($image->getMimeType());
                $image->move(
                    Image::UPLOAD_DIR . '/' . $newFilename[0],
                    $newFilename
                );
                $this->imageRepository->save($imageEntity);
                $message = new ImageConversion(
                    $imageEntity->getId(),
                    $form->get('scale')->getData(),
                    $form->get('type')->getData()
                );
                $this->bus->dispatch($message);

                return true;
            }
        }

        return false;
    }

    public function convert(ImageConversion $message) : void {
        $imageEntity = $this->imageRepository->find($message->getId());
        $imageBlob = $this->scale($imageEntity, $message);
        $imageBlob = $this->format($imageEntity, $message, $imageBlob);
        $imageEntity->setFilename($this->generateFileDiscName());
        if (!file_exists(Image::UPLOAD_DIR . '/' . $imageEntity->getFilename()[0])) {
            mkdir(Image::UPLOAD_DIR . '/' . $imageEntity->getFilename()[0]);
        }
        file_put_contents($imageEntity->getPath(), $imageBlob);
        $imageEntity->setStatus(1);
        $this->imageRepository->save($imageEntity);
    }

    private function format(Image $imageEntity, ImageConversion $message, string $imageBlob) : string
    {
        $format = $message->getFormat();
        if (!strpos($imageEntity->getMime(), $format)) {
            $imagick = new \Imagick();
            $imagick->readImageBlob($imageBlob);
            $imagick->setImageFormat($format);
            $imageEntity->setMime($imagick->getImageMimeType());
            $imageEntity->setExtension($format);
            $imageEntity->setLabel($imageEntity->getLabel() . '.' . $format);

            return $imagick->getImageBlob();
        }

        return $imageBlob;
    }

    private function scale(Image $imageEntity, ImageConversion $message): string
    {
        $path = $imageEntity->getPath();
        $image = $this->dataManager->find('thumbnail', $path);
        $image = $this->filterManager->applyFilter($image, 'thumbnail', [
            'filters' => [
                'thumbnail' => [
                    'size' => [$message->getScale(), $message->getScale()],
                    'mode' => 'outbound',
                ],
            ]
        ]);

        return $image->getContent();
    }

    private function generateFileDiscName(): string
    {

        return md5(uniqid('', true));
    }
}
