<?php
/*
* 2007-2014 PrestaShop
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
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license	http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
if (!defined('_CAN_LOAD_FILES_'))
	exit;

class multistorecss extends Module
{
	private static $cssKey = 'storestyle';

	public function __construct()
	{
		$this->name = 'multistorecss';
		$this->tab = 'front_office_features';
		$this->version = '1.0';
		$this->ps_versions_compliancy = array('min' => '1.5');
		$this->author = 'David Janke';

		parent::__construct();

		$this->displayName = $this->l('Multistore CSS Module');
		$this->description = $this->l('Define shop-specific CSS rules');
	}

	public function install()
	{
		$this->_clearCache('multistorecss.tpl');
		if (Shop::isFeatureActive()) {
  			Shop::setContext(Shop::CONTEXT_ALL);
		}
		return parent::install() &&
				Configuration::updateValue(multistorecss::$cssKey, '') &&
				$this->registerHook('header');
	}

	public function uninstall()
	{

		$this->_clearCache('multistorecss.tpl');
		return parent::uninstall() &&
				Configuration::deleteByName(multistorecss::$cssKey);
	}

	public function getSubmitId() {
		return 'submit' . $this->name;
	}

	public function getContent()
	{
		$output = '';
		if(Tools::isSubmit($this->getSubmitId())) {
			$storeStyle = strval(Tools::getValue(multistorecss::$cssKey));
			if($storeStyle) {
				Configuration::updateValue(multistorecss::$cssKey, $storeStyle);
				$output = $this->displayConfirmation($this->l('CSS updated'));
			} else {
				// TODO: What would cause this? How should the users respond?
				$output = $this->displayError($this->l('Could not access the CSS rules. Restart your browser and try again.'));
			}
		}
		return $output . $this->displayForm();
	}

	public function displayForm() {
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Settings'),
			),
			'message' => array(
				'type' => 'div',
				'class' => 'multishop_info',
				'html' => 'Changes apply to shop '.$this->context->shop->name
			),
			'input' => array(
				array(
					'type' => 'textarea',
					'label' => $this->l('CSS'),
					'name' => multistorecss::$cssKey,
					'cols' => 100,
					'rows' => 20,
					'required' => true
				)
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'button'
			)
		);

		$helper = new HelperForm();
		$helper->module = $this;
		$helper->name_controller = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;
		$helper->title = $this->displayName;
		$helper->show_toolbar = true;
		$helper->toolbar_scroll = true;
		$helper->submit_action = $this->getSubmitId();
		$helper->toolbar_btn = array(
			'save' => array(
						'desc' => $this->l('Save'),
						'href' => AdminController::$currentIndex .
									'&configure=' . $this->name .
									'&save=' . $this->name .
									'&token=' . Tools::getAdminTokenLite('AdminModules')),
			'back' => array(
						'desc' => $this->l('Back to list'),
						'href' => AdminController::$currentIndex .
									'&token=' . Tools::getAdminTokenLite('AdminModules'))
		);
		$helper->fields_value[multistorecss::$cssKey] = Configuration::get(multistorecss::$cssKey);

		return $this->displayShopInfo() . $helper->generateForm($fields_form);
	}

	public function hookHeader($params) {
		if (!$this->isCached('multistorecss.tpl', $this->getCacheId())) {	
			global $smarty;
			$smarty->assign(array(
				multistorecss::$cssKey => Configuration::get(multistorecss::$cssKey)
			));
		}
			return $this->display(__FILE__, 'multistorecss.tpl', $this->getCacheId());
	}

	public function displayShopInfo() {
		$output = '';
		if (Shop::getContext() != Shop::CONTEXT_SHOP) {
			$output = $this->displayWarning($this->l('CSS can only be applied to specific shops. Please select one above'));
		}
		return $output;
	}

	public function displayInfo($string) {
	   $output = '
	   <div class="info">
	      '.$string.'
	   </div>';
	   return $output;
	}

	public function displayWarning($string) {
	   $output = '
	   <div class="warn">
	      '.$string.'
	   </div>';
	   return $output;
	}
}