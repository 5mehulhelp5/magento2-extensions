<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Labels for Magento 2
 */

namespace Amasty\Label\Api\Data;

/**
 * @api
 */
interface LabelFrontendSettingsInterface
{
    public const PART_CODE = 'frontend_settings';

    public const LABEL_ID = 'label_id';
    public const TYPE = 'type';
    public const LABEL_TEXT = 'label_text';
    public const IMAGE = 'image';
    public const IMAGE_SIZE = 'image_size';
    public const POSITION = 'position';
    public const STYLE = 'style';
    public const ALT_TAG = 'alt_tag';
    public const REDIRECT_URL = 'redirect_url';

    /**
     * @return int
     */
    public function getType(): int;

    /**
     * @param int $type
     * @return void
     */
    public function setType(int $type): void;

    /**
     * @return string
     */
    public function getLabelText(): string;

    /**
     * @param string $labelText
     * @return void
     */
    public function setLabelText(string $labelText): void;

    /**
     * @return string|null
     */
    public function getImage(): ?string;

    /**
     * @param string|null $image
     * @return void
     */
    public function setImage(?string $image): void;

    /**
     * @return string|null
     */
    public function getImageSize(): ?string;

    /**
     * @param string|null $imageSize
     * @return void
     */
    public function setImageSize(?string $imageSize): void;

    /**
     * @return int
     */
    public function getPosition(): int;

    /**
     * @param int $imagePosition
     * @return void
     */
    public function setPosition(int $imagePosition): void;

    /**
     * @return string|null
     */
    public function getStyle(): ?string;

    /**
     * @param string|null $style
     * @return void
     */
    public function setStyle(?string $style): void;

    /**
     * @return string
     */
    public function getAltTag(): string;

    /**
     * @param string $altTag
     * @return void
     */
    public function setAltTag(string $altTag): void;

    /**
     * @return string
     */
    public function getRedirectUrl(): string;

    /**
     * @param string $redirectUrl
     * @return void
     */
    public function setRedirectUrl(string $redirectUrl): void;
}
