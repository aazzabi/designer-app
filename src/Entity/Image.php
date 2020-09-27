<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=ImageRepository::class)
 * @Vich\Uploadable
 */
class Image
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Folder", inversedBy="images")
     */
    private $folder;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image_size;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="folder_image", fileNameProperty="imageName", size="imageSize")
     *
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFolder(): ?Folder
    {
        return $this->folder;
    }

    public function setFolder(?Folder $folder): self
    {
        $this->folder = $folder;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->image_name;
    }

    public function setImageName(string $image_name): self
    {
        $this->image_name = $image_name;

        return $this;
    }

    public function getImageSize(): ?string
    {
        return $this->image_size;
    }

    public function setImageSize(?string $image_size): self
    {
        $this->image_size = $image_size;

        return $this;
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $imageFile
     * @throws \Exception
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }
}
