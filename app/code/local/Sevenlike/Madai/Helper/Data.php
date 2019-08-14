<?php 

class Sevenlike_Madai_Helper_Data extends Mage_Core_Helper_Data{
	
	protected $_cartProductIds;
	
	public function __construct() {
		$product_ids = array();
		$products = Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
		foreach ($products as $_products){
			$product_ids [$_products->getProductId()] = $_products->getQty();
		}
		$this->_cartProductIds = $product_ids;
	}
	
	/* Controllo se un singolo ID prodotto è nel carrello */
	public function isInCart($product) {
		return array_key_exists($product->getId(), $this->_cartProductIds);
	}
	
	/* Torna la quantita di un prodotto presente nel carrello */
	public function getQty($product) {
		return  $this->_cartProductIds [$product->getId()];
	}
	
	
	/* Dati di connessione per il singolo store */
	public function getSignerId(){
		if (Mage::getStoreConfig('madai/madai/testmode')=="1")
			return "30e77ae5-5803-472b-8f19-403f407aa440";
		else 
			return Mage::getStoreConfig('madai/madai/signerid');
	}
	
	public function getSignatureKey(){
		if (Mage::getStoreConfig('madai/madai/testmode')=="1")
			return "4707a110ad567f65f7771693c51822bf80949c803608b1550f8cbca908163c845ca6e607744c547e8f7fa586f1ab1806cc0853e33f1953d1cb9d40828f3a56c2";
		else
			return Mage::getStoreConfig('madai/madai/signaturekey');
	}
	
	public function getFormHeight(){
		return "500";
	}
	
	public function getIsCatalogHidden(){
		return "true";
	}
	
	public function getMadaiUrl(){
		if (Mage::getStoreConfig('madai/madai/testmode')=="1")
			return "https://test.madai.com";
		else
			return "https://www.madai.com";
	}
	
	public function getMadaiModaJsUrl(){
		if (Mage::getStoreConfig('madai/madai/testmode')=="1")
			return "https://test.madai.com/wa/static/modal/modal.js";
		else
			return "http://cdn-madai-com.s3.amazonaws.com/pub/modal.js";
	}
	
	
	/* Formattazione della data in ISO8601 */
	public function getActualIsoTime(){
		//@todo gestione spartana..
		$the_date = strtotime(date('Y-m-d H:i:s'));
		date_default_timezone_get();
		date("Y-d-mTG:i:sz",$the_date);
		date_default_timezone_set("UTC");
		$timestamp = date("Y-m-d", $the_date);
		$timestamp.="T".date("H:i", $the_date)."Z";
		return $timestamp;
	}
	
	public function hextobin($hexstr)
	{
		$n = strlen($hexstr);
		$sbin="";
		$i=0;
		while($i<$n)
		{
			$a =substr($hexstr,$i,2);
			$c = pack("H*",$a);
			if ($i==0){$sbin=$c;}
			else {$sbin.=$c;}
			$i+=2;
		}
		return $sbin;
	}
	
	public function getCloseTransactionParams($order_id){
		$signerId = Mage::helper('madai')->getSignerId(); 
		$signerKey = Mage::helper('madai')->getSignatureKey();
		$timestamp = Mage::helper('madai')->getActualIsoTime();
		$message = "{orderId:\"{$order_id}\"}";
		$hmac = hash_init("sha512", HASH_HMAC, Mage::helper('madai')->hextobin($signerKey));
		hash_update($hmac, utf8_decode($signerId));
		hash_update($hmac, utf8_decode($timestamp));
		hash_update($hmac, utf8_decode($message));
		$hmac = hash_final($hmac, true);
		$hmac = bin2hex($hmac);
		$params = array(
				"message" => $message,
				"signerId" => $signerId,
				"timestamp" => $timestamp,
				"signature" => $hmac,
		);
		return $params;	
	}
}