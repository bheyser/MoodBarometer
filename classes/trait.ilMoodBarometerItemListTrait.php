<?php
/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Trait ilMoodBarometerItemListTrait
 *
 * @author      Björn Heyser <info@bjoernheyser.de>
 *
 * @package     Plugins/MoodBarometer
 */
trait ilMoodBarometerItemListTrait
{
	/**
	 * @var array
	 */
	protected $items = array();
	
	/**
	 * @param mixed $item
	 */
	public function addItem($item)
	{
		$this->items[] = $item;
	}
	
	/**
	 * @return array
	 */
	public function getItems()
	{
		return $this->items;
	}
	
	/**
	 * @return bool
	 */
	public function hasItems()
	{
		return (bool)count($this->items);
	}
	
	/**
	 *
	 */
	public function resetItems()
	{
		$this->items = array();
	}
	
	/**
	 * @param array $items
	 */
	public function setItems($items)
	{
		$this->items = $items;
	}
	
	public function current()
	{
		return current($this->items);
	}
	
	public function next()
	{
		return next($this->items);
	}
	
	public function key()
	{
		return key($this->items);
	}
	
	public function valid()
	{
		return key($this->items) !== null;
	}
	
	public function rewind()
	{
		return reset($this->items);
	}
}