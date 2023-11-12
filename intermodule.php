<?php

if(!defined('_PS_VERSION_')){
    exit;
}

class InterModule extends Module {


    public function __construct() {

        $this->name = 'intermodule';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Doryan Fourrichon';
        $this->ps_versions_compliancy = [
            'min' => '1.6',
            'max' => _PS_VERSION_
        ];

        // on récupère le fonction du constructeur de la méthode __construct de module
        parent::__construct();
        $this->bootstrap = true;
        $this->displayName = $this->l('InterModule');
        $this->description = $this->l('My international module');
        $this->confirmUninstall = $this->l('Do you want to delete this module');
    }


    public function install()
    {
        if(!parent::install() ||
            !Configuration::updateValue('KEYMY','')
        ){
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        if(!parent::uninstall() ||
        !Configuration::deleteByName('KEYMY'))
        {
            return false;
        }
        return true;
    }

    public function getContent()
    {
        return $this->postProcess().$this->renderForm();
    }

    public function renderForm()
    {
        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $this->l('Settings')
            ],
            'input' => [
                [
                    'type' => 'text',
                    'name' => 'KEYMY',
                    'label' => $this->l('my key'),
                    'required' => true,
              /* lang */      'lang' => true
                ]
            ],
            'submit' => [
                'name' => 'saving',
                'class' => 'btn btn-warning',
                'title' => $this->l('valider')
            ]
        ];


    /* lang */    $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper = new HelperForm();
        /* lang */    $default_lang = Configuration::get('PS_LANG_DEFAULT');
        /* lang */   $helper->default_form_language = $lang->id;
        $helper->module  = $this;
        $helper->name_controller = $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        /* lang */   $helper->allow_employee_form_lang = Configuration::get('PS_LANG_DEFAULT');

        /* lang */   foreach(language::getLanguages(false) as $lang) {
            // dump($lang);
            $helper->languages[] = [
              'id_lang' => $lang['id_lang'],
              'iso_code' => $lang['iso_code'],
              'name' => $lang['name'],
              'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
            ];
        /* lang */        $key = 'KEYMY_'.$lang['id_lang'];
        /* lang */         $helper->fields_value['KEYMY'][$lang['id_lang']] = Configuration::get($key);
        }

        return $helper->generateForm($fieldsForm);

    }


    public function postProcess()
    {
        if(Tools::isSubmit('saving')){

            foreach(Language::getLanguages(false) as $lang) {
                /* lang */       Configuration::updateValue('KEYMY_'.$lang['id_lang'], Tools::getValue('KEYMY_'.$lang['id_lang']), true);
            }
        }
    }
}
