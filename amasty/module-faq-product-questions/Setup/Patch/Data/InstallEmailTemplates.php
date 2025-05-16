<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package FAQ and Product Questions for Magento 2
 */

namespace Amasty\Faq\Setup\Patch\Data;

use Amasty\Faq\Model\ConfigProvider;
use Magento\Email\Model\TemplateFactory;
use Magento\Framework\App\Area;
use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\State;
use Magento\Framework\Module\ResourceInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\Store;

class InstallEmailTemplates implements DataPatchInterface
{
    /**
     * @var ResourceInterface
     */
    private $moduleResource;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var TemplateFactory
     */
    private $emailTemplate;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var TypeListInterface
     */
    private $typeList;

    public function __construct(
        ResourceInterface $moduleResource,
        ModuleDataSetupInterface $moduleDataSetup,
        TemplateFactory $emailTemplate,
        WriterInterface $configWriter,
        State $appState,
        TypeListInterface $typeList
    ) {
        $this->moduleResource = $moduleResource;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->emailTemplate = $emailTemplate;
        $this->configWriter = $configWriter;
        $this->appState = $appState;
        $this->typeList = $typeList;
    }

    public function apply()
    {
        // Check if module was already installed or not.
        // If setup_version present in DB then we don't need to install email templates,
        // because setup_version is a marker.
        $setupDataVersion = (string)$this->moduleResource->getDataVersion('Amasty_Faq');
        if (!$setupDataVersion) {
            $this->moduleDataSetup->startSetup();
            $this->appState->emulateAreaCode(Area::AREA_ADMINHTML, [$this, 'saveAndSetEmails']);
            $this->moduleDataSetup->endSetup();
        }
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public function saveAndSetEmails()
    {
        $this->saveAndSetEmail(
            'Amasty FAQ: You received new question',
            'amastyfaq_admin_email_template',
            ConfigProvider::ADMIN_NOTIFY_EMAIL_TEMPLATE,
            Area::AREA_ADMINHTML
        );
        $this->saveAndSetEmail(
            'Amasty FAQ: Your question was answered',
            'amastyfaq_user_email_template',
            ConfigProvider::USER_NOTIFY_EMAIL_TEMPLATE
        );
        $this->typeList->invalidate(Config::TYPE_IDENTIFIER);
    }

    private function saveAndSetEmail(
        string $code,
        string $originalCode,
        string $configPath,
        string $area = Area::AREA_FRONTEND
    ) {
        try {
            /** @var \Magento\Email\Model\Template $mailTemplate */
            $mailTemplate = $this->emailTemplate->create();

            $mailTemplate->setDesignConfig(
                ['area' => $area, 'store' => Store::DEFAULT_STORE_ID]
            )->loadDefault(
                $originalCode
            )->setTemplateCode(
                $code
            )->setOrigTemplateCode(
                $originalCode
            )->setId(
                null
            )->save();

            $this->configWriter->save(ConfigProvider::PATH_PREFIX . $configPath, $mailTemplate->getId());
        } catch (\Exception $e) {
            null;
        }
    }
}
