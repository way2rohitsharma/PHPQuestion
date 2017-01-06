<?php

// Report simple running errors
error_reporting(E_ERROR | E_WARNING | E_PARSE);

$products	=	@$_REQUEST['products'];
$products	=	strtoupper($products);


// if products string is not blank..
if(!empty($products))
{
	
	$store	=	new store();
	$store->setPricing(array('A','B','C','D'),array('2','12','1.25','0.15'));	// Parameters: products, prices
	$store->setbulkpricing(array('A','C'),array('7','6'),array('4','6'));		// Parameters: products, prices, volumes

			
	$store->scan($products);
	
	echo $store->currency.$store->total;
	
}





// Defining class store.
class store{
		
	private $products		=	array();
	private $bulk_products	=	array();
	private $bulk_prices	=	array();
	private $bulk_volumes	=	array();	
	
	
	public $currency	=	'';
	private $error		=	'';
	private $seperator	=	"";
	
	
	public $total		=	0;
	

	// constructor..
   function __construct(){
	   
	   // Initilize variables that are required after object creation..
	   $this->currency	=	'$';
	   $this->seperator =	"\r\n";
	   
   }

   
	// function to set price of any product..
	// parameters: product name, product price..
	// return: will return true if successful or false in case it generates any errors..
	public function setPricing($products=array(), $prices = array()){
		
		
		// if any of the parameter is blank..then return false..
		if(is_array($products) && is_array($prices) && sizeof($products) == sizeof($prices)) 
		{
						
			// set price of each product..
			foreach($products as $index=>$product_name)
			{
				$this->products[$product_name]	=	$prices[$index];
			}
			
		
			return true;
		}
		else
		{	
			$this->error	=	'Please check products and price paramters.'.$this->seperator;
			return false;
		}		
		
	}


	// function to set bulk pricing of products..
	public function setbulkpricing($products=array(), $prices=array(), $volumes=array()){
			
			
		// if any of the parameter is blank..then return false..
		if(is_array($products) && is_array($prices) && is_array($volumes) && sizeof($products) == sizeof($prices) && sizeof($prices) == sizeof($volumes))
		{
			// set price of each product..
			foreach($products as $index=>$product_name)
			{
				$this->bulk_products[]				=	$product_name;
				$this->bulk_prices[$product_name]	=	$prices[$index];
				$this->bulk_volumes[$product_name]	=	$volumes[$index];				
			}
			
			return true;
		}
		else
		{	
			$this->error	=	'Please check products and price paramters.'.$this->seperator;
			return false;
		}
		
			
	}
	
	
	// function to get price of any product..
	// parameters: product name,
	// return: will return true if successfull or false in case it generates any errors..	
	public function scan($products=''){
	
	
		// if product name is blank..then return false and generate error..
		if(!empty($products))
		{
			
			$products_array	=	str_split($products);
			
			$products_counts_array	=	array();
			
			
			// set product count array..
			foreach($products_array as $product)
			{
				// if product name is set..
				$products_counts_array[$product]	+=	1;
			}
		
		
			
			foreach($products_counts_array as $product=>$product_count)
			{	
			
				// if product count is greater then one..
				if($product_count>1)
				{
					// check to see if product exist in bulk product listing..
					if(array_search($product,$this->bulk_products) !== false)
					{
						$quotient	=	floor($product_count/$this->bulk_volumes[$product]);
						$remainder	=	$product_count%$this->bulk_volumes[$product];
						$this->total +=	($quotient*$this->bulk_prices[$product])+($remainder*$this->products[$product]);
					}
					else
					{
						$this->total +=	$product_count*$this->products[$product];
					}
					
					
				}
				else
				{	
					
					// check to see if product exist..
					if(isset($this->products[$product]))
					{
						$this->total += $this->products[$product];
					}
					else
					{
						$this->error	.=	"Product ".$product." doesn't exist.".$this->seperator;
					}	
					
									
				}

				
			}

			
		}
		else
		{
			$this->error	=	"Please specify product name.".$this->seperator;
			return false;
		}
		
	}	
	
	
	// function to return/print error if any..
	public function get_error($print_error = false){
		
		// print error..
		if($print_error == true)
			echo $print_error;
		else
			return $this->error;
	
			
	}
	
	
}  
  
