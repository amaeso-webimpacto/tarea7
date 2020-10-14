<?php
/**
* 2007-2020 PrestaShop
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
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Modulo_tarea4 extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'modulo_tarea4';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'yomisma';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Modulo tarea 4');
        $this->description = $this->l('Modulo que envia un mail con confirmación de pedido y genera cupón descuento a partir de x cantidad gastada por el cliente en la tienda.');

        $this->confirmUninstall = $this->l('');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
       

        include(dirname(__FILE__).'/sql/install.php');

        return parent::install() &&
            $this->registerHook('actionValidateOrder') &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayOrderConfirmation1');
    }

    public function uninstall()
    {
        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitModulo_tarea4Module')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
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
        $helper->submit_action = 'submitModulo_tarea4Module';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
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
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-cut"></i>',
                        'desc' => $this->l('Cantidad mínima gasto para generar cupón descuento'),
                        'name' => 'MODULO_TAREA4_MINIMO_SUMAR',
                        'label' => $this->l('Minimo'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-money"></i>',
                        'desc' => $this->l('Valor (%) del cupón descuento'),
                        'name' => 'MODULO_TAREA4_VALOR',
                        'label' => $this->l('Valor'),
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
            'MODULO_TAREA4_MINIMO_SUMAR' => Configuration::get('MODULO_TAREA4_MINIMO_SUMAR'),
            'MODULO_TAREA4_VALOR' => Configuration::get('MODULO_TAREA4_VALOR'),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }


    //hook crear cupon y mandar emails
    public function hookActionValidateOrder($params)
    {
        
        //definiendo las variables que se van a usar
        $customer_id = $params['customer']->id;

        $firstname = $params['customer']->firstname;
        $lastname = $params['customer']->lastname;
        $email = $params['customer']->email;

        //variables que vienen el input en configuracion del modulo
        $valor = Configuration::get('MODULO_TAREA4_VALOR');
        $minimo = Configuration::get('MODULO_TAREA4_MINIMO_SUMAR');

        //variables para el email
        $idLang=(int)(Configuration::get('PS_LANG_DEFAULT'));
        $shopemail=Configuration::get('PS_SHOP_EMAIL');
        $shopname=Configuration::get('PS_SHOP_NAME');

        //Calculo del total pagado
        $total_spent_by_customer = Db::getInstance()->getValue(
            sprintf(
                'SELECT SUM(total_paid) FROM ps_orders WHERE id_customer = %d',
                (int)pSQL($customer_id)
            )
        );

        

        // para cada customer: acumulado = spent - (nºde cupones x minimo)
        $num_cupones = Db::getInstance()->getValue(
            sprintf(
                'SELECT COUNT (discountcode) FROM ps_modulo_tarea4 WHERE id_customer = %d',
                (int)pSQL($customer_id)
            )
        );
            
        $total_acumulate = $total_spent_by_customer - ($minimo * $num_cupones);
        
        
        //Condicion para crear cupon y mandar un mail u otro
        if ($total_acumulate >= $minimo) {
            //CREAR CUPÓN
            $cartRuleObj = new CartRule(); $cartRuleObj = new CartRule();
            $discountcode = Tools::passwdGen();
            $cartRuleObj->date_from =date('Y-m-d H:i:s');
            $cartRuleObj->date_to =date('Y-m-d H:i:s', strtotime('+1 year'));
            $cartRuleObj->name[Configuration::get('PS_LANG_DEFAULT')] = 'Descuento';
            $cartRuleObj->quantity = 1;
            $cartRuleObj->quantity_per_user = 1;
            $cartRuleObj->free_shipping = false;
            $cartRuleObj->active = true;
            $cartRuleObj->id_customer = $this->context->customer->id;
            while (CartRule::cartRuleExists($discountcode)) { //asegurarse de que no hay duplicados al hacer el cupon
                $discountcode = Tools::passwdGen();
            }
            $cartRuleObj->add();
            
            //GUARDA y ACTUALIZA CUPON en la tabla auxiliar
            $insert = array(
                'customer_id' => $customer_id,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'total_spent' => $total_spent_by_customer,
                'total_acumulate' => $total_acumulate,
                'discount_code' => $discountcode,
                'discount_valor' => $valor,
            );

           
            Db::getInstance()->insert('modulo_tarea4', $insert);
            
        

            //ENVIA MAIL CONFIRMACIÓN + CUPON DESCUENTO
            Mail::send(
                $idLang,  //default language id
                'confir_y_cupon', //email template file to be use
                'order confirmation and discount code',  //email subject
                $templateVars=array( //email vars
                    '{firstname}'=>$firstname,
                    '{lastname}'=>$lastname,
                    '{email}'=>$email,
                    '{total gastado}'=>$total_spent_by_customer,
                    '{valor}'=>$valor,
                    '{codigo descuento}'=>$discountcode,
                ),
                $this->context->customer->email, //receiver email adress
                null, //receiver name
                $shopemail, //from email address
                $shopname, //from name
                null,
                true, //mode smtp
                _PS_ROOT_DIR_.'modules/modulo_tarea4/mails', //custom template
                false, //die
                null, //shop id
                null,
                null
            );
        } else {
            //ENVIA MAIL CONFIRMACIÓN (sin cupón descuento)
            Mail::send(
                $idLang, //default language id
                'confir', //email template file to be use
                'Confirmación pedido', //email subject
                $templateVars=array( //email vars
                    '{firstname}'=>$firstname,
                    '{lastname}'=>$lastname,
                    '{email}'=>$email,
                    '{total gastado}'=>$total_spent_by_customer,
                ),
                $this->context->customer->email, //receiver email adress
                null, //receiver name
                $shopemail, //from email address
                $shopname, //from name
                null,
                true, //mode smtp
                _PS_ROOT_DIR_.'modules/modulo_tarea4/mails', //custom template
                false, //die
                null, //shop id
                null,
                null
            );
        }
    }

    //hook para enlazar hoja estilos y javascript
    public function hookDisplayHeader($params)
    {
        $this->context->controller->addCSS($this->_path . 'views/css/front.css');
        $this->context->controller->addCSS($this->_path . 'views/css/rascaygana.css');
        $this->context->controller->addJS($this->_path . 'views/js/front.js');
        

    }
    
    //hook para mostrar rasca y gana en 'Order confirmation'
    public function hookDisplayOrderConfirmation1($params)
    {

        //definicion variables
        $customer_id = $params['cart']->id_customer;
        $discountcode = Db::getInstance()->getValue(
            sprintf(
                'SELECT discount_code FROM ps_modulo_tarea4 WHERE customer_id = %d',
                (int)pSQL($customer_id)
            )
        );
        $valor = Db::getInstance()->getValue(
            sprintf(
                'SELECT discount_valor FROM ps_modulo_tarea4 WHERE customer_id = %d',
                (int)pSQL($customer_id)
            )
        );
        
        //asignar variable a smarty
        $this->context->smarty->assign('discountcode_smarty', $discountcode);
        $this->context->smarty->assign('valor_smarty', $valor);
        return $this->display(__FILE__, 'views/templates/hook/rascaygana.tpl');
    }
    
}
