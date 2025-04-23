<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Mega Menu Base for Magento 2
 */

namespace Amasty\MegaMenu\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Config\Block\System\Config\Form\Fieldset;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Module\Manager;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\View\Helper\Js;

class Information extends Fieldset
{
    /**
     * @var string
     */
    private $userGuide = 'https://amasty.com/docs/doku.php?id=magento_2:mega_menu';

    /**
     * @var array
     */
    private $enemyExtensions = [];

    /**
     * @var string
     */
    private $content;

    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(
        Context $context,
        Session $authSession,
        Js $jsHelper,
        Manager $moduleManager,
        array $data = []
    ) {
        parent::__construct($context, $authSession, $jsHelper, $data);
        $this->moduleManager = $moduleManager;
    }

    /**
     * Render fieldset html
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $html = $this->_getHeaderHtml($element);

        $this->setContent(__('Please update Amasty Base module. Re-upload it and replace all the files.'));

        $this->_eventManager->dispatch(
            'amasty_base_add_information_content',
            ['block' => $this]
        );

        $html .= $this->getContent();
        $html .= $this->_getFooterHtml($element);

        $html = str_replace(
            'amasty_information]" type="hidden" value="0"',
            'amasty_information]" type="hidden" value="1"',
            $html
        );
        $html = preg_replace('(onclick=\"Fieldset.toggleCollapse.*?\")', '', $html);

        return $html;
    }

    /**
     * @return array|string
     */
    public function getAdditionalModuleContent()
    {
        if ($this->moduleManager->isEnabled('Magento_PageBuilder')
            && !$this->moduleManager->isEnabled('Amasty_MegaMenuPageBuilder')
        ) {
            $result[] = [
                'type' => 'message-notice',
                'text' => __('Enable mage-menu-pagebuilder module to activate PageBuilder and Mega Menu integration. '
                    . 'Please, run the following command in the SSH: composer require amasty/mega-menu-page-builder')
            ];
        }

        if ($this->moduleManager->isEnabled('Magento_GraphQl')
            && !$this->moduleManager->isEnabled('Amasty_MegaMenuGraphQl')
        ) {
            $result[] = [
                'type' => 'message-notice',
                'text' => __('Enable mega-menu-graphql module to '
                    . 'activate GraphQl and Mega Menu. '
                    . 'Please, run the following command in the SSH: '
                    . 'composer require amasty/mega-menu-graphql')
            ];
        }

        return $result ?? '';
    }

    public function getUserGuide(): string
    {
        return $this->userGuide;
    }

    public function setUserGuide(string $userGuide)
    {
        $this->userGuide = $userGuide;
    }

    public function getEnemyExtensions(): array
    {
        return $this->enemyExtensions;
    }

    public function setEnemyExtensions(array $enemyExtensions)
    {
        $this->enemyExtensions = $enemyExtensions;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content)
    {
        $this->content = $content;
    }
}
