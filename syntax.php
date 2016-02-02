<?php
/**
 * DokuWiki Plugin articlelinks (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Michael Große <dokuwiki@cosmocode.de>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

class syntax_plugin_articlelinks extends DokuWiki_Syntax_Plugin {
    /**
     * @return string Syntax mode type
     */
    public function getType() {
        return 'formatting';
    }
    /**
     * @return string Paragraph type
     */
    public function getPType() {
        return 'block';
    }
    /**
     * @return int Sort order - Low numbers go before high numbers
     */
    public function getSort() {
        return 81;
    }

    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('<(?:relatedsection|relatedarticle|relatedarticles|mainarticle)>.*?</(?:article|section)>',$mode,'plugin_articlelinks');
    }

    /**
     * Handle matches of the articlelinks syntax
     *
     * @param string $match The match of the syntax
     * @param int    $state The state of the handler
     * @param int    $pos The position in the document
     * @param Doku_Handler    $handler The handler
     * @return array Data for the renderer
     */
    public function handle($match, $state, $pos, Doku_Handler $handler){
        $data = array();
        $type = substr($match,1,strpos($match, '>')-1);

        switch ($type) {
            case 'relatedarticles':
                $links = $this->getLang('related articles');
                $endtag = '</article>';
                break;
            case 'relatedarticle':
                $links = $this->getLang('related article');
                $endtag = '</article>';
                break;
            case 'mainarticle':
                $links = $this->getLang('main article');
                $endtag = '</article>';
                break;
            case 'relatedsection':
                $links = $this->getLang('related section');
                $endtag = '</section>';
                break;
            default:
                $links = '';
        }

        $links .= substr($match,strpos($match, '>') + 1 , strpos($match, $endtag) - strpos($match, '>') -1);
        $data['links'] =  p_get_instructions($links);
        return $data;
    }

    /**
     * Render xhtml output or metadata
     *
     * @param string         $mode      Renderer mode (supported modes: xhtml)
     * @param Doku_Renderer  $renderer  The renderer
     * @param array          $data      The data from the handler() function
     * @return bool If rendering was successful.
     */
    public function render($mode, Doku_Renderer $renderer, $data) {
        if($mode != 'xhtml') return false;

        $renderer->doc .= '<div class="mainarticle">';
        $info = array();
        $renderer->doc .= p_render('xhtml', $data['links'], $info);
        $renderer->doc .= '</div>';

        return true;
    }
}

// vim:ts=4:sw=4:et:
