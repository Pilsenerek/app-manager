<?php

namespace App\MessageHandler;

use App\Message\ImageConversion;
use App\Service\ImageService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ImageConversionHandler
{

    public function __construct(
        private ImageService $imageService,
    )
    {
    }

    public function __invoke(ImageConversion $message)
    {
        $this->imageService->convert($message);
    }
}
