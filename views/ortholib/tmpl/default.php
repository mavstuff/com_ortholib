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

<h1>Библиотека</h1>

<ul>
    <?php

        if (!empty($this->bookslist)) {
            foreach ($this->bookslist as $directorykey => $directoryval)
            {
                ?>

                <li><a href="index.php?option=com_ortholib&view=article&bookid=<?php echo $directorykey ?>"><?php if (!empty($directoryval["creator"])) echo $directoryval["creator"].". " ?><?php echo $directoryval["title"] ?></a>
                </li>

                <?php
            }
        }

    ?>

</ul>