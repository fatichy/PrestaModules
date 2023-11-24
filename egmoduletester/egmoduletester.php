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

class EgModuleTester extends Module
{

    public function __construct()
    {
        $this->name = 'egmoduletester';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'prestashop';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Eg Module Tester');
        $this->description = $this->l(' Add a description for your module');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('EG_CUSTOM_ACTIVE', '');
        Configuration::updateValue('EG_CUSTOM_TITLE', '');
        Configuration::updateValue('EG_CUSTOM_DESCRITION', '');

        return parent::install()
            && $this->registerHook('displayHome');
    }


    public function uninstall()
    {

        Configuration::deleteByName('EG_CUSTOM_ACTIVE', '');
        Configuration::deleteByName('EG_CUSTOM_TITLE', '');
        Configuration::deleteByName('EG_CUSTOM_DESCRITION', '');

        return parent::uninstall();
    }

    public function hookDisplayHome()
    {

        $active = Configuration::get('EG_CUSTOM_ACTIVE');
        $title = Configuration::get('EG_CUSTOM_TITLE');
        $Desc = Configuration::get('EG_CUSTOM_DESCRITION');

        $this->context->smarty->assign(array(
            'title' => $title,
            'Desc' => $Desc,
            'active' => $active,
        ));



        return $this->display(__FILE__, 'views/templates/hook/egmoduletester.tpl');
    }


    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitEgmoduletesterModule')) == true) {
            $form_values = $this->getConfigFormValues();

            foreach (array_keys($form_values) as $key) {
                Configuration::updateValue($key, Tools::getValue($key));
            }
        }

        return $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitEgmoduletesterModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'EG_CUSTOM_ACTIVE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-cloud"></i>',
                        'desc' => $this->l('Enter a title'),
                        'name' => 'EG_CUSTOM_TITLE',
                        'label' => $this->l('title'),
                    ),
                    array(
                        'type' => 'text',
                        'desc' => $this->l('Enter a description'),
                        'name' => 'EG_CUSTOM_DESCRITION',
                        'label' => $this->l('Description'),

                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),


                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'EG_CUSTOM_ACTIVE' => Configuration::get('EG_CUSTOM_ACTIVE', true),
            'EG_CUSTOM_TITLE' => Configuration::get('EG_CUSTOM_TITLE', true),
            'EG_CUSTOM_DESCRITION' => Configuration::get('EG_CUSTOM_DESCRITION', true),
        );
    }




    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path . '/views/js/front.js');
        $this->context->controller->addCSS($this->_path . '/views/css/front.css');
    }
}
