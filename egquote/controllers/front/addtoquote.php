<?php

class EgQuoteAddToQuoteModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
       
    if(empty($_SESSION['quotations'])) {
        $_SESSION['quotations']= [];
    }

        $this->setTemplate('module:egquote/views/templates/hook/liste.tpl');

    }

    public function displayAjaxAddQuote()
    {

        $id_product = Tools::getValue("product_id");
        
        $product = new Product($id_product);

        if (array_push($_SESSION['quotations'], $product)){
            $response=[
                'success'=>true,
                'product'=>$product,
            ];

           
        }

        header('Content-Type: application/json');
        die(json_encode($response));
    }

   
}