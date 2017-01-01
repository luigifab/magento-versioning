<?php
/**
 * Created S/03/12/2011
 * Updated M/08/11/2016
 *
 * Copyright 2011-2017 | Fabrice Creuzot (luigifab) <code~luigifab~info>
 * https://redmine.luigifab.info/projects/magento/wiki/versioning
 *
 * This program is free software, you can redistribute it or modify
 * it under the terms of the GNU General Public License (GPL) as published
 * by the free software foundation, either version 2 of the license, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but without any warranty, without even the implied warranty of
 * merchantability or fitness for a particular purpose. See the
 * GNU General Public License (GPL) for more details.
 */

class Luigifab_Versioning_Block_Adminhtml_Repository extends Mage_Adminhtml_Block_Widget_Grid_Container {

	public function __construct() {

		parent::__construct();

		$this->_controller = 'adminhtml_repository';
		$this->_blockGroup = 'versioning';
		$this->_headerText = (!is_null($branch = Mage::registry('versioning')->getCurrentBranch())) ?
			$this->__('Commits history (<span id="scmtype">%s</span>, %s)', Mage::getStoreConfig('versioning/scm/type'), $branch) :
			$this->__('Commits history (<span id="scmtype">%s</span>)', Mage::getStoreConfig('versioning/scm/type'));

		$this->_removeButton('add');

		$this->_addButton('diff', array(
			'label'   => $this->__('Show diff'),
			'onclick' => "versioning.goDiff('".$this->getUrl('*/*/status', array('from' => 'abc', 'to' => 'abc'))."');"
		));

		if (is_file($this->helper('versioning')->getMaintenanceFlag())) {
			$this->_addButton('maintenance_flag', array(
				'label'   => $this->__('Remove the maintenance page'),
				'onclick' => "versioning.cancelFlag(this, '".$this->getUrl('*/*/delMaintenanceFlag')."');",
				'class'   => 'delpage delete'
			));
		}
		else {
			$this->_addButton('maintenance_flag', array(
				'label'   => $this->__('Enable the maintenance page'),
				'onclick' => "versioning.confirmFlag(this, '".$this->getUrl('*/*/addMaintenanceFlag')."', this.textContent, '".$this->helper('versioning')->getMaintenanceInfo(true)."', '".$this->__('Martian sunset seen by Spirit.')."');"
			));
		}

		if (is_file($this->helper('versioning')->getUpgradeFlag())) {
			$this->_addButton('upgrade_flag', array(
				'label'   => $this->__('Remove the update page'),
				'onclick' => "versioning.cancelFlag(this, '".$this->getUrl('*/*/delUpgradeFlag')."');",
				'class'   => 'delpage delete'
			));
		}
		else {
			$this->_addButton('upgrade_flag', array(
				'label'   => $this->__('Enable the update page'),
				'onclick' => "versioning.confirmFlag(this, '".$this->getUrl('*/*/addUpgradeFlag')."', this.textContent, '".$this->helper('versioning')->getUpgradeInfo(true)."', '".$this->__('Martian sunset seen by Spirit.')."');"
			));
		}

		$this->_addButton('history', array(
			'label'   => $this->__('Updates history'),
			'onclick' => "setLocation('".$this->getUrl('*/*/history')."');",
			'class'   => 'go'
		));

		$this->_addButton('status', array(
			'label'   => $this->__('Repository status'),
			'onclick' => "setLocation('".$this->getUrl('*/*/status')."');",
			'class'   => 'go'
		));
	}

	public function getGridHtml() {

		$commits = Mage::registry('versioning')->getCommitCollection();
		$count  = count($commits) - 1;
		$cols = 0;
		$hash = '';

		// comptage dans l'ordre inverse
		// le commit le plus récent = count($commits) - 1
		// le commit le plus ancien = 0
		foreach ($commits as $commit) {

			$hash .= "\n";
			$hash .= '"'.$commit->getRevision().'": {';
			$hash .=  '"revision": "'.$commit->getRevision().'",';
			$hash .=  '"parents": ["'.implode('","', $commit->getParents()).'"],';
			$hash .=  '"branch": "'.$commit->getBranch().'",';
			$hash .=  '"col": '.$commit->getColumn().',';
			$hash .=  '"row": '.$count--;
			$hash .= '},';

			$cols = ($commit->getColumn() > $cols) ? $commit->getColumn() : $cols;
		}

		return $this->getChildHtml('grid')."\n".
			'<script type="text/javascript">'."\n".
			'var versioningIds = {'.substr($hash, 0, -1).'}, versioningCols = '.$cols.';'."\n".
			'</script>';

	}

	public function getHeaderCssClass() {
		return 'icon-head '.parent::getHeaderCssClass().' '.Mage::getStoreConfig('versioning/scm/type');
	}
}