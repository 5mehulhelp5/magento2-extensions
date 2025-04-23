<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\BlogPlus\Block\Category;

class Image extends \Magefan\Blog\Block\Category\AbstractCategory
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
        return (string)$this->getCategory()->getCategoryImage();
    }

    /**
     * @return string
     */
    public function getImageAlt(): string
    {
        return (string)$this->getCategory()->getData('category_img_alt') ?: $this->getCategory()->getTitle();
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
