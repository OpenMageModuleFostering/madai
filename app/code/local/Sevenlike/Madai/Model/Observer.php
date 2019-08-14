<?php 

class Sevenlike_Madai_Model_Observer {
	
	public function applyDiscount(Varien_Event_Observer $observer)
	{
		/* @var $item Mage_Sales_Model_Quote_Item */
		$item	= $observer->getQuoteItem();
		if ($item->getParentItem()) {
			$item = $item->getParentItem();
		}
		$madai_discount = Mage::getSingleton('checkout/session')->getQuote()->getMadaiDiscount();
		$madai_order_id = Mage::getSingleton('checkout/session')->getQuote()->getMadaiOrderId();
		// ho il prodotto Madai nel carrello
		if ($madai_discount){
			$actual_qty = floatval(Mage::helper('madai')->getQty($item->getProduct()));
			if ($actual_qty==1){
				$specialPrice = $item->getProduct()->getPrice()-$madai_discount;
				$item->setMadaiDiscount($madai_discount);
				$item->setMadaiOrderId($madai_order_id);
			}
			else{
				$specialPrice = (($item->getProduct()->getPrice()*$actual_qty)-$madai_discount)/$actual_qty;
			}
			$item->setCustomPrice($specialPrice);
			$item->setOriginalCustomPrice($specialPrice);
			$item->getProduct()->setIsSuperMode(true);
		}
		
	}

	/**
	 * @param Varien_Event_Observer $observer
	 */
	public function applyDiscounts(Varien_Event_Observer $observer)
	{

		foreach ($observer->getCart()->getQuote()->getAllVisibleItems() as $item /* @var $item Mage_Sales_Model_Quote_Item */) {
			if ($item->getParentItem()) {
				$item = $item->getParentItem();
			}
			if ($item->getMadaiDiscount() > 0) {
				$specialPrice = (($item->getProduct()->getPrice()*$item->getQty())-$item->getMadaiDiscount())/$item->getQty();
				$item->setCustomPrice($specialPrice);
				$item->setOriginalCustomPrice($specialPrice);
				$item->getProduct()->setIsSuperMode(true);
			}
		}
	}
	/*
	public function setCustomDataOnQuoteItem(Varien_Event_Observer $observer)
	{
	
		$quote = Mage::getModel('checkout/cart')->getQuote();
		$orderItem = $observer->getOrderItem();
		foreach ( $quote->getAllVisibleItems() as $_item )
		{
			if ($_item->getSku() == $orderItem->getSku())
			{
				$orderItem->setMadaiDiscount($_item->getMadaiDiscount());
			}
		}
	}
	*/
	
	public function sendOrderConfirmation(Varien_Event_Observer $observer)
    {
        $orderIds = $observer->getEvent()->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $block = Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('madai_trackorder');
        if ($block) {
            $block->setOrderIds($orderIds);
        }
    }
    
}
	
