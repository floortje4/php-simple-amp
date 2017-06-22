<!doctype html>
<html amp lang="nl">
<head>
	<meta charset="utf-8">
	<script async src="https://cdn.ampproject.org/v0.js"></script>
	<script async custom-element="amp-ad" src="https://cdn.ampproject.org/v0/amp-ad-0.1.js"></script>
	<script async custom-element="amp-sidebar" src="https://cdn.ampproject.org/v0/amp-sidebar-0.1.js"></script>
	<script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>
	<meta name="viewport" content="width=device-width,minimum-scale=1">
	<style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
	<style amp-custom>
    	<?php include('static/css/amp/amp.css');?>
	</style>
	<link rel="canonical" href="<?php echo str_replace('?tpl=amp','',$_SERVER['REQUEST_URI']);?>" />
	<body>
    	<amp-analytics type="googleanalytics">
            <script type="application/json">
            {
              "vars": {
                "account": "xx-00000-00"
              },
              "triggers": {
                "trackPageview": {
                  "on": "visible",
                  "request": "pageview"
                }
              }
            }
            </script>
        </amp-analytics>
    	<header id="logo" >
        	<button on='tap:sidebar1.toggle' class="hamburger">☰</button>
    	</header>
    	<amp-sidebar id="sidebar1" layout="nodisplay" side="left">
    		<!-- menu goes here -->
        </amp-sidebar>


		<?php

        // catch templates
		ob_start ();
		include ($tpl->main);
		$html = ob_get_contents ();
		ob_end_clean ();

		// replace img with amp-img
		$pattern = '/<img/i';
		$replacement = '<amp-img';
		$html = preg_replace($pattern, $replacement, $html);	

		$pattern = '/<\/img/i';
		$replacement = '</amp-img';
		$html = preg_replace($pattern, $replacement, $html);


        // replace ads :: ins with amp-ad (we dont use <ins />)
		$pattern = '/<\/ins/i';
		$replacement = '</amp-ad';
		$html = preg_replace($pattern, $replacement, $html);

		$pattern = '/<ins/i';
		$replacement = '<amp-ad';
		$html = preg_replace($pattern, $replacement, $html);

        // load html into domdocument
		$doc = new DOMDocument();
		$doc->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // xpath object
		$xpath = new DOMXPath($doc);

		// remove script + css + forms
		$nodes = $xpath->query("//script | style | form");
		foreach ($nodes as $node) {
			$node->parentNode->removeChild($node);
		}

        // fix image
		$nodes = $xpath->query("//amp-img");
		foreach ($nodes as $node) {
			$filename = $node->getAttribute('src');
			$size = getimagesize($filename);
			if($size[1] < 100 || $size[0] < 100){

				$node->setAttribute('layout','fixed');
			} else {
				$node->setAttribute('layout','responsive');
			}
			$node->setAttribute('height',$size[1]);
			$node->setAttribute('width',$size[0]);
		}

		//ads
		$nodes = $xpath->query('//amp-ad');
		foreach ($nodes as $node) {
			$node->setAttribute('layout','responsive');
			$node->setAttribute('width','300');
			$node->setAttribute('height','250');
			$node->setAttribute('type','adsense');
			$node->removeAttribute('data-ad-format');
		}


        //remove stuff with class="noamp"
		$nodes = $xpath->query("//*[contains(@class, 'noamp')]");
		foreach ($nodes as $node) {
			$node->parentNode->removeChild($node);
		}

		// remove rel attributes
		$nodes = $xpath->query('//*[@rel!=""]');
		foreach ($nodes as $node) {
			$node->removeAttribute('rel');
		}

		//remove inline styles
		$nodes = $xpath->query('//*[@style!=""]');
		foreach ($nodes as $node) {
			$node->removeAttribute('style');
		}

		// Output the HTML of our container
		echo $doc->saveHTML();
		?>
	</body>
</html>
