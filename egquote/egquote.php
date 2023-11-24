<?php
/**
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}
session_start();
class Egquote extends Module
{
    public function __construct()
    {
        $this->name = 'egquote';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'test';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Eg Quote');
        $this->description = $this->l(' display in the back-office');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        Configuration::updateValue('EGQUOTE_LIVE_MODE', false);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook ('displayProductListReviews') &&
            $this->registerHook('displayProductActions') &&
            $this->registerHook('displayNavFullWidth') ;
        }

    public function uninstall()
    {
        Configuration::deleteByName('EGQUOTE_LIVE_MODE');

        return parent::uninstall();
    }

    public function hookDisplayProductListReviews($params){

        $product=$params['product'];

        $this->context->smarty->assign(array(

          "ProductId" => $product['id_product']
        ));
        return $this->display(__FILE__, 'views/templates/hook/button.tpl');

    }
 
    public function hookDisplayProductActions($params){

        $product=$params['product'];

        $this->context->smarty->assign(array(

          "ProductId" => $product['id_product']
        ));

        return $this->display(__FILE__, 'views/templates/hook/button.tpl');

    }
    
    public function hookDisplayNavFullWidth(){

        $this->context->smarty->assign([
            'quotations'=>$_SESSION['quotations'],
            'link' => $this->context->link->getModuleLink($this->name, 'checkQuotes')
           
         
        ]);

        return $this->display(__FILE__, 'views/templates/hook/liste.tpl');

    }

    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
        Media::addJsDef(
            [
                'frontlink' => $this->context->link->getModuleLink($this->name, 'addtoquote')
            ]
        );
    }
}
