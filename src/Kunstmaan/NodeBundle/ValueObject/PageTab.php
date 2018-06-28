<?php

namespace Kunstmaan\NodeBundle\ValueObject;

class PageTab {
	/**
	 * @var string
	 */
	private $internalName;

	/**
	 * @var string
	 */
	private $tabTitle;

	/**
	 * @var string
	 */
	private $template;

	/**
	 * @var string
	 */
	private $formTypeClass;

	/**
	 * @var integer
	 */
	private $position = 1;

	/**
	 * @return string
	 */
	public function getInternalName()
	{
		return $this->internalName;
	}

	/**
	 * @param string $internalName
	 * @return PageTab
	 */
	public function setInternalName($internalName)
	{
		$this->internalName = $internalName;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTabTitle()
	{
		return $this->tabTitle;
	}

	/**
	 * @param string $tabTitle
	 * @return PageTab
	 */
	public function setTabTitle($tabTitle)
	{
		$this->tabTitle = $tabTitle;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTemplate()
	{
		return $this->template;
	}

	/**
	 * @param string $template
	 * @return PageTab
	 */
	public function setTemplate($template)
	{
		$this->template = $template;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getFormTypeClass()
	{
		return $this->formTypeClass;
	}

	/**
	 * @param string $formTypeClass
	 * @return PageTab
	 */
	public function setFormTypeClass($formTypeClass)
	{
		$this->formTypeClass = $formTypeClass;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getPosition()
	{
		return $this->position;
	}

	/**
	 * @param int $position
	 * @return PageTab
	 */
	public function setPosition($position)
	{
		$this->position = $position;

		return $this;
	}
}