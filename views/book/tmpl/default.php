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

JHtml::_('script', 'com_ortholib/script.js', false, true);
JHtml::_('stylesheet', 'com_ortholib/style.css', array(), true);



?>

<h1><?php /*echo $this->bookname*/ ?></h1>



<?php echo $this->PrintBookToc($this->booktoc); ?>


