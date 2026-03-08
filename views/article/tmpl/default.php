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

<h1><?php /*echo $this->bookid.", id ".$this->bookid*/?> </h1>

<div class="bookcontainer">
    <button id="tocBtn">☰ Зміст</button>
    <div class="lefttoc">
        <a href="#" id="tocClose">✖ Закрити</a>
        <div><a href="index.php?option=com_ortholib&view=ortholib">&lt;&lt;До списку книг</a> </div>
        <?php echo $this->PrintBookToc($this->booktoc); ?>
    </div>
    <div class="articletext">
        <?php echo $this->article; ?>
    </div>
</div>


