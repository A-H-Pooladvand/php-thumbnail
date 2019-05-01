<?php

namespace Thumb;

use RuntimeException;
use Thumb\lib\Str\Str;
use Intervention\Image\Image;
use Intervention\Image\Constraint;
use Intervention\Image\ImageManager;

final class Img
{
    /**
     * ImageManager instance.
     *
     * @var \Intervention\Image\ImageManager
     */
    private $imageManager;

    /**
     * Determines source folder
     * to get images from.
     *
     * @var string $repository
     */
    private $repository;

    /**
     * Determines destination folder
     * to store image thumbnails.
     *
     * @var string $destination
     */
    private $destination;

    /**
     * Determines height of the resizing image.
     *
     * @var int|null $height
     */
    private $height;

    /**
     * Determines width of the resizing image.
     *
     * @var int|null $width
     */
    private $width;

    /**
     * Image instance of intervention.
     *
     * @var Image $image
     */
    private $image;

    /**
     * Determines Quality of image.
     *
     * @var int $quality
     */
    private $quality;

    /**
     * Mode of image for manipulation.
     *
     * @var string $mode
     */
    private $mode;

    /**
     * Image path.
     *
     * @var string $path
     */
    private $path;

    /**
     * Img constructor.
     *
     * @param \Intervention\Image\ImageManager $imageManager
     */
    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;

        $this->setRepository();
        $this->setDestination();
    }

    /**
     * Resize|Crop|Fit an image and manipulate it's width
     * and height based on the given parameters
     *
     * @param string $path
     * @param int $width
     * @param int $height
     * @param string $mode
     * @param int|null $quality
     *
     * @return string
     */
    public function make(string $path, int $width = null, int $height = null, string $mode = null, int $quality = null): string
    {
        $this->setPath($path);
        $this->setMode($mode);
        $this->setQuality($quality);
        $this->setWidth($width);
        $this->setHeight($height);

        $this->validateParameters();

        if ($this->fileAlreadyExists()) {
            return $this->imagePath();
        }

        $this->setImage();
        $this->manipulate();
        $this->makeDestinationDirectory();
        $this->getImage()->save($this->getDestination().$this->getFileName(true), $quality);
        $this->getImage()->destroy();

        return $this->imagePath();
    }

    /**
     * Set repository (source folder).
     *
     * @return \Thumb\Img
     */
    private function setRepository(): self
    {
        $this->repository = base_dir();

        return $this;
    }

    /**
     * Set destination folder.
     *
     * @return \Thumb\Img
     */
    private function setDestination(): self
    {
        $this->destination = base_dir().'/thumbnails';

        return $this;
    }

    /**
     * Get destination folder.
     *
     * @return string
     */
    private function getDestination(): string
    {
        return $this->destination;
    }

    /**
     * Get repository property.
     *
     * @return string
     */
    private function getRepository(): string
    {
        return $this->repository;
    }

    /**
     * Find image and set it.
     *
     * @return void
     */
    private function setImage(): void
    {
        $this->image = $this->imageManager->make($this->getRepository().$this->getPath());
    }

    /**
     * Get image intervention image instance.
     *
     * @return \Intervention\Image\Image
     */
    private function getImage(): Image
    {
        return $this->image;
    }

    /**
     * Normalizes given path, if path has / at the beginning
     * we do nothing otherwise we add a slash
     * to the begin of the path
     *
     * @param string $path
     *
     * @return void
     */
    private function setPath(string $path): void
    {
        $firstChar = $path[0];

        if (DIRECTORY_SEPARATOR === $firstChar) {
            $this->path = $path;

            return;
        }

        $this->path = DIRECTORY_SEPARATOR.$path;
    }

    /**
     * @return int|null
     */
    public function getHeight(): ?int
    {
        return $this->height;
    }

    /**
     * @param int|null $height
     */
    public function setHeight(?int $height): void
    {
        $this->height = $height;
    }

    /**
     * Get image path.
     *
     * @return string
     */
    private function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return int|null
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * @param int|null $width
     */
    public function setWidth(?int $width): void
    {
        $this->width = $width;
    }

    /**
     * Creates storage folder if not exists.
     *
     * @return \Thumb\Img
     */
    private function makeDestinationDirectory(): self
    {
        @mkdir($this->getDestination().Str::withoutFileName($this->filePath()), 0755, true);

        return $this;
    }

    /**
     * Crop the given image based on
     * the given width and height.
     *
     * @return \Thumb\Img
     */
    private function crop(): self
    {
        if (null === $this->width && null === $this->height) {
            throw new RuntimeException('Width or height needs to be defined.');
        }

        $this->setMissingParams();

        $this->getImage()->crop($this->width, $this->height);

        return $this;
    }

    /**
     * If width is null we will set
     * width to reasonable amount.
     *
     * @return \Thumb\Img
     */
    private function setAutoWidth(): self
    {
        $this->width = round($this->height * 16 / 9);

        return $this;
    }

    /**
     * If height is null we will set
     * height to reasonable amount.
     *
     * @return \Thumb\Img
     */
    private function setAutoHeight(): self
    {
        $this->height = round($this->width * 9 / 16);

        return $this;
    }

    /**
     * Determining file name and adding width and height to file name.
     *
     * @param bool $isSave
     * @return string
     */
    private function getFileName(bool $isSave = null): string
    {
        $isSave = $isSave ?? false;

        $path = $isSave ? $this->filePath() : $this->getPath();

        $path = substr($path, strpos($path, DIRECTORY_SEPARATOR));

        return substr_replace($path, "_{$this->getWidth()}x{$this->getHeight()}_{$this->getMode()}_{$this->getQuality()}", strrpos($path, '.'), 0);
    }

    /**
     * If width or height is null we will
     * set them to a reasonable amount.
     *
     * @return \Thumb\Img
     */
    private function setMissingParams(): self
    {
        if (null === $this->width) {
            $this->setAutoWidth();
        }

        if (null === $this->height) {
            $this->setAutoHeight();
        }

        return $this;
    }

    /**
     * Validates parameters so they cant
     * be greater than 2000.
     */
    private function validateParameters(): void
    {
        if ($this->width > 2000 || $this->height > 2000) {
            throw new RuntimeException('Width or height value can not be greater than 2000');
        }
    }

    /**
     * Resize image to given width and height.
     *
     * @return \Thumb\Img
     */
    private function resize(): self
    {
        $this->getImage()->resize($this->width, $this->height, static function (Constraint $constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        return $this;
    }

    /**
     * Resize and make image to fit as possible.
     *
     * @return \Thumb\Img
     */
    private function fit(): self
    {
        $this->getImage()->fit($this->width, $this->height, static function (Constraint $constraint) {
            $constraint->upsize();
        });

        return $this;
    }

    /**
     * Determines image path to echo in views....
     *
     * @return string
     */
    private function imagePath(): string
    {
        return 'thumbnails'.$this->getFileName(true);
    }

    /**
     * Validates whether file already exists in destination folder.
     *
     * @return bool
     */
    private function fileAlreadyExists(): bool
    {
        if ('resize' === $this->getMode()) {
            $this->setMissingParams();
        }

        return file_exists($this->getDestination().$this->getFileName(true));
    }

    /**
     * Manipulates given image based on the given mode.
     *
     * @return \Thumb\Img
     */
    private function manipulate(): self
    {
        $mode = $this->getMode();
        if (! method_exists($this, $mode)) {
            throw new RuntimeException("{$mode} option does not exists. please provide a valid option");
        }

        $this->$mode();

        return $this;
    }

    /**
     * Get file path.
     *
     * @return string
     */
    public function filePath(): string
    {
        $path = $this->getPath();
        $firstCharacter = $path[0];

        if (DIRECTORY_SEPARATOR === $firstCharacter) {
            $path = trim($path, DIRECTORY_SEPARATOR);
        }

        return substr($path, strpos($path, DIRECTORY_SEPARATOR));
    }

    /**
     * @param string|null $mode
     *
     * @return void
     */
    private function setMode(string $mode = null): void
    {
        $this->mode = $mode ?? 'resize';
    }

    /**
     * @return string
     */
    private function getMode(): string
    {
        return $this->mode;
    }

    /**
     * @param int $quality
     *
     * @return void
     */
    private function setQuality(int $quality = null): void
    {
        $this->quality = $quality ?? 100;
    }

    /**
     * @return int
     */
    private function getQuality(): int
    {
        return $this->quality;
    }
}
