<?php
/**
 *          Pico Partials
 *
 *          Lets you include twig partials in your markdown using:
 *          [twig:<partial>]
 *          eg: [twig:sidebar.twig] 
 * 
 *          Avoid creating an infinite loop by ensuring you don't call a partial that contains itself.
 * 
 * @author  Michael Phipps
 * @link    
 * @link    https://github.com/lloydsargent/MarkdownMacros
 * @license http://opensource.org/licenses/MIT The MIT License
 * 
 * Hours having written php code, maybe 4 hours. Heh.
 */
class PicoPartials extends AbstractPicoPlugin
{
    const API_VERSION = 3;
    private $currentPagePath;
    private $base_url = "";

    /**
     * Triggered after Pico has prepared the raw file contents for parsing
     *
     * @see DummyPlugin::onContentParsing()
     * @see Pico::parseFileContent()
     * @see DummyPlugin::onContentParsed()
     *
     * @param string &$markdown Markdown contents of the requested page
     *
     * @return void
     */
    public function onContentPrepared(&$markdown)
    {
        
        // echo "<!--";
        // print_r($this);
        // echo "-->";

        // get unique list of partials
        $pattern = '/\[twig:(.*)\]/';
        preg_match_all($pattern, $markdown, $matches);
        $partials = array_unique($matches[1]);


        // I believe there is a way to use the existing twig object rather than create a new instance, but I will work that out in future.

        $loader = new \Twig\Loader\FilesystemLoader('/var/www/michaelphipps.com/public/themes/michaelphipps');
        $twig = new \Twig\Environment($loader, [
            // 'cache' => '/path/to/compilation_cache',
        ]);


        foreach ($partials as $partial) {
            try {
            $template = $twig->load($partial);
            $replacement_text = $template->render(['the' => 'variables', 'go' => 'here']);
            $markdown = str_replace("[twig:".$partial."]", $replacement_text, $markdown);
            } catch (Exception $e) {
                $markdown = str_replace("[twig:".$partial."]", "<span style='padding:5px;background:red;color:white;'>[twig:".$partial."]</span> <-- Look, an error!", $markdown);
            }
        }











        // $myconfig = $this->getPico()->getConfig();                              // get the configuration list
        // $macros_name = $myconfig['custom_macros'];                              // get my custom macros name out of the list
        // $macros = $myconfig[$macros_name];                                      // get my list of custom macros

        // //----- build an array of key:value pairs representing the macros
        // $replacement_list = $this->createReplacementList($markdown, $macros);

        // //----- replace the macro with the substitute text
        // $this->performSubstitution($markdown, $macros, $replacement_list);

        // return;        
    }



}