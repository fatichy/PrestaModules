<?php

class EgQuotecheckQuotesModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $total= 0;
        foreach ($_SESSION['quotations'] as $quotation ) {
            $total += $quotation->price;
        }
        

        $this->context->smarty->assign(array(

          "quotations" => $_SESSION['quotations'],

            "total"=>$total
        ));

        $this->setTemplate('module:egquote/views/templates/hook/form.tpl');

    }

}
