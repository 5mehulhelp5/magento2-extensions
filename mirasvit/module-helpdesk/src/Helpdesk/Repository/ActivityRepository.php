<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-helpdesk
 * @version   1.3.6
 * @copyright Copyright (C) 2025 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Helpdesk\Repository;

use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\Helpdesk\Api\Data\ActivityInterface;
use Mirasvit\Helpdesk\Api\Data\ActivityInterfaceFactory;
use Mirasvit\Helpdesk\Model\ResourceModel\Activity\CollectionFactory;

class ActivityRepository
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ActivityInterfaceFactory
     */
    private $factory;

    public function __construct(
        EntityManager $entityManager,
        CollectionFactory $collectionFactory,
        ActivityInterfaceFactory $hostFactory
    ) {
        $this->entityManager = $entityManager;
        $this->collectionFactory = $collectionFactory;
        $this->factory = $hostFactory;
    }

    /**
     * @return \Mirasvit\Helpdesk\Model\ResourceModel\Host\Collection|ActivityInterface[]
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * @return ActivityInterface
     */
    public function create()
    {
        return $this->factory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $host = $this->create();
        $host = $this->entityManager->load($host, $id);

        if (!$host->getId()) {
            return false;
        }

        return $host;
    }

    /**
     * @param ActivityInterface $activity
     * @return bool
     */
    public function delete(ActivityInterface $activity)
    {
        $this->entityManager->delete($activity);

        return true;
    }

    /**
     * @param ActivityInterface $activity
     * @return ActivityInterface
     */
    public function save(ActivityInterface $activity)
    {
        return $this->entityManager->save($activity);
    }
}
