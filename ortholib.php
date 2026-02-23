<?php
/**
 * @package    ortholib
 *
 * @author     MAV <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

defined('_JEXEC') or die;

$controller = JControllerLegacy::getInstance('ortholib');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
