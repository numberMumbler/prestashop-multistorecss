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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
if (!defined('_CAN_LOAD_FILES_'))
	exit;

class multistorecss extends Module
{
	public function __construct()
	{
		$this->name = 'multistorecss';
		$this->tab = 'front_office_features';
		$this->version = '1.0';
		$this->ps_versions_compliancy = array('min' => '1.5');
		$this -> author = 'David Janke';

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
				Configuration::updateValue('storestyle', '') &&
				$this->registerHook('header');
	}

	public function uninstall()
	{

		$this->_clearCache('multistorecss.tpl');
		return parent::uninstall() &&
				Configuration::deleteByName('storestyle');
	}

	public function getContent()
	{
		$html = '';
		if (isset($_POST['submitModule']) &&
			isset($_POST['storestyle']) &&
			$_POST['storestyle'] != '')
		{	
			Configuration::updateValue('storestyle', $_POST['storestyle'] : '',  true);
			$html .= '<div class="confirm">'.$this->l('Configuration updated').'</div>';
		}
		return $html;
	}

	public function hookHeader($params) {
		if (!$this->isCached('multistorecss.tpl', $this->getCacheId())) {	
			global $smarty;
			$smarty->assign(array(
				'storestyle' => Configuration::get('storestyle')
			));
		}
			return $this->display(__FILE__, 'multistorecss.tpl', $this->getCacheId());
	}
}