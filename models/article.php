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


require_once( JPATH_COMPONENT_SITE.DIRECTORY_SEPARATOR."htmlpurifier".DIRECTORY_SEPARATOR."HTMLPurifier.auto.php");

/**
 * ortholib model.
 *
 * @package  ortholib
 * @since    1.0
 */
class OrtholibModelArticle extends JModelLegacy
{

    /**
     * @var string message
     */
    protected $bookname;
    protected $article;
    protected $articletitle;
    protected $anchor;
    protected $booktoc;

    /**
     * Get the message
     *
     * @return  string  The message to be displayed to the user
     */
    public function getBookName()
    {
        return $this->bookname;
    }

    public function getAnchor()
    {
        if (!isset($this->anchor))
        {
            return "";
        }

        return $this->anchor;
    }

    public function getArticleTitle()
    {
        return $this->articletitle;
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
                $src = (string)$navPoint->content["src"];
                $tocelems[$tocitemid]["src"] = $src;
                $tocelems[$tocitemid]["anchor"] = "";

                if (strstr($src, "#"))
                {
                    list ($url, $anchor) = explode('#', (string)$navPoint->content["src"], 2);
                    $tocelems[$tocitemid]["src"] = $url;
                    $tocelems[$tocitemid]["anchor"] = $anchor;
                }
            }

            if (!empty($navPoint->navPoint))
            {
                $tocelems[$tocitemid]["children"] = array();
                $this->getSubItems($navPoint->navPoint, $tocelems[$tocitemid]["children"]);
            }
        }
    }

    public function getArticle()
    {
        if (!isset($this->article))
        {
            $app = JFactory::getApplication();
            $bookid = $app->input->get('bookid', "");
            $navpoint = $app->input->get('navpoint');

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
                return $this->article;
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
                $tocncx->registerXPathNamespace('n', 'http://www.daisy.org/z3986/2005/ncx/');

                $articlefile_obj = $tocncx->xpath("(//n:navPoint)[1]/n:content/@src");
                $articletitle_obj = $tocncx->xpath("(//n:navPoint)[1]/n:navLabel/n:text/text()");
                $bookname_obj = $tocncx->xpath("//n:docTitle/n:text/text()");

                if ($navpoint) {
                    $articlefile_obj = $tocncx->xpath("//n:navPoint[@id=\"" . $navpoint . "\"]/n:content/@src");
                    $articletitle_obj = $tocncx->xpath("//n:navPoint[@id=\"" . $navpoint . "\"]/n:navLabel/n:text/text()");
                }

                $articlefile_part = (string)$articlefile_obj[0];
                $this->articletitle = (string)$articletitle_obj[0];
                $this->bookname = (string)$bookname_obj[0];

                if (!empty($articlefile_part))
                {
                    list ($url, $anchor) = explode('#', $articlefile_part, 2);
                    $articlefile_part = $url;
                    $this->anchor = $anchor;

                    $oebpsdir = dirname($toc_ncx_file);
                    $articlefile_full = $oebpsdir.DIRECTORY_SEPARATOR.$articlefile_part;

                    $path_parts = pathinfo($articlefile_part);
                    $articlefile_part_cache = $path_parts['filename']."_cache.".$path_parts['extension'];
                    $articlefile_full_cache = $oebpsdir.DIRECTORY_SEPARATOR.$articlefile_part_cache;

                    $article = "";

                    if(!file_exists($articlefile_full_cache) || filemtime($articlefile_full) > filemtime($articlefile_full_cache))
                    {
                        $article = file_get_contents($articlefile_full);

                        $config = HTMLPurifier_Config::createDefault();
                        $config->set('Core.Encoding', 'utf-8');
                        $config->set('HTML.Doctype', 'XHTML 1.1');
                        //$config->set('HTML.Allowed', 'p,a[title|name],abbr[title],acronym[title],
                        //b,strong,blockquote[cite],code,em,i,span,div,h1,h2,h3,h4,h5');
                        $config->set('Attr.EnableID', true);
                        //$config->set('Attr.IDPrefix', 'user_');
                        //$config->set('HTML.Trusted', true);
                        //$config->set('HTML.Attr.Name.UseCDATA', true);

                        //$config->set('AutoFormat.AutoParagraph', TRUE);
                        //$config->set('AutoFormat.Linkify', TRUE);

                        $purifier = new HTMLPurifier($config);

                        $article = $purifier->purify($article);
                        file_put_contents ($articlefile_full_cache, $article);
                        chmod($articlefile_full_cache, 0777);  // восьмеричное, верный способ
                    }
                    else
                    {
                        $article = file_get_contents($articlefile_full_cache);
                    }


                    $this->article = $article;
                }
            }
        }

        return $this->article;
    }
}
