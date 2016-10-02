<?php
/**
 * Created V/06/04/2012
 * Updated V/16/09/2016
 * Version 27
 *
 * Copyright 2011-2016 | Fabrice Creuzot (luigifab) <code~luigifab~info>
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

class Luigifab_Versioning_Block_Adminhtml_History_Grid extends Mage_Adminhtml_Block_Widget_Grid {

	public function __construct() {

		parent::__construct();

		$this->setId('history_grid');
		$this->setDefaultSort('date');
		$this->setDefaultDir('desc');

		$this->setUseAjax(true);
		$this->setSaveParametersInSession(false);
		$this->setPagerVisibility(true);
		$this->setFilterVisibility(false);
	}

	protected function _prepareCollection() {

		$page = $this->getParam($this->getVarNamePage(), 1);
		$size = $this->getParam($this->getVarNameLimit(), 20);

		$this->setCollection(Mage::getModel('versioning/history')->init($page, $size));
		return parent::_prepareCollection();
	}

	protected function _prepareColumns() {

		$this->addColumn('from', array(
			'header'   => $this->__('Current revision'),
			'index'    => 'from',
			'align'    => 'center',
			'width'    => '120px',
			'sortable' => false,
			'filter'   => false
		));

		$this->addColumn('to', array(
			'header'   => $this->__('Requested revision'),
			'index'    => 'to',
			'align'    => 'center',
			'width'    => '120px',
			'sortable' => false,
			'filter'   => false
		));

		$this->addColumn('branch', array(
			'header'   => $this->__('Branch'),
			'index'    => 'branch',
			'align'    => 'center',
			'width'    => '130px',
			'sortable' => false,
			'filter'   => false
		));

		$this->addColumn('empty', array(
			'sortable' => false,
			'filter'   => false
		));

		$this->addColumn('remote_addr', array(
			'header'   => $this->__('IP address'),
			'index'    => 'remote_addr',
			'align'    => 'center',
			'width'    => '150px',
			'sortable' => false,
			'filter'   => false
		));

		$this->addColumn('user', array(
			'header'   => $this->__('User'),
			'index'    => 'user',
			'align'    => 'center',
			'width'    => '150px',
			'sortable' => false,
			'filter'   => false
		));

		$this->addColumn('date', array(
			'header'   => $this->__('Date'),
			'index'    => 'date',
			'type'     => 'datetime',
			'format'   => Mage::getSingleton('core/locale')->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
			'align'    => 'center',
			'width'    => '150px',
			'sortable' => false,
			'filter'   => false
		));

		$this->addColumn('duration', array(
			'header'   => $this->__('Duration'),
			'index'    => 'duration',
			'align'    => 'center',
			'width'    => '60px',
			'sortable' => false,
			'filter'   => false,
			'frame_callback' => array($this, 'decorateDuration')
		));

		$this->addColumn('status', array(
			'header'    => $this->__('Status'),
			'index'     => 'status',
			'align'     => 'status',
			'width'     => '125px',
			'filter'    => false,
			'sortable'  => false,
			'frame_callback' => array($this, 'decorateStatus')
		));

		$this->addColumn('action', array(
			'align'     => 'center',
			'width'     => '55px',
			'filter'    => false,
			'sortable'  => false,
			'is_system' => true,
			'frame_callback' => array($this, 'decorateLink')
		));

		return parent::_prepareColumns();
	}


	public function getRowClass($row) {
		return '';
	}

	public function getRowUrl($row) {
		return null;
	}

	public function decorateStatus($value, $row, $column, $isExport) {

		$status = (in_array($row->getData('status'), array('Update completed', 'Upgrade completed'))) ?
			'success' : 'error'; // pour translate.php $this->__('Success') - il y avait Upgrade avant 3.4.2

		return '<span class="grid-'.$status.'">'.$this->__(ucfirst($status)).'</span>';
	}

	public function decorateDuration($value, $row, $column, $isExport) {

		$data = $row->getData('duration');
		$minutes = intval($data / 60);
		$seconds = intval($data % 60);

		if ($data > 599)
			$data = '<strong>'.(($seconds > 9) ? $minutes.':'.$seconds : $minutes.':0'.$seconds).'</strong>';
		else if ($data > 59)
			$data = '<strong>'.(($seconds > 9) ? '0'.$minutes.':'.$seconds : '0'.$minutes.':0'.$seconds).'</strong>';
		else if ($data > 1)
			$data = ($seconds > 9) ? '00:'.$data : '00:0'.$data;
		else
			$data = '⩽ 1';

		return $data;
	}

	public function decorateLink($value, $row, $column, $isExport) {

		$data = addslashes(base64_encode($row->getData('details')));
		return '<a href="#" onclick="return versioning.history(this, \''.$data.'\');">'.$this->__('View').'</a>';
	}
}