<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class MonModule extends Module
{
    public function __construct()
    {
        $this->name = 'monmodule';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Votre Nom';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);

        parent::__construct();

        $this->displayName = $this->l('Egmodulecategories');
        $this->description = $this->l("Un module pour afficher un bloc de catégories sur la page d'accueil.");

        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ?');

        if (!Configuration::get('MONMODULE')) {
            $this->warning = $this->l('Aucun nom fourni');
        }
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('displayHome');
    }

    public function uninstall()
    {
        Configuration::deleteByName('MONMODULE');

        return parent::uninstall();
    }

    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit'.$this->name)) {
            $titre = strval(Tools::getValue('TITRE'));
            $sousTitre = strval(Tools::getValue('SOUSTITRE'));
            $url = strval(Tools::getValue('URL'));
            $image = strval(Tools::getValue('IMAGE'));
            $position = strval(Tools::getValue('POSITION'));
            $status = strval(Tools::getValue('STATUS'));

            if (!$titre || empty($titre) || !Validate::isGenericName($titre)) {
                $output .= $this->displayError($this->l('Nom invalide'));
            } else {
                Configuration::updateValue('TITRE', $titre);
                Configuration::updateValue('SOUSTITRE', $sousTitre);
                Configuration::updateValue('URL', $url);
                Configuration::updateValue('IMAGE', $image);
                Configuration::updateValue('POSITION', $position);
                Configuration::updateValue('STATUS', $status);

                $output .= $this->displayConfirmation($this->l('Paramètres mis à jour'));
            }
        }

        return $output.$this->displayForm();
    }

    public function displayForm()
    {
        // Récupérer les valeurs actuelles
        $titre = Configuration::get('TITRE');
        $sousTitre = Configuration::get('SOUSTITRE');
        $url = Configuration::get('URL');
        $image = Configuration::get('IMAGE');
        $position = Configuration::get('POSITION');
        $status = Configuration::get('STATUS');

        // Créer le formulaire
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Paramètres'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Titre'),
                    'name' => 'TITRE',
                    'value' => $titre,
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Sous-titre'),
                    'name' => 'SOUSTITRE',
                    'value' => $sousTitre,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('URL'),
                    'name' => 'URL',
                    'value' => $url,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Image'),
                    'name' => 'IMAGE',
                    'value' => $image,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Position'),
                    'name' => 'POSITION',
                    'value' => $position,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Statut'),
                    'name' => 'STATUS',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Activé')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Désactivé')
                        )
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->l('Enregistrer'),
                'class' => 'btn btn-default pull-right',
            ),
        );

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = array(
            'save' =>
            array(
                'desc' => $this->l('Enregistrer'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
            ),
            'back' => array(
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Retour à la liste'),
            ),
        );

        // Charger les valeurs actuelles dans le formulaire
        $helper->fields_value['TITRE'] = $titre;
        $helper->fields_value['SOUSTITRE'] = $sousTitre;
        $helper->  $helper->fields_value['URL'] = $url;
        $helper->fields_value['IMAGE'] = $image;
        $helper->fields_value['POSITION'] = $position;
        $helper->fields_value['STATUS'] = $status;

        return $helper->generateForm($fields_form);
    }

    public function hookDisplayHome($params)
    {
        $titre = Configuration::get('TITRE');
        $sousTitre = Configuration::get('SOUSTITRE');
        $url = Configuration::get('URL');
        $image = Configuration::get('IMAGE');
        $position = Configuration::get('POSITION');
        $status = Configuration::get('STATUS');

        if ($status) {
            $this->context->smarty->assign(array(
                'titre' => $titre,
                'sousTitre' => $sousTitre,
                'url' => $url,
                'image' => $image,
                'position' => $position,
            ));

            return $this->display(__FILE__, 'views/templates/hook/displayHome.tpl');
        }

        return '';
    }
}
