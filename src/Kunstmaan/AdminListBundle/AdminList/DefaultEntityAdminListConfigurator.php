<?php

namespace Kunstmaan\AdminListBundle\AdminList;

/**
 * Generates adminlist paths by convention because this is a repeating effort.
 *
 * To use this, the routing names of the adminlist methods in your controller must follow this format:
 * <FullBundleName>_admin_<LowerCaseEntityNamePlural>[_<add/edit/delete/export>]
 *
 * Additionally, the delete action in your controller must be named 'deleteAction'.
 * If your controller is not located in a subfolder of the bundle's Controller folder, you must provide it's path at construction.
 * eg. /src/FullBundleName/Controller/Subfolder/EntityController
 * -> $controllerPath = 'Subfolder\\Entity'
 */

abstract class DefaultEntityAdminListConfigurator extends AbstractAdminListConfigurator {

	const SUFFIX_ADD = 'add';
	const SUFFIX_EDIT = 'edit';
	const SUFFIX_EXPORT = 'export';
	const SUFFIX_DELETE = 'delete';

	protected $bundleName;
	protected $entityName;
	protected $controllerPath;

	function __construct($bundleName, $entityName, $controllerPath = null) {
		$this->bundleName = $bundleName;
		$this->entityName = $entityName;
		$this->controllerPath = $controllerPath;
	}

	public function getAddUrlFor($params = array()) {
		return array(
			strtolower($this->getEntityName()) => array('path' => $this->getPathByConvention($this::SUFFIX_ADD),
			'params' => $params)
		);
	}

	public function getEditUrlFor($item) {
		return array(
			'path'		=> $this->getPathByConvention($this::SUFFIX_EDIT),
			'params'	=> array('entity_id' => $item->getId()
		));
	}

	public function getDeleteUrlFor($item) {
		return array(
			'action' => sprintf('%s:delete', $this->getControllerPath()),
			'path' => $this->getPathByConvention($this::SUFFIX_DELETE)
		);
	}

	public function getIndexUrlFor() {
		return $this->getPathByConvention();
	}

	public function getRepositoryName() {
		return sprintf('%s:%s', $this->getBundleName(), $this->getEntityName());
	}

	public function getPathByConvention($suffix = null) {
		if (empty($suffix)) {
			return sprintf('%s_admin_%ss', $this->getBundleName(), strtolower($this->getEntityName()));
		}
		return sprintf('%s_admin_%ss_%s', $this->getBundleName(), strtolower($this->getEntityName()), $suffix);
	}

	public function getControllerPathByConvention() {
		return sprintf('%s:%s', $this->getBundleName(), $this->getEntityName());
	}

	public function getBundleName() {
		return $this->bundleName;
	}

	public function getEntityName() {
		return $this->entityName;
	}

	public function getControllerPath() {
		if (!empty($this->controllerPath)) {
			return sprintf('%s:%s', $this->getBundleName(), $this->controllerPath);
		}
		return $this->getControllerPathByConvention();
	}
}
