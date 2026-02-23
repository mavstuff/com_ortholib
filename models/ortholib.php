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
class OrtholibModelOrtholib extends JModelLegacy
{

    /**
     * @var string message
     */
    protected $bookslist;

    protected function getBookInfo($content_opf_file, $directory_base)
    {
        $contentopfxml = simplexml_load_file($content_opf_file);

        $metadataxml = $contentopfxml->metadata->children("dc", TRUE);

        if ($metadataxml)
        {
            $title = !empty($metadataxml->title) ? (string)$metadataxml->title : "";
            $creator = !empty($metadataxml->creator) ? (string)$metadataxml->creator : "";

            if (empty($title))
                $title = $directory_base;

            $book = array(
                "creator" => $creator,
                "title" => $title);

            return $book;
        }

        return null;
    }

    /**
     * Get the message
     *
     * @return  string  The message to be displayed to the user
     */
    public function getBooksList()
    {
        if (!isset($this->bookslist))
        {
            $this->bookslist = array();

            $searchPath = JPATH_SITE.DIRECTORY_SEPARATOR."media/com_ortholib/epubunzip";

            $directories = glob($searchPath . '/*' , GLOB_ONLYDIR);

            foreach ($directories as $directory)
            {
                $containerfile = $directory."/META-INF/container.xml";

                if (file_exists($containerfile))
                {
                    $containerxml = simplexml_load_file($containerfile);

                    if (!empty($containerxml->rootfiles->rootfile["full-path"]))
                    {
                        $content_opf_file_part = $containerxml->rootfiles->rootfile["full-path"];
                        $content_opf_file_full = $directory."/".$content_opf_file_part;

                        $directory_base = basename($directory);

                        //echo $directory_base.PHP_EOL;

                        if (file_exists($content_opf_file_full))
                        {
                            $book = $this->getBookInfo($content_opf_file_full, $directory_base);

                            if ($book != null)
                            {
                                $this->bookslist[$directory_base] = $book;
                            }
                        }
                    }
                }
            }

			//usort($this->bookslist, "custom_sort");
			// Define the custom sort function
			//function custom_sort($a,$b) {
			//	return $a['creator']>$b['creator'];
			//}
        }

        return $this->bookslist;
    }



}
