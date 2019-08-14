<?php 

class Sevenlike_Madai_Block_Product_View extends Mage_Core_Block_Template {
	
	public function getMadaiId(){
		$current_product = Mage::registry('current_product');
		return $current_product->getMadaiId();
	}
}