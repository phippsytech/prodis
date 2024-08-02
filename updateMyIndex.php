<?php
// Load the HTML file into a DOMDocument object
$doc = new DOMDocument();
// ignore warnings
libxml_use_internal_errors(true);
$doc->loadHTMLFile('/var/www/staging.phippsy.tech/ndismate/my/dist/index.html');

// Get the script and link tags from the head
$scriptTag = $doc->getElementsByTagName('script')->item(1);
if($scriptTag){
    $scriptSrc = $scriptTag->getAttribute('src');
    // $scriptTag->parentNode->replaceChild($doc->createComment($doc->saveHTML($scriptTag)), $scriptTag);
    $scriptTag->parentNode->removeChild($scriptTag);
}


$linkTag = $doc->getElementsByTagName('link')->item(2);
if($linkTag){
    $linkHref = $linkTag->getAttribute('href');
    // $linkTag->parentNode->replaceChild($doc->createComment($doc->saveHTML($linkTag)), $linkTag);
    $linkTag->parentNode->removeChild($linkTag);
}
// Save the modified HTML file
if($linkTag || $scriptTag){

    date_default_timezone_set('Australia/Sydney');
    // Create the meta tag

    $xpath = new DOMXPath($doc);

    $metaTag = $xpath->query('//meta[@name="version"]')->item(0);

    if ($metaTag === null) {
        $metaTag = $doc->createElement('meta');
        $metaTag->setAttribute('name', 'version');
        
        // Add the meta tag to the head element
        $head = $doc->getElementsByTagName('head')->item(0);
        $head->appendChild($metaTag);
    }

    $metaTag->setAttribute('content', date('j M Y - g:ia', time()));

    $doc->saveHTMLFile('/var/www/staging.phippsy.tech/ndismate/my/dist/index.html');
    $init = json_encode([
        "script"=>$scriptSrc,
        "style"=>$linkHref
    ]);
    file_put_contents("/var/www/staging.phippsy.tech/ndismate/src/myinit.json", $init);
}
