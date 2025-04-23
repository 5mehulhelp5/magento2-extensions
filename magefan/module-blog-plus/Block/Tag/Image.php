<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\BlogPlus\Block\Tag;

class Image extends \Magefan\Blog\Block\Tag\AbstractTag
{
    /**
     * @var array
     */
    private $imageSize;

    /**
     * @return string
     */
    public function getImageUrl(): string
    {
        return (string)$this->getTag()->getTagImage();
    }

    /**
     * @return string
     */
    public function getImageAlt(): string
    {
        return (string)$this->getTag()->getData('tag_img_alt') ?: $this->getTag()->getTitle();
    }

    /**
     * @return int
     */
    public function getImageWidth(): int
    {
        if (null === $this->imageSize) {
            $this->imageSize = getimagesize($this->getImageUrl());
        }

        if ($this->imageSize && isset($this->imageSize[0])) {
            return (int)$this->imageSize[0];
        }

        return 0;
    }

    /**
     * @return int
     */
    public function getImageHeight(): int
    {
        if (null === $this->imageSize) {
            $this->imageSize = getimagesize($this->getImageUrl());
        }

        if ($this->imageSize && isset($this->imageSize[1])) {
            return (int)$this->imageSize[1];
        }

        return 0;
    }
}
