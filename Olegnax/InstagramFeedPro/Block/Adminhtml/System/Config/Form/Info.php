<?php
/**
 * @author      Olegnax
 * @package     Olegnax_InstagramFeedPro
 * @copyright   Copyright (c) 2021 Olegnax (http://olegnax.com/). All rights reserved.
 * @noinspection PhpDeprecationInspection
 */
declare(strict_types=1);

namespace Olegnax\InstagramFeedPro\Block\Adminhtml\System\Config\Form;

use Exception;
use Magento\Backend\Block\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\View\Helper\Js;
use Olegnax\Core\Block\Adminhtml\NoticeContent;
use Olegnax\Core\Helper\ModuleInfo;
use Olegnax\InstagramFeedPro\Helper\Helper;

class Info extends \Olegnax\Core\Block\Adminhtml\System\Config\Form\Info
{
    /**
     * @var Helper
     */
    protected $_helper;

    /**
     * Info constructor.
     * @param Context $context
     * @param Session $authSession
     * @param Js $jsHelper
     * @param NoticeContent $content
     * @param ModuleInfo $moduleInfo
     * @param Helper $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $authSession,
        Js $jsHelper,
        NoticeContent $content,
        ModuleInfo $moduleInfo,
        Helper $helper,
        array $data = []
    ) {
        $this->_helper = $helper;
        parent::__construct($context, $authSession, $jsHelper, $content, $moduleInfo, $data);
    }

    /**
     * @param array $data
     * @throws Exception
     * @throws Exception
     */
    public function toTPL($data = [])
    {
        $status = $this->status($data);
        $rightUrls = [
            'docs' => __('User Guide'),
        ];
        $rightBlock = '';
        foreach ($rightUrls as $key => $label) {
            if (isset($data[$key]) && !empty($data[$key])) {
                $rightBlock .= '<div class="ox-info-block__' . $key . '"><a href="' . $this->escapeUrl($data[$key]) . '" target="_blank">' . $label . '</a></div>';
            }
        }
        $beforeBlock = '';
        $rightBlock .= $this->rightCustomBlocks($data);
        if (!$status && 'olegnax_instagram_license' != $this->getRequest()->getParam('section')) {
            $beforeBlock = '<div class="message message-error error" style="margin-bottom:20px;"><span>' . __('Extension is not activated! Some functionality is disabled.') . '</span> <a href="' . $this->getLicenseUrl() . '">' . __('Click here to Activate') . '</a></div>';
        }
        echo $beforeBlock;
        ?>
		<div class="ox-info-block">
			<a href="https://olegnax.com/" target="_blank" class="ox-info-block__logo"></a>
			<div class="ox-info-block__title"><?= $this->escapeHtml($data['title']); ?></div>
            <?= $this->leftCustomBlocks($data); ?>
            <?php if ($status) : ?>
                <?php if ($data['update_status']): ?>
					<div class="ox-info-block__version expired">
						<div class="ox-module-version">v<?= $this->escapeHtml($data['setup_version']); ?> <span
									class="ox-server-version">v<?= $this->escapeHtml($data['server_version']) ?></span><?php if (isset($data['url_changelog']) && !empty($data['url_changelog'])): ?>
							<a href="<?= $this->escapeUrl($data['url_changelog']) ?>"
							   target="_blank"><?= __("What's New") ?></a></div><?php endif; ?>
					</div>
                <?php else: ?>
					<div class="ox-info-block__version">
						<div class="ox-module-version">v<?= $this->escapeHtml($data['setup_version']); ?></div>
					</div>
                <?php endif; ?>
            <?php else: ?>
				<strong style="color:#a4a4a4">Extension is not Activated.</strong>
            <?php endif; ?>
            <?php if (!empty($rightBlock)): ?>
				<div class="ox-info-block__right"><?= $rightBlock; ?></div><?php endif; ?>
		</div>
        <?php
    }

    /**
     * Left Custom Blocks
     *
     * @param array $data
     * @return string
     * @throws Exception
     * @throws Exception
     * @noinspection PhpUnusedParameterInspection
     */
    protected function status($data = [])
    {
        $license = $this->_helper->get();
        $code = $this->_helper->getSystemDefaultValue(Helper::OPT_PREFIX . 'code');
        $status = !empty($license)
            && isset($license->data->the_key)
            && $license->data->the_key == $code
            && $license->data->status == "active";
        return $status;
    }

    /**
     * Right Custom Blocks
     *
     * @param array $data
     * @return string
     * @throws Exception
     * @throws Exception
     */
    protected function rightCustomBlocks($data = [])
    {
        $license = $this->_helper->get();
        $status = $this->status($data);
        $supportExpired = $status && $license->data->has_expired;
        $notice = '';
        if ($status) {
            $notice .= '<div class="ox-info-block__support support-' . ($supportExpired ? 'expired' : 'active') . '"><div class="ox-wrapper"><span class="label">' . __('Support') . '</span>';
            if ($supportExpired) {
                $notice .= '<a href="#" target="_blank">' . __('Renew') . '</a>';
            } else {
                $notice .= '<a href="https://olegnax.com/help" target="_blank">' . __('Active') . '</a>';
            }
            $notice .= '</div></div>';
        }
        return $notice;
    }

    /**
     * @return string
     */
    protected function getLicenseUrl()
    {
        return $this->getUrl(
            '*/*/*',
            [
                '_current' => true,
                'section' => 'olegnax_instagram_license',
            ]
        );
    }

    /**
     * @param array $data
     * @return string
     * @throws Exception
     */
    protected function leftCustomBlocks($data = [])
    {
        $status = $this->status($data);
        $license = $this->_helper->get();
        $devLicense = $status && isset($license->notices->develop);
        if ($devLicense) {
            return '<div class="ox-info-block__dev">' . __('Dev License Activated. <br>Do not use on live store.') . '</div>';
        }
        return '';
    }
}
