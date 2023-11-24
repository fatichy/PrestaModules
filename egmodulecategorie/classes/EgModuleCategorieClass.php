<?php
class EgModuleCategorieClass extends ObjectModel
{
    /** @var int EdBannerID */
    public $id_eg_module_categorie;

 	/** @var string title Manufacture */
	public $title;

    /** @var  int sport position */
    public $position;

    /** @var  string Long description Manufacture*/
    public $subtitle;

    /** @var string image  */
    public $image;

    /** @var string link image */
    public $url;

    /** @var bool Status for display Banner*/
    public $status = true;


	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'eg_module_categorie',
		'primary' => 'id_eg_module_categorie',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => array(

            'position' =>           array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'status' =>             array('type' => self::TYPE_BOOL),
            'image' =>              array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'url' =>               array('type' => self::TYPE_STRING,'validate' => 'isGenericName'),

            /* Lang fields Banner*/
            'title' =>              array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isGenericName'),
            'subtitle' =>        array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml'),
        ),
	);

    /**
     * Adds current sport as a new Object to the database
     *
     * @param bool $autoDate    Automatically set `date_upd` and `date_add` columns
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the Banner has been successfully added
     * @throws
     * @throws
     */
    public function add($autoDate = true, $nullValues = false)
    {
        $this->position = (int) $this->getMaxPosition() + 1;
        return parent::add($autoDate, $nullValues);
    }

    /**
     * @return int MAX Position Banner
     */
    public static function getMaxPosition()
    {
        $query = new DbQuery();
        $query->select('MAX(position)');
        $query->from('eg_module_categorie', 'eg');

        $response = Db::getInstance()->getRow($query);

        if ($response['MAX(position)'] == null){
            return -1;
        }
        return $response['MAX(position)'];
    }

    /**
     * @param $way int
     * @param $position int Position Banner
     * @return bool
     * @throws
     */
    public function updatePosition($way, $position)
    {
        $query = new DbQuery();
        $query->select('eg.`id_eg_module_categorie`, eg.`position`');
        $query->from('eg_module_categorie', 'eg');
        $query->orderBy('eg.`position` ASC');
        $tabs = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

        if (!$tabs ) {
            return false;
        }

        foreach ($tabs as $tab) {
            if ((int) $tab['id_eg_module_categorie'] == (int) $this->id) {
                $moved_tab = $tab;
            }
        }

        if (!isset($moved_tab) || !isset($position)) {
            return false;
        }

        // < and > statements rather than BETWEEN operator
        // since BETWEEN is treated differently according to databases
        return (Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'eg_module_categorie`
            SET `position`= `position` '.($way ? '- 1' : '+ 1').'
            WHERE `position`
            '.($way
                    ? '> '.(int)$moved_tab['position'].' AND `position` <= '.(int)$position
                    : '< '.(int)$moved_tab['position'].' AND `position` >= '.(int)$position
                ))
            && Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'eg_module_categorie`
            SET `position` = '.(int)$position.'
            WHERE `id_eg_module_categorie` = '.(int)$moved_tab['id_eg_module_categorie']));
    }

    /**
     * @param $value string image Banner
     * @return string src
     */
    public static function showImage($value)
    {
        $src = __PS_BASE_URI__. 'modules/egmodulecategorie/views/img/'.$value;
        return $value ? '<img src="'.$src.'" width="80" height="40px" class="img img-thumbnail"/>' : '-';
    }


    /**
     * @param $limit int
     * @return array list banner by hook
     * @throws
     */
    public static function getCategories($limit = null)
    {
        $idLang = Context::getContext()->language->id;

        $query = new DbQuery();
        $query->select('eg.*, egl.*');
        $query->from('eg_module_categorie', 'eg');
        $query->leftJoin('eg_module_categorie_lang', 'egl', 'eg.`id_eg_module_categorie` = egl.`id_eg_module_categorie`'.Shop::addSqlRestrictionOnLang('egl'));
        $query->where('eg.`status` =  1 AND egl.`id_lang` =  '.(int) $idLang);
        if ($limit) {
            $query->limit((int) $limit);
        }
        $query->orderBy('eg.`position` ASC');

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
    }

   
    

   

    public static function updateEgBannerImag($champ, $imgValue)
    {
        $res = Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'eg_module_categorie` SET '.$champ.' = Null  WHERE '.$champ.' = "'.$imgValue.'"');
        if ($res && file_exists(__PS_BASE_URI__. 'modules/egmodulecategorie/views/img/'.$imgValue)) {
            @unlink(__PS_BASE_URI__. 'modules/egmodulecategorie/views/img/'.$imgValue);
        }
    }
}
