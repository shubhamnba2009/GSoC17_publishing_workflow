<?php
/**
 * Items Model for a Prove Component.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_prove
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       4.0
 */
namespace Joomla\Component\Workflow\Administrator\Model;

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Model\ListModel;

/**
 * Model class for items
 *
 * @since  4.0
 */
class Transitions extends ListModel
{

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   4.0
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = \JFactory::getApplication();
		$workflowID = $app->getUserStateFromRequest($this->context . '.filter.workflow_id', 'workflow_id', 1, 'cmd');

		$this->setState('filter.workflow_id', $workflowID);

		parent::populateState($ordering, $direction);

		// TODO: Change the autogenerated stub
	}


	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  \Joomla\CMS\Table\Table  A JTable object
	 *
	 * @since   4.0
	 */
	public function getTable($type = 'Transition', $prefix = 'Administrator', $config = array())
	{
		return parent::getTable($type, $prefix, $config);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  string  The query to database.
	 *
	 * @since   4.0
	 */
	public function getListQuery()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		$select = 'transition.`id`, transition.`title`, f_status.`title` AS `from_status`, t_status.`title` AS `to_status`';
		$joinTo = '#__workflow_status AS t_status ON t_status.`id` = transition.`to_status_id`';

		$query
			->select($select)
			->from('#__workflow_transitions AS transition')
			->leftJoin('#__workflow_status AS f_status ON f_status.`id` = transition.`from_status_id`')
			->leftJoin($joinTo);

		// Filter by extension
		if ($workflowID = (int) $this->getState('filter.workflow_id'))
		{
			$query->where('transition.workflow_id = ' . $db->quote($workflowID));
		}

		return $query;
	}
}
