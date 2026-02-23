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
class OrtholibViewArticle extends JViewLegacy
{

    public $bookid;
    public $bookname;
    public $navpoint;
    public $article;
    public $articletitle;
    public $anchor;

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
        $this->navpoint = $app->input->get('navpoint');

        // Assign data to the view
        $this->article = $this->get('Article');
        $this->bookname = $this->get('BookName');
        $this->articletitle = $this->get('ArticleTitle');

        $this->anchor = $this->get('Anchor');
        $this->booktoc = $this->get('BookToc');

        $pathway = $app->getPathway();
        $pathway->addItem($this->bookname, "index.php?option=com_ortholib&view=article&bookid=".$this->bookid);
        $pathway->addItem($this->articletitle);

        $document = JFactory::getDocument();
        $document->setTitle($this->articletitle);


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
                if (!empty($value["anchor"])) {
                    $out .= "<li><a style=\"cursor:pointer;\" onclick='window.location.href=\"".JRoute::_("index.php?option=com_ortholib&view=article&bookid=" . $this->bookid . "&navpoint=" . $key)."\"+\"#" . $value["anchor"] . "\"'>" .
                        $value["text"] . "</a></li>";
                }
                else{
                    $out .= "<li><a href=\"".JRoute::_("index.php?option=com_ortholib&view=article&bookid=" . $this->bookid . "&navpoint=" . $key) . "\">" .
                        $value["text"] . "</a></li>";
                }
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
