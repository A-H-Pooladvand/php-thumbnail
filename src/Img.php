<?php

namespace Thumb;

use RuntimeException;
use Thumb\lib\Str\Str;
use Intervention\Image\Image;
use Intervention\Image\Constraint;
use Intervention\Image\ImageManager;

class Img
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
     *
     * @param int|null $quality
     * @return string
     */
    public function make(string $path, int $width = null, int $height = null, string $mode = null, int $quality = null): string
    {
        $this->width = $width;
        $this->height = $height;
        $this->validateParameters();
        $path = $this->normalizePath($path);

        if ($this->fileAlreadyExists($path, $mode)) {
            return $this->imagePath($path, $mode);
        }

        $this->setImage($path);
        $this->manipulate($mode);
        $this->makeDestinationDirectory($path);
        $this->image->save($this->getDestination().$this->getFileName($path, $mode), $quality ?? 100);
        $this->image->destroy();

        return $this->imagePath($path, $mode);
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
     * Get repository property.
     *
     * @return string
     */
    private function getRepository(): string
    {
        return $this->repository;
    }

    /**
     * Normalizes given path, if path has / at the beginning
     * we do nothing otherwise we add a slash
     * to the begin of the path
     *
     * @param string $path
     *
     * @return string
     */
    private function normalizePath(string $path): string
    {
        $firstChar = $path[0];

        if ($firstChar === '/') {
            return $path;
        }

        return '/'.$path;
    }

    /**
     * Set destination folder.
     *
     * @return \Thumb\Img
     */
    private function setDestination(): self
    {
        $this->destination = base_dir(
                config('setting.destination_directory')
            );

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
     * Find image and set it.
     *
     * @param string $path
     *
     * @return \Thumb\Img
     */
    private function setImage(string $path): self
    {
        $this->image = $this->imageManager->make($this->getRepository().$path);

        return $this;
    }

    /**
     * Creates storage folder if not exists.
     *
     * @param string $path
     *
     * @return \Thumb\Img
     */
    private function makeDestinationDirectory(string $path): self
    {
        @mkdir($this->getDestination().Str::withoutFileName($path), 0755, true);

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

        $this->image->crop($this->width, $this->height);

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
     * @param string $path
     *
     * @param string $mode
     * @return mixed
     */
    private function getFileName(string $path, string $mode)
    {
        return substr_replace($path, "_{$this->width}x{$this->height}_{$mode}", strrpos($path, '.'), 0);
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
        $this->image->resize($this->width, $this->height, static function (Constraint $constraint) {
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
        $this->image->fit($this->width, $this->height, static function (Constraint $constraint) {
            $constraint->upsize();
        });

        return $this;
    }

    /**
     * Determines image path to echo in views....
     *
     * @param string $path
     * @param string $mode
     * @return string
     */
    private function imagePath(string $path, string $mode): string
    {
        return config('setting.destination_directory').$this->getFileName($path, $mode);
    }

    /**
     * Validates whether file already exists in destination folder.
     *
     * @param string $path
     * @param string $mode
     * @return bool
     */
    private function fileAlreadyExists(string $path, string $mode): bool
    {
        return file_exists($this->getDestination().$this->getFileName($path, $mode));
    }

    /**
     * Manipulates given image based on the given mode.
     *
     * @param string|null $mode
     * @return \Thumb\Img
     */
    private function manipulate(string $mode = null): self
    {
        if ('fit' === $mode) {
            return $this->fit();
        }

        if ('crop' === $mode) {
            return $this->crop();
        }

        return $this->resize();
    }
}
