<?php
namespace Milople\Depositpayment\Block\Order\Invoice;
class Totals extends \Magento\Framework\View\Element\AbstractBlock
{
   public function initTotals()
   {
      \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info('Invoice total block file is called');
       $orderTotalsBlock = $this->getParentBlock();
       $order = $orderTotalsBlock->getOrder();
       $source = $orderTotalsBlock->getSource();
       
       \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($order->getBasePaidAmount());
       \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($order->getBaseRemainingAmount());
       \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($order->getInstallmentFee());

        if($order->getBasePaidAmount() > 0)
        {
            $orderTotalsBlock->addTotal(new \Magento\Framework\DataObject(
               [
                   'code' => 'partial_paid',
                   'value' => $order->getBasePaidAmount(),
                   'base_value' => $order->getBasePaidAmount(),
                   'strong' => false,
                   'area'=> 'footer',
                   'label' => "Paid Amount"
               ]
           ), 'subtotal');
        }
        if($order->getBaseRemainingAmount() > 0)
        {
            $orderTotalsBlock->addTotal(new \Magento\Framework\DataObject(
               [
                   'code' => 'remaining_amount',
                   'value' => $order->getBaseRemainingAmount(),
                   'base_value' => $order->getBaseRemainingAmount(),
                   'strong' => false,
                   'area'=> 'footer',
                   'label' => "Remaining Amount"
               ]
           ), 'subtotal');
        }
        if($order->getInstallmentFee() > 0)
        {
            $orderTotalsBlock->addTotal(new \Magento\Framework\DataObject(
               [
                   'code' => 'installment_fee',
                   'value' => $order->getInstallmentFee(),
                   'base_value' => $order->getInstallmentFee(),
                   'area'=> 'footer',
                   'strong' => false,
                   'label' => "Installment Fee"
               ]
           ), 'subtotal');
        }
   }
}