<?php
class Sevenlike_Madai_Block_Trackorder extends Mage_Core_Block_Text
{
	protected function _getOrdersTrackingCode()
	{
		$result=null;
		$orderIds = $this->getOrderIds();
		if (empty($orderIds) || !is_array($orderIds)) {
			return;
		}
		$collection = Mage::getResourceModel('sales/order_collection')->addFieldToFilter('entity_id', array('in' => $orderIds));
		foreach ($collection as $order) {
   			$order_id=Mage::getSingleton('checkout/session')->getQuote()->getMadaiOrderId();
   			$params = Mage::helper('madai')->getCloseTransactionParams($order->getIncrementId());
   			$url= Mage::helper('madai')->getMadaiUrl().'/wa/ui/magento/orders/'.$order_id.'/payment';
   			$fields_string='';
   			//url-ify the data for the POST
   			foreach($params as $key=>$value) {
   				//FIXME gestirlo meglio..
   				if ($key!="signature")
   					$fields_string .= $key.'='.$value.'&';
   				else
   					//	$fields_string .= $key.'='.rawurlencode($value).'&';
   					$fields_string .= $key.'='.urlencode($value).'&';
   			}
   			rtrim($fields_string, '&');
   			$fields_string=substr($fields_string,0,-1);
   			
   			$ch = curl_init();
   			curl_setopt($ch,CURLOPT_URL, $url);
   			curl_setopt($ch,CURLOPT_POST, count($params));
   			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
   			//execute post
   			$result = curl_exec($ch);
   			Mage::log($result);
   			//echo "<hr><hr>";
   			//var_dump($result);
   			curl_close($ch);
		}
		return true;
	}

	protected function _toHtml()
	{
		return $this->_getOrdersTrackingCode();
	}
	
}
