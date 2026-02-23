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

/**
 * ortholib view.
 *
 * @package  ortholib
 * @since    1.0
 */
class OrtholibViewBook extends JViewLegacy
{

    public $bookid;
    public $bookname;
    public $booktoc;

    /**
     * Display the Hello World view
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */
    function display($tpl = null)
    {
        $app = JFactory::getApplication();

        $this->bookid = $app->input->get('bookid');

        // Assign data to the view
        $this->bookname = $this->get('Bookname');

        $this->booktoc = $this->get('BookToc');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JLog::add(implode('<br />', $errors), JLog::WARNING, 'jerror');

            return false;
        }

        // Display the view
        parent::display($tpl);
    }


    function PrintBookToc($booktoc)
    {
        $out = '<ul>';

        foreach($booktoc as $key => $value)
        {
            if (!empty($value["text"]))
            {
                $out .= "<li><a href=\"index.php?option=com_ortholib&view=article&bookid=".$this->bookid."&navpoint=".$key."\">".
                    $value["text"]."</a></li>";
            }

            if (!empty($value["children"]))
            {
                $out .= $this->PrintBookToc($value["children"]);
            }
        }

        $out.='</ul>';

        return $out;
    }

}
