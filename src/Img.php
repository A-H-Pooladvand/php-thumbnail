<?php

namespace Thumb;

use RuntimeException;
use Thumb\lib\Str\Str;
use Intervention\Image\Image;
use Intervention\Image\Constraint;
use Intervention\Image\ImageManager;

class Img
{
    private $imageManager;

    private $repository;

    private $destination;

    private $height;

    private $width;

    /**
     * @var Image $image
     */
    private $image;

    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;

        $this->setRepository();
        $this->setDestination();
    }

    public function make(string $path, int $width = null, int $height = null, string $mode = null): void
    {
        $this->width = $width;
        $this->height = $height;
        $this->validateParameters();

        $path = $this->normalizePath($path);
        $this->setImage($path);

        $mode = $mode ?? 'resize';

        if ('resize' === $mode) {
            $this->resize();
        }

        if ('crop' === $mode) {
            $this->crop();
        }

        if ('fit' === $mode) {
            $this->fit();
        }

        $this->makeDestinationDirectory($path);
        $this->image->save($this->getDestination().$this->getFileName($path));
        $this->image->destroy();
    }

    private function setRepository(): void
    {
        $this->repository = public_dir(
            config('setting.images_directory')
        );
    }

    private function getRepository()
    {
        return $this->repository;
    }

    private function normalizePath(string $path): string
    {
        $firstChar = $path[0];

        if ($firstChar === '/') {
            return $path;
        }

        return '/'.$path;
    }

    private function setDestination(): void
    {
        $this->destination = public_dir(
            config('setting.thumbnails_directory')
        );
    }

    private function getDestination(): string
    {
        return $this->destination;
    }

    private function setImage(string $path): self
    {
        $this->image = $this->imageManager->make($this->getRepository().$path);

        return $this;
    }

    private function makeDestinationDirectory(string $path): void
    {
        @mkdir($this->getDestination().Str::withoutFileName($path), 0755, true);
    }

    private function crop(): self
    {
        if (null === $this->width && null === $this->height) {
            throw new RuntimeException('Width or height needs to be defined.');
        }

        $this->setMissingParams();

        $this->image->crop($this->width, $this->height);

        return $this;
    }

    private function setAutoWidth(): self
    {
        $this->width = round($this->height * 16 / 9);

        return $this;
    }

    private function setAutoHeight(): self
    {
        $this->height = round($this->width * 9 / 16);

        return $this;
    }

    private function getFileName(string $path)
    {
        return substr_replace($path, "_{$this->width}x{$this->height}", strrpos($path, '.'), 0);
    }

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

    private function validateParameters(): void
    {
        if ($this->width > 2000 || $this->height > 2000) {
            throw new RuntimeException('Width or height value can not be greater than 2000');
        }
    }

    private function resize(): self
    {
        $this->image->resize($this->width, $this->height, static function (Constraint $constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        return $this;
    }

    private function fit(): self
    {
        $this->image->fit($this->width, $this->height, static function (Constraint $constraint) {
            $constraint->upsize();
        });

        return $this;
    }
}