<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Advanced Reports Base for Magento 2
 */

namespace Amasty\Reports\Block\Adminhtml\Report\Sales\Overview\Compare;

use Amasty\Reports\Block\Adminhtml\Navigation;
use Amasty\Reports\Helper\Data;
use Amasty\Reports\Model\OptionSource\Rule\FormValue;
use Amasty\Reports\Model\Source\IndexedAttributes;
use Amasty\Reports\Model\Utilities\GetDefaultFromDate;
use Amasty\Reports\Model\Utilities\GetDefaultToDate;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Button;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Form\AbstractForm;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;

class Toolbar extends \Amasty\Reports\Block\Adminhtml\Report\Sales\Overview\Toolbar
{
    public const FIELDSET_COUNT = 3;

    public const COLOR_FIRST_LINE = '#78b5d9';

    public const COLOR_SECOND_LINE = '#6f94d7';

    public const COLOR_THIRD_LINE = '#7c69d6';

    /**
     * @var GetDefaultFromDate
     */
    private $getDefaultFromDate;

    /**
     * @var GetDefaultToDate
     */
    private $getDefaultToDate;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        Data $helper,
        Navigation $navigation,
        Collection $eavCollection,
        FormValue $formValueRules,
        \Amasty\Reports\Block\Adminhtml\Framework\Data\FormFactory $reportFormFactory,
        IndexedAttributes $indexedAttributes,
        array $data = [],
        GetDefaultFromDate $getDefaultFromDate = null, // TODO move to not optional
        GetDefaultToDate $getDefaultToDate = null
    ) {
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $systemStore,
            $helper,
            $navigation,
            $eavCollection,
            $formValueRules,
            $reportFormFactory,
            $indexedAttributes,
            $data,
            $getDefaultFromDate,
            $getDefaultToDate
        );
        $this->getDefaultFromDate = $getDefaultFromDate ?? ObjectManager::getInstance()->get(GetDefaultFromDate::class);
        $this->getDefaultToDate = $getDefaultToDate ?? ObjectManager::getInstance()->get(GetDefaultToDate::class);
    }

    /**
     * @param AbstractForm $parentElement
     * @return $this|\Amasty\Reports\Block\Adminhtml\Report\Sales\Overview\Toolbar
     */
    protected function addDateControls(AbstractForm $parentElement)
    {
        $dateFormat = 'y-MM-dd';
        for ($i = 0; $i < self::FIELDSET_COUNT; $i++) {
            $fieldset = $parentElement->addFieldset(
                'from_to_' . $i,
                [
                    'legend' => __('Range #%1   ', $i + 1),
                    'class' => 'amreports-ranges-fieldset',
                    'wrapperclass' => 'amreports-fieldset-container'
                ]
            );

            [$fromValue, $toValue] = $this->getFromToValues($i);

            $fieldset->addField(
                'from_' . $i,
                'date',
                [
                    'label' => __('From'),
                    'name' => 'from_' . $i,
                    'date_format' => $dateFormat,
                    'format' => $dateFormat,
                    'value' => $fromValue
                ]
            );

            $fieldset->addField(
                'to_' . $i,
                'date',
                [
                    'label' => __('To'),
                    'name' => 'to_' . $i,
                    'format' => $dateFormat,
                    'date_format' => $dateFormat,
                    'value' => $toValue
                ]
            );
        }

        return $this;
    }

    /**
     * @param $index
     * @return array
     */
    private function getFromToValues($index)
    {
        $fromValue = null;
        $toValue = null;
        if ($index == 0) {
            $fromValue = $this->getDefaultFromDate->getDate();
            $toValue = $this->getDefaultToDate->getDate();
        }

        return [$fromValue, $toValue];
    }

    /**
     * @param AbstractForm $form
     * @return $this|\Amasty\Reports\Block\Adminhtml\Report\Sales\Overview\Toolbar
     * @throws LocalizedException
     */
    protected function addControls(AbstractForm $form)
    {
        parent::addControls($form);
        $form->addField(
            'submit',
            'note',
            [
                'text' => $this->getLayout()->createBlock(
                    Button::class
                )->setData(
                    ['label' => __('Compare'), 'class' => 'left']
                )->toHtml()
            ]
        );

        return $this;
    }

    /**
     * @return string
     */
    public function getDataRole()
    {
        return 'amreports-toolbar_on_button';
    }

    /**
     * @inheritdoc
     */
    protected function addViewControls(AbstractForm $form, $values, $defaultValue)
    {
        return $this;
    }
}
