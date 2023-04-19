<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Loggable\Loggable;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[Gedmo\Loggable]
class Image
{

    public const UPLOAD_DIR = '/var/www/app/var/upload';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\Image()]
    #[Gedmo\Versioned]
    private ?string $filename = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Gedmo\Versioned]
    private ?string $extension = null;

    #[ORM\Column(length: 255)]
    #[Gedmo\Versioned]
    private ?string $mime = null;

    #[ORM\Column(length: 255)]
    #[Gedmo\Versioned]
    private ?string $label = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Gedmo\Versioned]
    private ?int $status = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function getPath(): ?string
    {

        return self::UPLOAD_DIR . '/' . $this->filename[0] . '/' . $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getMime(): ?string
    {
        return $this->mime;
    }

    public function setMime(string $mime): self
    {
        $this->mime = $mime;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }
}
