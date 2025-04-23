<?php
namespace Milople\Depositpayment\Block\Order;
class Totals extends \Magento\Framework\View\Element\AbstractBlock
{
   public function initTotals()
   {
       $orderTotalsBlock = $this->getParentBlock();
       $order = $orderTotalsBlock->getOrder();
       $source = $orderTotalsBlock->getSource();
        if($source->getBasePaidAmount() > 0)
        {
            $orderTotalsBlock->addTotal(new \Magento\Framework\DataObject(
               [
                   'code' => 'partial_paid',
                   'value' => $source->getBasePaidAmount(),
                   'base_value' => $source->getBasePaidAmount(),
                   'strong' => false,
                   'area'=> 'footer',
                   'label' => "Paid Amount"
               ]
           ), 'subtotal');
        }
        if($source->getBaseRemainingAmount() > 0)
        {
            $orderTotalsBlock->addTotal(new \Magento\Framework\DataObject(
               [
                   'code' => 'remaining_amount',
                   'value' => $source->getBaseRemainingAmount(),
                   'base_value' => $source->getBaseRemainingAmount(),
                   'strong' => false,
                   'area'=> 'footer',
                   'label' => "Remaining Amount"
               ]
           ), 'subtotal');
        }
        if($source->getInstallmentFee() > 0)
        {
            $orderTotalsBlock->addTotal(new \Magento\Framework\DataObject(
               [
                   'code' => 'installment_fee',
                   'value' => $source->getInstallmentFee(),
                   'base_value' => $source->getInstallmentFee(),
                   'area'=> 'footer',
                   'strong' => false,
                   'label' => "Installment Fee"
               ]
           ), 'subtotal');
        }
   }
}