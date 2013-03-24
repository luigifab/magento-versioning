<?php
/**
 * Created V/08/06/2012
 * Updated D/24/03/2013
 * Version 8
 *
 * Copyright 2012-2013 | Fabrice Creuzot (luigifab) <code~luigifab~info>
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

class Luigifab_Versioning_Block_Adminhtml_Widget_Graph extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

	public function render(Varien_Object $row) {

		$parents = implode(' ', $row->getParents());
		$revision = str_replace('.', '-', $row->getRevision());
		$tags = implode(' ', $row->getTags());

		$graph = array(
			'<input type="hidden" value="'.$parents.'" class="parents rev-'.$revision.' parent-'.implode(' parent-', $row->getParents()).'" />',
			'<input type="hidden" value="'.$tags.'" class="tags rev-'.$revision.'" />',
			'<input type="hidden" value="'.$row->getRevision().'" class="revision rev-'.$revision.'" />',
			'<input type="hidden" value="'.$row->getBranchName().'" class="branch" />'
		);

		return str_replace(array('-', '.', ' parent', ' rev', ' [merge]'), array('', '', ' parent-', ' rev-', ''), implode($graph));
	}
}