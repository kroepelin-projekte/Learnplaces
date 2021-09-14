<?php
declare(strict_types=1);

namespace SRAG\Learnplaces\gui\block\util;

/**
 * Trait ReadOnlyViewAware
 *
 * @package SRAG\Learnplaces\gui\block\util
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
trait ReadOnlyViewAware {

	/**
	 * @var bool $readonly
	 */
	private $readonly = true;


	/**
	 * @return bool
	 */
	public function isReadonly(): bool {
		return $this->readonly;
	}


	/**
	 * @param bool $readonly
	 *
	 * @return void
	 */
	public function setReadonly(bool $readonly): void {
		$this->readonly = $readonly;
	}
}