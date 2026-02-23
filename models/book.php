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
 * ortholib model.
 *
 * @package  ortholib
 * @since    1.0
 */
class OrtholibModelBook extends JModelLegacy
{

    /**
     * @var string message
     */
    protected $bookname;
    protected $bookid;
    protected $booktoc;
    protected $tocitemid;

    /**
     * Get the message
     *
     * @return  string  The message to be displayed to the user
     */
    public function getBookname()
    {
        if (!isset($this->bookname))
        {
            $this->bookname = 'Book name';
        }

        return $this->bookname;
    }


    public function getBookToc()
    {
        if (!isset($this->booktoc))
        {
            $this->booktoc = array();

            $app = JFactory::getApplication();
            $bookid = $app->input->get('bookid');

            $searchPath = JPATH_SITE . DIRECTORY_SEPARATOR . "media/com_ortholib/epubunzip";

            $directories = glob($searchPath . '/*', GLOB_ONLYDIR);

            $found = false;
            $bookdirectory = "";

            foreach ($directories as $directory) {
                $directory_base = basename($directory);
                if ($bookid == $directory_base) {
                    $found = true;
                    $bookdirectory = $directory;
                    break;
                }
            }

            if (!$found) {
                return $this->booktoc;
            }

            $containerfile = $bookdirectory . "/META-INF/container.xml";

            $toc_ncx_file = "";

            if (file_exists($containerfile)) {
                $containerxml = simplexml_load_file($containerfile);

                if (!empty($containerxml->rootfiles->rootfile["full-path"])) {
                    $content_opf_file_part = $containerxml->rootfiles->rootfile["full-path"];
                    $content_opf_file_full = $bookdirectory . "/" . $content_opf_file_part;

                    if (file_exists($content_opf_file_full)) {
                        $toc_ncx_file = $this->getBookTocFile($content_opf_file_full);

                    }
                }
            }

            if (!empty($toc_ncx_file))
            {
                $tocncx = simplexml_load_file($toc_ncx_file);

                $navMap = $tocncx->navMap;

                $this->tocitemid = 1;

                $this->getSubItems($navMap, $this->booktoc);

            }
        }

        return $this->booktoc;
    }


    protected function getBookTocFile($content_opf_file)
    {
        $contentopfxml = simplexml_load_file($content_opf_file);
        $oebpsdir = dirname($content_opf_file);

        if (!empty($contentopfxml->manifest))
        {
            foreach ($contentopfxml->manifest->item as $item)
            {
                if ($item['id'] == 'ncx')
                {
                    if (isset ($item['href']))
                    {
                        $toc_ncx_file = $oebpsdir.DIRECTORY_SEPARATOR.$item['href'];

                        if (file_exists($toc_ncx_file))
                            return $toc_ncx_file;
                    }
                }
            }
        }
        return null;
    }

    protected function getSubItems(SimpleXMLElement $navPoints, &$tocelems)
    {
        foreach ($navPoints as $navPoint)
        {
            //if (empty($navPoint["id"]))
            //   continue;

            //var_dump($navPoint["id"]);

            $tocitemid = (string)$navPoint["id"];

            if (!empty($navPoint->navLabel->text))
            {
                $tocelems[$tocitemid]["text"] = (string) $navPoint->navLabel->text;
                $tocelems[$tocitemid]["src"] = (string) $navPoint->content["src"];
            }

            if (!empty($navPoint->navPoint))
            {
                $tocelems[$tocitemid]["children"] = array();
                $this->getSubItems($navPoint->navPoint, $tocelems[$tocitemid]["children"]);
            }
        }
    }
}
