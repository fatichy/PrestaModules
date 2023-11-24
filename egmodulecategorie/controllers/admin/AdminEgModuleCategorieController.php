
<?php


/**
 * @property EgModuleCategorieClass $object
 */
class AdminEgModuleCategorieController extends ModuleAdminController
{
    protected $position_identifier = 'id_eg_module_categorie';
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'eg_module_categorie';
        $this->className = 'EgModuleCategorieClass';
        $this->identifier = 'id_eg_module_categorie';
        $this->_defaultOrderBy = 'position';
        $this->_defaultOrderWay = 'ASC';
        $this->toolbar_btn = null;
        $this->list_no_link = true;
        $this->lang = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        Shop::addTableAssociation($this->table, array('type' => 'shop'));

        parent::__construct();

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        $this->fields_list = array(
            'id_eg_module_categorie' => array(
                'title' => $this->l('Id')
            ),
            'image' => array(
                'title' => $this->l('Image'),
                'type' => 'text',
                'callback' => 'showImage',
                'callback_object' => 'EgModuleCategorieClass',
                'class' => 'fixed-width-xxl',
                'search' => false,
            ),
            'title' => array(
                'title' => $this->l('Title'),
                'filter_key' => 'b!title',
            ),
             'subtitle' => array(
                'title' => $this->l('subTitle'),
                'filter_key' => 'b!subtitle',
            ),
            'status' => array(
                'title' => $this->l('status'),
                'align' => 'center',
                'active' => 'status',
                'class' => 'fixed-width-sm',
                'type' => 'bool',
                'orderby' => false
            ),
            'position' => array(
                'title' => $this->l('Position'),
                'filter_key' => 'a!position',
                'position' => 'position',
                'align' => 'center',
                'class' => 'fixed-width-md',
            ),

        );
    }

    /**
     * @param $description
     * @return string Content without html
     */
    public static function getDescriptionClean($description)
    {
        return Tools::getDescriptionClean($description);
    }

    /**
     * AdminController::init() override
     * @see AdminController::init()
     */
    public function init()
    {
        parent::init();

        if (Shop::getContext() == Shop::CONTEXT_SHOP && Shop::isFeatureActive()) {
            $this->_where = ' AND b.`id_shop` = '.(int)Context::getContext()->shop->id;
        }
    }

    /**
     * @see AdminController::initPageHeaderToolbar()
     */
    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_banner'] = array(
                'href' => self::$currentIndex.'&addeg_banner&token='.$this->token,
                'desc' => $this->l('Add new banner'),
                'icon' => 'process-icon-new'
            );
        }
        parent::initPageHeaderToolbar();
    }

    /**
     * @param $item
     * @return array
     */
    protected function stUploadImage($item)
    {
        $result = array(
            'error' => array(),
            'image' => '',
        );
        $types = array('gif', 'jpg', 'jpeg', 'jpe', 'png', 'svg');
        if (isset($_FILES[$item]) && isset($_FILES[$item]['tmp_name']) && !empty($_FILES[$item]['tmp_name'])) {
            $name = str_replace(strrchr($_FILES[$item]['name'], '.'), '', $_FILES[$item]['name']);

            $imageSize = @getimagesize($_FILES[$item]['tmp_name']);
            if (!empty($imageSize) &&
                ImageManager::isCorrectImageFileExt($_FILES[$item]['name'], $types)) {
                $imageName = explode('.', $_FILES[$item]['name']);
                $imageExt = $imageName[1];
                $tempName = tempnam(_PS_TMP_IMG_DIR_, 'PS');
                $coverImageName = $name .'-'.rand(0, 1000).'.'.$imageExt;
                if ($upload_error = ImageManager::validateUpload($_FILES[$item])) {
                    $result['error'][] = $upload_error;
                } elseif (!$tempName || !move_uploaded_file($_FILES[$item]['tmp_name'], $tempName)) {
                    $result['error'][] = $this->l('An error occurred during move image.');
                } else {
                    $destinationFile = _PS_MODULE_DIR_ . $this->module->name.'/views/img/'.$coverImageName;
                    if (!ImageManager::resize($tempName, $destinationFile, null, null, $imageExt)){
                        $result['error'][] = $this->l('An error occurred during the image upload.');
                    }
                }
                if (isset($tempName)) {
                    @unlink($tempName);
                }

                if (!count($result['error'])) {
                    $result['image'] = $coverImageName;
                    $result['width'] = $imageSize[0];
                    $result['height'] = $imageSize[1];
                }
                return $result;
            }
        } else {
            return $result;
        }
    }

    /**
     * AdminController::postProcess() override
     * @see AdminController::postProcess()
     */
    public function postProcess()
    {
        // Upload FILES EG Banner
        if ($this->action && $this->action == 'save') {
            $image = $this->stUploadImage('image');
            if (isset($image['image']) && !empty($image['image'] )) {
                $_POST['image']= $image['image'];
            }
        }
        // Delete Images EG Banner
        if (Tools::isSubmit('forcedeleteImage') || Tools::getValue('deleteImage')) {
            $champ = Tools::getValue('champ');
            $imgValue = Tools::getValue('image');
            EgBannerClass::updateEgBannerImag($champ, $imgValue);
            if (Tools::isSubmit('forcedeleteImage')) {
                Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminEgBanner'));
            }
        }

        return parent::postProcess();
    }

    /**
     * @see AdminController::initProcess()
     */
    public function initProcess()
    {
        $this->context->smarty->assign(array(
            'uri' => $this->module->getPathUri()
        ));
        parent::initProcess();
    }


    public function renderForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        if ($this->display == 'edit') {
            $idSelected = (int) EgBannerClass::getCategorySelectedById((int) $obj->id);
        } else {
            $idSelected = 0;
        }

        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Page'),
                'icon' => 'icon-folder-close'
            ),
            // custom template
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Title:'),
                    'name' => 'title',
                    'lang' => true,
                    'desc' => $this->l('Please enter a title for the category.'),
                ), 
                array(
                    'type' => 'text',
                    'label' => $this->l('SubTitle:'),
                    'name' => 'subtitle',
                    'lang' => true,
                    'desc' => $this->l('Please enter a Subtitle for the category.'),
                ),
                array(
                    'type' => 'file',
                    'label' => $this->l('Image :'),
                    'name' => 'image',
                    'delete_url' => self::$currentIndex.'&'.$this->identifier .'='.$obj->id.'&token='.$this->token.'&champ=image&deleteImage=1',
                    'desc' => $this->l('Upload an image for your top category.')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('url:'),
                    'name' => 'url',
                    'desc' => $this->l('Please enter a Url for the category.'),
                ), 
                array(
                    'type' => 'switch',
                    'label' => $this->l('Display'),
                    'name' => 'status',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                ),
            ),
             'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );


        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            );
        }

        return parent::renderForm();
    }

    /**
     * Update Positions 
     */
    public function ajaxProcessUpdatePositions()
    {
        $way = (int)(Tools::getValue('way'));
        $idegcategorie = (int)(Tools::getValue('id'));
        $positions = Tools::getValue($this->table);

        foreach ($positions as $position => $value){
            $pos = explode('_', $value);

            if (isset($pos[2]) && (int)$pos[2] === $idegcategorie){
                if ($banner = new EgModuleCategorieClass((int)$pos[2])){
                    if (isset($position) && $banner->updatePosition($way, $position)){
                        echo 'ok position '.(int)$position.' for tab '.(int)$pos[1].'\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update tab '.(int)$idegcategorie.' to position '.(int)$position.' "}';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "This tab ('.(int)$idegcategorie.') can t be loaded"}';
                }

                break;
            }
        }
    }
}
