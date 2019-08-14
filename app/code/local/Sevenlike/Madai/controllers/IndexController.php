<?php 

class Sevenlike_Madai_IndexController extends Mage_Core_Controller_Front_Action {
	
	/*
	 * Azione che intercetta i parametri in ritorno
	 * dalla pagina della scheda prodotto di Madai,
	 * Dopo che l'utente ha ridotto il prezzo verso questa 
	 * rotta vengono passati i paramentri come da specifiche
	 */
    public function returnAction()
    {
		Mage::log($this->getRequest()->getParams(),"madai.log");
     	if  (($this->getRequest()->getParam('signerId')) && ($this->getRequest()->getParam('signerId'))){
    		//raccolgo tutti i paramentri passati dalla piattaforma
    		$submit_x=$this->getRequest()->getParam('submit_x');
    		$submit_y=$this->getRequest()->getParam('submit_y');
    		$signerId=$this->getRequest()->getParam('signerId');
    		$timestamp=$this->getRequest()->getParam('timestamp');
    		$order=urldecode($this->getRequest()->getParam('order'));
    		$signature=$this->getRequest()->getParam('signature');
    		$json = json_decode($order);
    		$madai_price =$json->price->amount;
    		$madai_id =$json->productId;
    		$order_id = $json->orderId;
    		try {
    			//Faccio un Load 2 volte! Con LoadByAttribute non ho infatti i dati di stock.
    			$product = Mage::getModel('catalog/product')->loadByAttribute("madai_id",$madai_id);
    			$product= Mage::getModel('catalog/product')->load($product->getEntityId());
    			//Setto i dati sulla sessione cosi' da riprenderli nell'observer e settarli sul singolo item
    			Mage::getSingleton('checkout/session')->getQuote()->setMadaiDiscount($product->getFinalPrice()-$madai_price);
    			Mage::getSingleton('checkout/session')->getQuote()->setMadaiOrderId($order_id);
    			//aggiungo il prodotto al carrello
    			$cart = Mage::getSingleton('checkout/cart');
    			$cart->addProduct($product, array('qty' => 1));
    			$cart->save();
    		}
    		catch (Exception $ex) {
    			echo $ex->getMessage();
    		}
    		$this->_redirectUrl("/checkout/cart");
     	}
     	else{
     		  Mage::getSingleton('customer/session')->addError('There was an error processing your request');
     	}
	}
}