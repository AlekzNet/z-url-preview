<?php

class ZLinkPreview {

    var $description;
    var $title;
    var $image = array();
    var $url;
    var $html;
    var $parsemode;
    var $curlerrno;
    var $curlerr;
    var $curlinf = array();
    var $htmlblank;

    function __construct($url) {
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "https://" . $url . '/';
        }
        $this->url = $url;
        $this->getHTML();
    }

    function setParseMode($m = "r") {
    	$this->parsemode = $m;
    }

    function getHTML() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);

        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
	curl_setopt($ch, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.119 Safari/537.36");
        curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . "/cacert.pem");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $this->html = curl_exec($ch);
		$this->curlinf = curl_getinfo($ch);
		$this->curlerr = curl_error($ch);
		$this->curlerrno = curl_errno($ch);
//	print_r($this->curlinf);
        if (!$this->html) {
            //echo 'Error: ' . curl_error($ch);
            //die();
            $this->htmlblank = true;
        }
        curl_close($ch);
//        $this->html = str_replace("<head>", "<head><base href=\"$this->url\">", $this->html);
    }

    function getcurlerrno() {
        echo $this->curlerrno;
    }

    function getcurlerr() {
        echo $this->curlerr;
    }

    function getcurlinf() {
        print_r($this->curlinf);
    }

    function getDescription() {
        if ($this->parsemode == "d") {
            $res = "";
            $dom = new DOMDocument();
            @$dom->loadHTML($this->html);
            foreach($dom->getElementsByTagName('meta') as $meta) {  // prefer og:description
                if ($meta->getAttribute('property') == "og:description") {
                    $res = $meta->getAttribute('content');
                    break;
                }
            }
            if ($res == "") {  // failback to basic description
                foreach($dom->getElementsByTagName('meta') as $meta) {
                    if ($meta->getAttribute('name') == "description") {
                        $res = $meta->getAttribute('content');
                        break;
                    }
                }
            }
            if ($res == "") {  // failback to first p if meta's missing or blank
                $res = $dom->getElementsByTagName('p')->item(0)->nodeValue;
            }
            echo $res;
        } else {
            if (preg_match_all('/<meta(?=[^>]*name="description")\s[^>]*content="([^>]*)"/si', $this->html, $matches)) {
                foreach ($matches[1] as $key => $content) {
                    echo $content;
                }
            } else if (preg_match_all('/<meta(?=[^>]*name="og:description")\s[^>]*content="([^>]*)"/si', $this->html, $matches)) {
                foreach ($matches[1] as $key => $content) {
                    echo $content;
                }
            }
        }
    }

    function getTitle() {
        if ($this->parsemode == "d") {
            $title = "";
            $dom = new DOMDocument();
            @$dom->loadHTML($this->html);
            foreach($dom->getElementsByTagName('meta') as $meta) {  // prefer og:title
                if ($meta->getAttribute('property') == "og:title") {
                    $title = $meta->getAttribute('content');
                    break;
                }
            }
            if ($title == "") {  // failback to title if og:title missing or blank
                $title = $dom->getElementsByTagName('title')->item(0)->nodeValue;
            }
            if ($title == "") {  // failback to h1 if title missing or blank
                $title = $dom->getElementsByTagName('h1')->item(0)->nodeValue;
            }
            echo $title;
        } else {
            // if (preg_match("/<title>(.+)<\/title>/si", $this->html, $matches)) { // Changed due to issue with BBC news
            if (preg_match("/<title>(.+)<\/title>/i", $this->html, $matches)) {
                echo $matches[1];
            } else {
                $dom = new DOMDocument();
                @$dom->loadHTML($this->html);
                echo $dom->getElementsByTagName('title')->item(0)->nodeValue;
            }
        }
    }

    function getImage($multiple = false) {
        if ($this->parsemode == "d") {
                $res = "";
                $dom = new DOMDocument();
                @$dom->loadHTML($this->html);
                foreach($dom->getElementsByTagName('meta') as $meta) {  // prefer og:image
                        if ($meta->getAttribute('property') == "og:image") {
                                $res = $meta->getAttribute('content');
                                break;
                        }
                }
                if ($res == "") {  // failback to first img if og:image missing or blank
                        $res = @$dom->getElementsByTagName('img')->item(0)->getAttribute('src');
                }
                if ($res != "") {  // only try and clean up the url if an image was found
                        // we need the fqdn without the trailing /
                        $urlo = rtrim ($this->url,"/");

                        $res = preg_replace("/&#?[a-z0-9]{2,8};/i", "", $res);
                        if (substr($res, 0, 4) == "http") { // if the url starts with http we're done
                                $reso = $res;
                        } else {
                                if (substr($res, 0, 1) == "/") {  // if url starts with / then it could be an absolute path
                                        if (substr($res, 0, 2) == "//") {  // ok, not absolute, but for dual-mode http(s) sites
                                                $reso = "http:" . $res;
                                        } elseif (substr($res, 0, 3) == "://") {  // for dual-mode http(s) sites with :
                                                $reso = "http" . $res;
                                        } else {  // absolute to prepend fqdn
                                                $reso = $urlo . $res;
                                        }
                                } else {  // doesn't start with a / so a relative path - for now assume a / base path
                                        $reso = $urlo . "/" . $res;
                                }
                        }
                }
                echo $reso;
        } else {
            /* First we will check if facebook opengraph image tag exist */
            if (preg_match_all('/<meta(?=[^>]*property="og:image")\s[^>]*content="([^>]*)"/si', $this->html, $matches)) {
                foreach ($matches[1] as $key => $content) {
                    $image[] = preg_replace("/&#?[a-z0-9]{2,8};/i", "", $content);
                    if ($key == 5)
                        break;
                }
            }

            /* If not then we will get the first image from the html source */
            else if (preg_match_all('/<img [^>]*src=["|\']([^"|\']+)/i', $this->html, $matches)) {
                foreach ($matches[1] as $key => $value) {
                    if (strpos($value, 'http') === false) {
                        // If trailing slash is missing from domain AND image path does not start with slash, insert one - technically should check for base href, but later :-)
                        if ((substr($this->url, -1) != "/") && (substr($value, 0, 1) != "/")) {
                            $image[] = $this->url . '/' . preg_replace("/&#?[a-z0-9]{2,8};/i", "", $value);
                        } else {
                            $image[] = $this->url . preg_replace("/&#?[a-z0-9]{2,8};/i", "", $value);
                        }
                    } else {
                        $image[] = preg_replace("/&#?[a-z0-9]{2,8};/i", "", $value);
                    }

                    if ($key == 5)
                        break;
                }
            }
            $image_index = (isset($_GET['image_no'])) ? $_GET['image_no'] - 1 : 0;
            echo (!$multiple) ? $image[$image_index] : str_replace(array("\\", "\"", " "), array("", "", ""), json_encode($image));
        }
    }

}
//print_r($_GET);
if ((strpos($_GET['url'], "<") === false) && (strpos($_GET['url'], ">") === false)) {
    if (filter_var($_GET['url'], FILTER_VALIDATE_URL)) {
        $zlinkPreview = new ZLinkPreview($_GET['url']);
        define('SHORTINIT', true);
        require_once('../../../wp-load.php');
        $linkmode = get_option('zurlpreview_linkmode');
        switch ($linkmode) {
            case "target-blank":
                $linkmodehtml = ' target="_blank"';
                break;
            case "target-newwindow":
                $linkmodehtml = ' target="newwindow"';
                break;
            case "rel-external":
                $linkmodehtml = ' rel="external"';
                break;
            default:
                $linkmodehtml = '';
        }
        $zlinkPreview->setParseMode(get_option('zurlpreview_parsemode'));
?>
<div class="at_zurlpreview">
            <?php
            if ($zlinkPreview->htmlblank == true) {
            ?>
            <p class="imgd">Error No: <?php $zlinkPreview->getcurlerrno();  ?></p>
            <p class="imgd">Error: <?php $zlinkPreview->getcurlerr();  ?></p>
            <p class="imgd">Info: <?php $zlinkPreview->getcurlinf();  ?></p>
            <?php
            } else {
            ?>
            <?php
            if (get_option('zurlpreview_noheadtag') != "Yes") {
	           	if (get_option('zurlpreview_linkheader') == "Yes") {
					?>
					<h2><a href="<?php echo $zlinkPreview->url; ?>" <?php echo $linkmodehtml; ?>><?php $zlinkPreview->getTitle();  ?></a></h2>
					<?php
            	} else {
					?>
					<h2><?php $zlinkPreview->getTitle();  ?></h2>
					<?php
            	}
            }
			?>
			<h3 style="display:none;"><?php $zlinkPreview->getTitle();  ?></h3>
			<?php
            if (get_option('zurlpreview_noimage') != "Yes") {
	           	if (get_option('zurlpreview_linkimage') == "Yes") {
					?>
					<p class="imgp"><a href="<?php echo $zlinkPreview->url; ?>" <?php echo $linkmodehtml; ?>><img data-src = "<?php $zlinkPreview->getImage(1); ?>" src="<?php $zlinkPreview->getImage();  ?>"></a></p>
					<?php
            	} else {
					?>
					<p class="imgp"><img data-src = "<?php $zlinkPreview->getImage(1); ?>" src="<?php $zlinkPreview->getImage();  ?>"></p>
					<?php
            	}
            }
			if (get_option('zurlpreview_nointro') != "Yes") {
			?>
			<p class="imgd"><?php $zlinkPreview->getDescription();  ?></p>
			<?php
			}
			if (get_option('zurlpreview_titlelink') == "Yes") {
			?>
			<p class="imgs"><a href="<?php echo $zlinkPreview->url; ?>" <?php echo $linkmodehtml; ?>><?php echo htmlspecialchars($zlinkPreview->getTitle());  ?></a></p>
			<?php
			} else {
			?>
			<p class="imgs"><?php echo get_option('zurlpreview_linktxt'); ?> <a href="<?php echo $zlinkPreview->url; ?>" <?php echo $linkmodehtml; ?>><?php echo preg_replace('#^https?://#', '', $zlinkPreview->url);  ?></a></p>
			<?php
			}
            ?>

            <?php } ?>
</div>
<?php
    } else {
?>
<div class="at_zurlpreview">
    <p>URL validation failed.</p>
</div>
<?php
    }
} else {
?>
<div class="at_zurlpreview">
    <p>URL validation failed (Possible XSS).</p>
</div>
<?php
}
