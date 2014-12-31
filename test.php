<?php
/**
 * @package   Warp Theme Framework
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

$url = $_SERVER['REQUEST_URI'];
if ( isset( $_GET['start'] ) ) {
    $queryStart = strpos($url, '?');
    $domainEnd = strpos($url, '/');
    $clip = substr($url, $domainEnd, $queryStart);
    $query = str_replace($clip, '', $url);
}

if(!$error) {
    if(array_key_exists('error',$_GET)) {
        $error = $_GET('error');
    }
}

?>

<!DOCTYPE HTML>
<html lang="<?php echo $this['config']->get('language'); ?>" dir="<?php echo $this['config']->get('direction'); ?>"  data-config='<?php echo $this['config']->get('body_config','{}'); ?>'>

<head>

    <base href="http://doit-qa.missouri.edu/" />
    <meta property="og:url" content="http://doit-qa.missouri.edu/" />
    <meta property="og:title" content="Home - Division of IT" />
    <meta property="og:type" content="website" />
    <meta property="og:description" content="The University of Missouri Division of Information Technology serves the IT needs of the UM System and Mizzou." />
    <meta name="description" content="The University of Missouri Division of Information Technology serves the IT needs of the UM System and Mizzou." />
    <title>Error! - Division of IT</title>
    <link href="/templates/yoo_master2/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon" />
    <link href="http://doit-qa.missouri.edu" rel="canonical" />
    <link rel="stylesheet" href="/components/com_k2/css/k2.css" type="text/css" />
    <script src="/media/system/js/mootools-core.js" type="text/javascript"></script>
    <script src="/media/jui/js/jquery.min.js" type="text/javascript"></script>
    <script src="/media/jui/js/jquery-noconflict.js" type="text/javascript"></script>
    <script src="/media/jui/js/jquery-migrate.min.js" type="text/javascript"></script>
    <script src="/media/system/js/core.js" type="text/javascript"></script>
    <script src="/components/com_k2/js/k2.js?v2.6.8&amp;sitepath=/" type="text/javascript"></script>

    <link rel="apple-touch-icon-precomposed" href="/templates/yoo_master2/apple_touch_icon.png">
    <link rel="stylesheet" href="/templates/yoo_master2/styles/doit/css/bootstrap.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" href="/templates/yoo_master2/styles/doit/css/responsiveslides.css">
    <link rel="stylesheet" href="//www.google.com/uds/solutions/dynamicfeed/gfdynamicfeedcontrol.css">
    <link rel="stylesheet" href="/templates/yoo_master2/styles/doit/css/theme.css">
    <link rel="stylesheet" href="/templates/yoo_master2/styles/doit/css/custom.css">
    <script src="/templates/yoo_master2/styles/doit/js/jsapi.js"></script>
    <script src="/templates/yoo_master2/styles/doit/js/gfdynamicfeedcontrol.js"></script>
    <script src="/templates/yoo_master2/styles/doit/js/jquery.hoverIntent.r7.min.js"></script>
    <script src="/templates/yoo_master2/styles/doit/js/jquery.slidepanel.js"></script>
    <script src="/templates/yoo_master2/styles/doit/js/responsiveslides.min.js"></script>
    <script src="/templates/yoo_master2/styles/doit/js/dropdownmenu.js"></script>
    <script src="/templates/yoo_master2/styles/doit/js/jquery-ui.min.js"></script>
    <script src="/templates/yoo_master2/warp/vendor/uikit/js/uikit.js"></script>
    <script src="/templates/yoo_master2/warp/vendor/uikit/js/addons/autocomplete.js"></script>
    <script src="/templates/yoo_master2/warp/vendor/uikit/js/addons/search.js"></script>
    <script src="/templates/yoo_master2/warp/js/social.js"></script>
    <script src="/templates/yoo_master2/styles/doit/js/custom.js"></script>

</head>

<div class="uk-container">
    <header>
        <a id="skipnav" href="#mainContent" class="smallNote offLeft">Skip to main content</a>
        <?php if ($this['widgets']->count('toolbar-l + toolbar-r')) : ?>
            <div class="tm-toolbar uk-clearfix uk-hidden-small">

                <?php if ($this['widgets']->count('toolbar-l')) : ?>
                    <div class="uk-float-left"><?php echo $this['widgets']->render('toolbar-l'); ?></div>
                <?php endif; ?>

                <?php if ($this['widgets']->count('toolbar-r')) : ?>
                    <div class="uk-float-right"><?php echo $this['widgets']->render('toolbar-r'); ?></div>
                <?php endif; ?>

            </div>
        <?php endif; ?>

        <?php if ($this['widgets']->count('logo + headerbar')) : ?>
            <div class="tm-headerbar uk-clearfix uk-hidden-small">

                <?php if ($this['widgets']->count('logo')) : ?>
                    <a class="tm-logo" title="<?php echo $this['config']->get('site_name') . ' Home'; ?>" href="<?php echo $this['config']->get('site_url'); ?>"><?php echo $this['widgets']->render('logo'); ?></a><a href="/index.php?option=com_xmap&view=html&id=1 #xmap" class="navButton" data-slidepanel="panel" title="Open Navigation"></a>
                <?php endif; ?>

                <?php if ($this['widgets']->count('menu + search')) : ?>
                    <nav class="tm-navbar uk-navbar">

                        <?php if ($this['widgets']->count('menu')) : ?>
                            <?php echo $this['widgets']->render('menu'); ?>
                        <?php endif; ?>

                        <?php if ($this['widgets']->count('banner')) : ?>
                            <a href="#banner" class="uk-navbar-toggle uk-visible-small" data-uk-banner></a>
                        <?php endif; ?>

                        <?php if ($this['widgets']->count('search')) : ?>
                            <div class="uk-navbar-flip">
                                <div class="uk-navbar-content uk-hidden-small"><?php echo $this['widgets']->render('search'); ?></div>
                            </div>
                        <?php endif; ?>

                        <?php if ($this['widgets']->count('logo-small')) : ?>
                            <div class="uk-navbar-content uk-navbar-center uk-visible-small"><a class="tm-logo-small" href="<?php echo $this['config']->get('site_url'); ?>"><?php echo $this['widgets']->render('logo-small'); ?></a></div>
                        <?php endif; ?>

                    </nav>
                <?php endif; ?>
                <div class="topRight">
                    <div class="uk-panel">
                        <p><span id="feedControl">Loading...</span></p>
                    </div>
                    <div class="uk-panel">
                        <form id="suggestionForm" role="search" action="/search" method="get" name="searchForm">
                            <div id="searchTools">
                                <label class="offLeft" for="searchBox" data-role="none">Search</label>
                                <input id="searchBox" type="search" autocomplete="on" maxlength="256" placeholder="search..." name="q" data-role="none">
                                <input id="searchSubmitButton" type="submit" value="Go" name="btnG" data-role="none">
                            </div>
                        </form>
                    </div>
                    <div class="uk-panel">
                        <?php
                        function findAvailableReps() {
                            $available = false;
                            $url = 'https://remote.missouri.edu/api/command.ns?username=TechSupportChat&password=8$54%RGx8cGx&action=get_logged_in_reps';
                            $url2 = 'https://remote.missouri.edu/api/command.ns?username=TechSupportChat&password=8$54%RGx8cGx&action=get_support_teams&showmembers';
                            $reps = simplexml_load_file($url);
                            $supportTeams = simplexml_load_file($url2);
                            $repIds = array();
                            for($i=0;$i<$reps->count();$i++) {
                                foreach($reps->rep[$i]->attributes() as $repId) {
                                    $repIds[$i] = $repId;
                                }
                            }
                            foreach($supportTeams as $supportTeam) {
                                if($supportTeam->name == 'Tech Support') {
                                    for($i=0;$i<$supportTeam->members->representative->count();$i++) {
                                        foreach($supportTeam->members->representative[$i]->attributes() as $supportTeamRepId) {
                                            $supportTeamRepIds[$i] = $supportTeamRepId;
                                        }
                                    }
                                }
                            }
                            foreach($repIds as $repId) {
                                foreach($supportTeamRepIds as $supportTeamRepId) {
                                    if((int)$repId == (int)$supportTeamRepId) {
                                        $available = true;
                                        return $available;
                                    } else {
                                        $available = false;
                                    }
                                }
                            }
                            return $available;
                        }
                        if (findAvailableReps()) {
                            echo '<button type="button" data-role="none" id="liveChat" title="(opens in a new window)" onclick="window.open(\'https://remote.missouri.edu/api/start_session.ns?issue_menu=1&amp;id=1&amp;c2cjs=1\',\'chat\', \'height=360,width=480\');">Live Chat!</button>';
                        } else {
                            echo '<button type="button" data-role="none" id="liveChatDisabled" class="btn-primary" disabled>Live Chat Closed</button>';
                        }
                        ?>
                    </div>
                </div>

            </div>
        <?php endif; ?>


    </header>

    <div class="uk-vertical-align-middle uk-container-center">

        <div class="centeredError">

            <i class="tm-error-icon uk-icon-frown-o"></i>
            <?php
            if (strtolower($error) == '404') {
                $title = 'This is not the page you\'re looking for.';
                $message = 'We\'ve recently updated our site, so old bookmarks may no longer work. Try returning to the <a href="/">Home page</a> and looking again. Our best guesses at what you\'re looking for are below:';
            } elseif(strtolower($error) == '403') {
                $title = 'You shall not pass!';
                $message = 'Either the username or password was incorrect. Try again.';
            } elseif(strtolower($error) == '500') {
                $title = 'It would be illogical to assume that all conditions remain stable.';
                $message = 'Something on the server has gone wrong somewhere. <a href="mailto:doit@missouri.edu">Contact</a> the webmaster and let us know what you were doing when this all started. Then you can head back to the <a href="/">Home page</a>.';
            }
            ?>
            <h1 class="tm-error-headline"><?php echo $error; ?></h1>

            <h2 class="uk-h3 uk-text-muted"><?php echo $title; ?></h2>

            <p><?php echo $message; ?></p>

        </div>
        <?php
        echo '<div id="k2Container" class="tm-content">
			<div class="itemBody">
				<div class="search-results">';
        $path = pathinfo($url, PATHINFO_DIRNAME);
        $file = preg_replace('/\\.[^.\\s]{2,5}$/', '', basename($url));
        $searchTerm = str_replace(array('/','-','_'),' ',$path) . ' ' . $file;
        $gsaQuery = 'http://search.missouri.edu/search?';

        $searchParams = array(
            'site'            => 'doit',
            'proxystylesheet' => 'doit_site',
            'client'          => 'doit_site',
            'output'          => 'xml_no_dtd',
            'proxyreload'	  => 1
        );

        // Add search inputs to query array
        $searchParams['q'] = $searchTerm;
        if ( isset($_GET['start'])  ) { $searchParams['start']  = $_GET['start'];  }
        if ( isset($_GET['sort'])   ) { $searchParams['sort']   = $_GET['sort'];   }
        if ( isset($_GET['filter']) ) { $searchParams['filter'] = $_GET['filter']; }
        $gsaQuery .= http_build_query($searchParams);

        // get results
        $ch = @curl_init();
        @curl_setopt( $ch, CURLOPT_URL, $gsaQuery );
        @curl_setopt( $ch, CURLOPT_HEADER, 0 );
        return @curl_exec( $ch ); // this echoes the results
        @curl_close( $ch );
        echo '<div>
			</div>
		</div>

		<footer class="tm-footer">
			<a class="tm-totop-scroller" data-uk-smooth-scroll="" href="#"></a>

			<div class="uk-panel">
				<p class="black"><a href="/" id="footerLogo" title="DoIT Home Page"></a> | <a href="http://www.missouri.edu/" id="muLogo" title="MU Home Page"></a> | <a href="http://www.umsystem.edu/" id="systemLogo" title="UM System Home Page"></a></p>
				<h1><a href="/site-map">Site Map</a></h1>
				<p>� 2014 Curators of the University of Missouri. <a href="http://www.missouri.edu/dmca/" target="_blank" title="(opens in a new window)">DMCA</a> and <a href="http://missouri.edu/statements/copyright.php" target="_blank" title="(opens in a new window)">other copyright information</a>. All rights reserved. An <a href="http://missouri.edu/statements/eeo-aa.php" title="(opens in a new window)">equal opportunity/affirmative action</a> institution.</p>
			</div>
			<div class="uk-panel">
				<h1><a href="/about">About DoIT</a></h1>
				<h2><a href="mailto:doit@missouri.edu?subject=Email%20from%20Contact%20Link">Contact</a></h2>
				<p><a href="http://www.missouri.edu" title="MU home">University of Missouri</a>&nbsp;/&nbsp;<a href="http://umsystem.edu" title="UM System">UM System</a><br><a href="/about" title="Division of IT">Division of Information Technology</a><br> 615 Locust St.,&nbsp;Columbia,&nbsp;MO,&nbsp;65211<br><a title="Dial 573.882.5000" class="nowrap" href="tel:+15738825000">573.882.5000</a><br><a href="http://www.facebook.com/MUDoIT" target="_blank" title="Division of IT - Facebook - (opens in a new window)"><span id="fb"></span></a><span class="black">&nbsp;|&nbsp;</span><a href="http://twitter.com/MUdoIT" target="_blank" title="Division of IT - Twitter - (opens in a new window)"><span id="tw"></span></a></p>
			</div>
		</footer>
	</div>';
        ?>
        </body>
</html>