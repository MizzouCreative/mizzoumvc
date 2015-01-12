<?php
/**
 *
 * @package WordPress
 * @subpackage Mizzou MVC
 * @category theme
 * @category model
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 *
 * ASSUMES that Base.php and Site.php classes have already been included
 *
 * @todo rename to Framework to better reflect it's purpose?
 */

class Content {
    /**
     * @var array default options used by the render method
     */
    protected static $aryDefaultOptions = array(
        'include_sidebars'  => false,
        'override_outerview'=>false,
        'include_pagination'=>false,
        'include_header'    =>true,
        'include_footer'    =>true,
    );

    /**
     * @var array list of pages that should include a sidebar
     * @todo this is completely specific to IPP and needs to be moved back down to the child theme and then sent back up
     * via the theme options class
     */
    protected static $aryIncludeSidebarPages = array(
        'about',
        'strategic-plan',
        'annual-reports',
        'contact',
        'staff',
        'policy-research-scholars',
        'graduate-research-assistants'
    );

    /**
     * @var array
     */
    protected static $arySiteSiteMembers = array('URL','Name');

    /**
     * @var string
     */
    protected static $strDateArchiveType = '';

    /**
     * @var string
     */
    protected static $strPageTitle = '';

    /**
     * @var string
     */
    protected static $strHeaderTitle = '';

    /**
     * @var null
     */
    protected static $objPagePostType = null;

    /**
     *
     */
    protected static $objViewEngine = null;

    /**
     *
     */
    protected static $objView = null;
    /**
     * @param string $strInnerViewFileName
     * @param array $aryData
     * @param array $aryOptions
     */
    public static function render($strInnerViewFileName,$aryData,$aryOptions=array())
    {

        $aryViewVariables               = $aryData;
        $strEditPostLink                = '';
        $boolIncludeNoIndex             = false;
        $boolIncludeSidebar             = false;
        $boolIncludeImageAboveHeader    = false;

        /**
         * @todo refactor to remove pre-twig code
         */
        $aryViewVariables['EditPostLink']           = $strEditPostLink;
        $aryViewVariables['IncludeNoIndex']         = $boolIncludeNoIndex;
        $aryViewVariables['IncludeSidebar']         = $boolIncludeSidebar;
        $aryViewVariables['IncludeImageAboveHeader']= $boolIncludeImageAboveHeader;


        $aryOptions = array_merge(self::$aryDefaultOptions,$aryOptions);
        //_mizzou_log($aryOptions,'aryOptions after I merged');
        //extract($aryData);



        /**
         * @todo don't like this since it creates a direct dependency, but we need data from the Site model in order
         * to know what to pass/not pass to the view. Dependency injection via render method? or
         */
        if(!isset($objSite) || !is_object($objSite)){
            $objSite = new Site();
        }

        self::$objViewEngine = self::_initializeViewEngine();

        /**
         * @todo remove the twig debug filter before going to production
         */
        $objTwigDebug = new Twig_SimpleFilter('var_export',function($string){
           return PHP_EOL.'<pre>'.var_export($string,true).'</pre>'.PHP_EOL;
        });

        self::$objViewEngine->addFunction('header',new Twig_SimpleFunction('header','get_header'));
        self::$objViewEngine->addFunction('footer',new Twig_SimpleFunction('footer','get_footer'));

        /*$objTwigController = new Twig_SimpleFunction('controller',function($strName){

        });*/

        self::$objViewEngine->addFilter($objTwigDebug);

        //do we need the EditPostLink?
        if((is_single() || is_page()) && '' != $strPostLink = get_edit_post_link()){
            $strEditPostLink = ' ' . $strPostLink;
            $aryViewVariables['EditPostLink'] = $strEditPostLink;
        }

        /**
         * Page specific checks...
         * @todo this is specific to IPP and needs to be removed
         */
        if(is_page() && isset($objMainPost)){
            /**
             * @wp-hack
             * hack. we only want the sidebar on specific pages. change this into a function that determines if a sidebar is actually
             * needed
             */
            if(in_array($objMainPost->slug,self::$aryIncludeSidebarPages)){
                $boolIncludeSidebar = true;
                $aryViewVariables['IncludeSidebar'] = $boolIncludeSidebar;
            }

            if($objMainPost->image != ''){
                $boolIncludeImageAboveHeader = true;
                $aryViewVariables['IncludeImageAboveHeader'] = $boolIncludeImageAboveHeader;
            }
        }

        /**
         * This one is a bit of a bugger...
         * If we have access to the MainPost object AND either noindex or nolink is set and ON
         * OR
         * we're on a 404 page
         * then
         * we want to include the meta element for robots to not index the page
         * @todo this is header stuff and should be moved to the header controller
         *
         */
        if(
            is_404()
            || (
                isset($objMainPost)
                && (
                    (isset($objMainPost->noindex) && $objMainPost->noindex == 'on')
                    ||
                    (isset($objMainPost->nolink) && $objMainPost->nolink == 'on')
                )
            )
        ) {
            $boolIncludeNoIndex = true;
            $aryViewVariables['IncludeNoIndex'] = $boolIncludeNoIndex;
        }

        /**
         * @todo this needs to be moved either into a theme option or config file
         * deprecated?
         *
         */
        $intSpanWidth = ($boolIncludeSidebar) ? 8 : 12;
        $aryViewVariables['SpanWidth'] = $intSpanWidth;

        /**
         * For now, we want to make both the $objSite-> members and direct variables available to designers. Eventually
         * we'll decide one way or the other
         * @todo do we want to replicate this functionality in twig?
         */

        foreach($objSite->currentPublicMembers() as $strSiteKey){
            $strSiteVariable = '';
            if(in_array($strSiteKey,self::$arySiteSiteMembers)) {
                $strSiteVariable = 'Site'.$strSiteKey;
            } else {
                $strSiteVariable = $strSiteKey;
            }

            $aryViewVariables[$strSiteVariable] = $objSite->{$strSiteKey};

            $strSiteVariable = 'str'.$strSiteVariable;

            $$strSiteVariable = $objSite->{$strSiteKey};
        }

        $aryViewVariables['objSite'] = $objSite;

        //outerView needs breadcrumbs and inner view data
        /**
         * @todo the breadcrumbs plugin needs to be converted to a Model with a matching view
         * @todo How do we tell twig to include a subview?
         */
        //get the contents for the breadcrumbs
        /**
        if($objSite->IncludeBreadcrumbs){
            $strBreadCrumbs = $objSite->_captureOutput('breadcrumbs');
        } else {
            $strBreadCrumbs = '';
        }*/


        /**
         * @todo i dont like this one bit.  The situation we have is that it's possible for a controller to override
         * what the page title is, instead of us determining the page title.  But we also have methods here (specifically
         * self::_getHeaderTitle() and self::_determineHeaderTitle() that need the page title whether or not it has been
         * determined manually, or overridden. Going to need to think on this a bit...
         * the page title
         */
        //$strPageTitle = (isset($strPageTitle)) ? $strPageTitle : '';
        //$strPageTitle = self::_getPageTitle();
        if(!isset($aryViewVariables['PageTitle'])){
            $aryViewVariables['PageTitle'] = self::_getPageTitle();
        }

        /**
         * If we're on the home page (which is where the blog posts are listed), or we are on an archive page for any
         * other CPTs, then we need to include Next & Previous page links
         */
        if((is_home() || is_archive()) && $aryOptions['include_pagination']){
            $strPaginationNext = get_next_posts_link('&laquo; Previous Entries ');
            $strPaginationPrevious = get_previous_posts_link('Newer Entries &raquo;');
            if(is_null($strPaginationNext)) $strPaginationNext = '';
            if(is_null($strPaginationPrevious)) $strPaginationPrevious = '';

            $aryViewVariables['PaginationNext'] = $strPaginationNext;
            $aryViewVariables['PaginationPrevious'] = $strPaginationPrevious;
        }

        /**
         * Also temporary
         * @todo dont let this go to production
         * @todo this should be moved into the header controller
         */
        if(!isset($aryViewVariables['HeadTitle'])){
            $aryViewVariables['HeadTitle']= self::_getHeaderTitle($aryViewVariables['PageTitle'],$objSite->Name);
        }


        /**
         * check the view name to see if we've been given the full name w/ extension, or just the file name
         */
        if(!preg_match('/\.[a-z]{2,4}/',$strInnerViewFileName)){
            /**
             * @todo Is this an option that can be configured in twig? Or can we make this a config variable higher?
             */
            $strInnerViewFileName .= '.twig';
        }

        self::$objView = self::$objViewEngine->loadTemplate($strInnerViewFileName);
        _mizzou_log(self::$objViewEngine->getTemplateClass($strInnerViewFileName),'what is the template class for ' . $strInnerViewFileName);

        //now we need to start getting everyhing

        //_mizzou_log($strInnerView,'attempting to get: ');
        //get contents from the inner view
        /**
        if(!$aryOptions['override_outerview']){
            if(file_exists($strInnerView)){
                ob_start();
                require_once $strInnerView;
                $strInnerViewContent = ob_get_contents();
                ob_end_clean();
            } else {
                $strInnerViewContent = '<p>Unable to retrieve inner view.</p>';
            }
        }*/



        /**
         * @todo captureContents is in the site model so we need to expand it to allow for storage of this type of data
         */
        $strWpHeaderContents = $objSite->wpHeader;
        $aryViewVariables['strWpHeaderContents'] = $objSite->wpHeader;
        $strSearchFormContents = $objSite->SearchForm;
        $aryViewVariables['strSearchFormContents'] = $objSite->SearchForm;
        $strWpFooterContents = $objSite->wpFooter;
        $aryViewVariables['strWpFooterContents'] = $objSite->wpFooter;

        //start actual output

        // replaces get_header();
        //require_once $strViewsPath.'header.html';
        /**
         * @todo replace with a require to the sidebar view
         */
        /**
        if($boolIncludeSidebar) {
            get_sidebar();
        }*/
        /**
        if($aryOptions['override_outerview']){
            require_once $strInnerView;
        } else {
            require_once $strViewsPath . 'outerView.php';
        }


        // replaces get_footer();
        //require_once $strViewsPath . 'footer.html';
        require_once $strThemePath . 'footer.html';
        */

        echo self::$objView->render($aryViewVariables);
    }

    /**
     * @return string
     */
    protected function _getPageTitle()
    {
        if('' == self::$strPageTitle){
            self::_determinePageTitle();
        }

        return self::$strPageTitle;
    }
    /**
     * Determines H1 title for pages
     * @return string
     * @todo sub-page support, category/taxonomy support and pagination support
     */
    protected function _determinePageTitle()
    {
        $strPageTitle = '';
        if(is_archive()){
            _mizzou_log(post_type_archive_title(null,false),'we know we have an archive, here is the post_type_archive_title');
            if(is_date()){
                $strDateArchiveType = self::_getDateArchiveType();
                _mizzou_log($strDateArchiveType,'our archive date type');
                $aryDateParts = array();
                $strDatePattern = '';
                switch ($strDateArchiveType){
                    case 'day':
                        $aryDateParts[] = get_the_time('d');
                        $strDatePattern = ' %s,';
                    case 'month':
                        /**
                         * since it is possible that the day is already in the array, we need to make sure that month
                         * is pushed onto the beginning of the array no matter what, hence the array_unshift
                         */
                        array_unshift($aryDateParts,get_the_time('F'));
                        $strDatePattern = '%s'.$strDatePattern;
                    case 'year':
                        $aryDateParts[] = get_the_time('Y');
                        $strDatePattern .= ' %d';
                        break;
                }

                //_mizzou_log($strDatePattern,'our date pattern');
                //_mizzou_log($aryDateParts,'our date parts');

                $strPageTitle = vsprintf($strDatePattern,$aryDateParts);
                $objPagePostType = self::_getPagePostType();

                $strPageTitle .= ' ' . $objPagePostType->label;
                //_mizzou_log($strPageTitle,'we have a date archive. this is the date formatted title weve come up with');
            } else {
                $strPageTitle = post_type_archive_title(null,false);
                _mizzou_log($strPageTitle,'we are a non-dated archive. this is what was returned from post_type_archive_title');
                /**
                 * If it isn't a dated archive, has it been filtered by a taxonomy?
                 */
                global $wp_query;
                $objQueried = get_queried_object();
                if(is_object($objQueried) && count($wp_query->tax_query->queries) > 0){
                    $strPageTitle = $objQueried->name . ' ' . $strPageTitle;
                }
            }
        } else {
            $strPageTitle = wp_title('',false);
        }
        _mizzou_log($strPageTitle,'page title as determined',false,array('func'=>__FUNCTION__,'file'=>__FILE__));
        self::$strPageTitle = $strPageTitle;
    }

    /**
     * Originally this was built to be used to determine the paths for use in breadcrumbs and in the HeaderTitle. At this
     * point, it's future is unclear since the general consensus is to never use breadcrumbs
     *
     * Marking as deprecated until we know what we want to do with it
     *
     * @param $strPageTitle
     * @param string $strSiteName
     * @return array
     * @deprecated
     */
    protected function _determinePagePath($strPageTitle,$strSiteName='')
    {
        $aryPath = array();

        if(is_null($strPageTitle)){
            $strPageTitle = self::_determinePageTitle();
        } else {
            /**
             * since we need this in the header, and its possible for the controller to manually set the page title, we
             * need to make sure there isn't any html in the page title
             */
            $strPageTitle = strip_tags($strPageTitle);
        }

        if(is_archive() || is_single()){
            //ok, we have a lot of different archives to deal with. let's separate out the single
            $strPostType = get_post_type();

            if($strPostType != 'post'){
                $objPostType = get_post_type_object($strPostType);
                $strPostTypeName = $objPostType->labels->name;
                $strPostTypeURL = get_post_type_archive_link($strPostType);
            } else {
                /**
                 * @todo the name of the default post type should either be pulled dynamically or moved into the theme
                 * options so we can get it from there, not typed statically.
                 */
                $strPostTypeName = 'Blog';
                $strPostTypeURL = get_permalink(get_option('page_for_posts'));
            }

            if(is_single()){
                if(!empty($strPageTitle) && $strPageTitle != ''){
                    $aryPath[$strPageTitle] = null; //we dont include a URL for the page we are on
                }
                $aryPath[$strPostTypeName] = $strPostTypeURL;

            } else {
                /**
                 * ok, what type of archive are we dealing with. Also, seems silly that we have to check specifically for
                 * category and tag considering they are just default taxonomies and dont really differ.
                 */
                if(is_date()){
                    $strYear    = get_the_time('Y');
                    $strYearURL = null;
                    $strMonth   = get_the_time('m');
                    $strMonthURL= null;

                    $strDateArchiveType = self::_determineDateArchiveType();

                    switch($strDateArchiveType){
                        case 'day':
                            $aryPath[get_the_time('d')] = null;
                            $strMonthURL = get_month_link($strYear,$strMonth);
                            $strYearURL = get_year_link($strYear);
                        //pass-through done intentionally
                        case 'month':
                            /**
                             * $strMonth is the numeric representation of our month (e.g. 06), but we'll want it in text
                             * format
                             */
                            $objDate = DateTime::createFromFormat('!m',$strMonth);
                            $aryPath[$objDate->format('F')] = $strMonthURL;
                            if(is_null($strYearURL)) $strYearURL = get_year_link($strYear);
                        //pass-through done intentionally
                        case 'year':
                            $aryPath[$strYear] = $strYearURL;
                            break;

                        default:
                            /**
                             * @todo besides log, do we need to throw an exception?
                             */
                            _mizzou_log($strDateArchiveType,'we are in a date archive, but if failed day, month and year checks',false,array('func'=>__FUNCTION__));
                            break;
                    }

                } elseif(is_tax() || is_tag() || is_category()){
                    $strTerm = get_query_var('term');
                    $strTaxonomy = get_query_var('taxonomy');
                    $objTaxonomy = get_taxonomy($strTaxonomy);
                    $objTerm = get_term_by('slug',$strTerm,$strTaxonomy);
                    /**
                    _mizzou_log($strTerm,'the cat/tag/tax term');
                    _mizzou_log($strTaxonomy,'the cat/tag/tax name');
                    _mizzou_log($objTaxonomy,'the taxonomy object');
                    _mizzou_log($objTerm,'the taxonomy term object');
                     */
                    $aryPath[$objTerm->name] = null;

                }

                $aryPath[$strPostTypeName] = $strPostTypeURL;

            }
        } elseif(is_page()) {
            //do sub sub pages need to have that information reflected in the title?
        } else {
            //it's not an archive a single, or a page. what do we have left?
        }

        /**
         * @todo get this data from the site object
         */
        if($strSiteName == ''){
            $strSiteName = get_bloginfo('name');
        }

        /**
         * @todo get this data from the site object
         */
        $aryPath[$strSiteName] = home_url();

        /**
         * Inclusion of this information should be in a theme option
         * @todo include theme option for this
         */
        $aryPath['University of Missouri'] = 'http://missouri.edu/';

        return $aryPath;
    }

    /**
     * @return string
     */
    protected function _getDateArchiveType()
    {
        if('' == self::$strDateArchiveType){
            self::_determineDateArchiveType();
        }

        return self::$strDateArchiveType;
    }

    /**
     *
     */
    protected function _determineDateArchiveType()
    {
        $strDateArchiveType = '';

        if(is_day()){
            $strDateArchiveType = 'day';
        } elseif(is_month()){
            $strDateArchiveType = 'month';
        } elseif(is_year()){
            $strDateArchiveType = 'year';
        }

        self::$strDateArchiveType = $strDateArchiveType;
    }

    /**
     * @param $strPageTitle
     * @param $strSiteName
     * @return string
     */
    protected function _getHeaderTitle($strPageTitle,$strSiteName)
    {
        if('' == self::$strHeaderTitle){
            self::_determineHeaderTitle($strPageTitle,$strSiteName);
        }

        return self::$strHeaderTitle;
    }

    /**
     * @param $strPageTitle
     * @param $strSiteName
     */
    protected function _determineHeaderTitle($strPageTitle,$strSiteName)
    {
        $aryTitleParts = array();
        $strPageTitle = trim(strip_tags($strPageTitle));

        if('' != $strPageTitle){
            $aryTitleParts[] = $strPageTitle;
        }

        $objPostType = self::_getPagePostType();

        /**
         * @todo Should we check to see if it isnt null, or check to see if it is an object? We're making an assumption
         * right now that if it isnt null, then ->labels and ->labels->name have been set and are accessible
         */
        if(!is_null($objPostType) && $strPageTitle !== $objPostType->labels->name){
            $aryTitleParts[] = $objPostType->labels->name;
        }

        $aryTitleParts[] = $strSiteName;
        $aryTitleParts[] = 'University of Missouri';

        //_mizzou_log($aryTitleParts,'aryTitleParts right before we implode');
        /**
         * @todo implosion glue should come from a theme option
         */
        self::$strHeaderTitle = implode(' // ',$aryTitleParts);
    }

    /**
     * @return null
     */
    protected function _getPagePostType()
    {
        if(is_null(self::$objPagePostType)){
            self::_determinePagePostType();
        }

        return self::$objPagePostType;
    }

    /**
     *
     */
    protected function _determinePagePostType()
    {
        //$strPostType = get_post_type();

        /**
         * the normal method failed so fall back to a secondary method
         * @todo should we just do this method all the time instead of using it when get_post_type fails?
         */
        $strPostType = get_query_var('post_type');
        if(is_array($strPostType)){
            $strPostType = reset($strPostType);
        }

        if('' != $strPostType){
            self::_adjustPostTypeLabels($strPostType);
            self::$objPagePostType = get_post_type_object($strPostType);
        } else {
            /**
             * @todo we need to do something else here besides log. We have functionality further down the line that
             * depends on the PostType being determined.
             *
             * HOWEVER, there are perfectly valid scenarios where we dont have a post type. Front Page is one, Search
             */
            global $wp_query;
            //_mizzou_log($wp_query,'WARNING: We were unable to determine the post type we are dealing with. Here is wp_query',true);
        }
    }

    /**
     * @param $strPostType
     */
    protected function _adjustPostTypeLabels($strPostType)
    {
        /**
         * For the love of God, wordpress... why do you have such a hard-on for global variables????!?!#@#@!~$!@
         */
        global $wp_post_types;
        switch($strPostType){
            case 'post':
                /**
                 * @todo is there ever going to be a situation where the default post type is being used with its
                 * default labels and we DONT want to adjust the label? Or should we have a theme option here that
                 * allows us to define what the label should be for the default type?  This just seems too specific to
                 * the IPP website requirements.
                 */
                if(isset($wp_post_types[$strPostType]) && $wp_post_types[$strPostType]->labels->name == 'Posts'){
                    $wp_post_types[$strPostType]->labels->name = 'Blog';
                    $wp_post_types[$strPostType]->label = 'Blog Posts';
                }

                break;
        }

        //_mizzou_log($wp_post_types[$strPostType],'our post type after we adjusted the labels');
    }

    /**
     * 
     */
    protected function _includeTaxonomyMenu()
    {
        $aryPubMenu = array();
        global $wp_query; //stoopid wordpress globals
        $aryWPQuery = $wp_query->query;
        if(isset($aryWPQuery['post_type'])) unset($aryWPQuery['post_type']);
        $strCurrentKey = reset($aryWPQuery);
        $strCurrentTaxonomy = key($aryWPQuery);
        $aryTaxonomies = get_object_taxonomies(self::$objPagePostType->name,'objects');
        foreach($aryTaxonomies as $objTaxonomy){

        }
    }

    /**
     * Initializes Template engines filesystem loader
     * @return Twig_Loader_Filesystem
     *
     * @todo should this be here? We now have a direct dependency on a TWIG object. What if we want to change template
     * systems?
     */
    protected function _initializeViewLoader()
    {
        $aryViewDirectories = array();
        $strParentThemePath = get_template_directory().DIRECTORY_SEPARATOR;
        $strChildThemePath = get_stylesheet_directory().DIRECTORY_SEPARATOR;

        if($strChildThemePath != $strParentThemePath){
            $aryViewDirectories[] = $strChildThemePath;
        }

        $aryViewDirectories[] = $strParentThemePath;

        foreach($aryViewDirectories as $intDirectoryKey=>$strDirectory){
            $aryViewDirectories[$intDirectoryKey] = $strDirectory.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR;
        }

        return new Twig_Loader_Filesystem($aryViewDirectories);

    }

    protected function _initializeViewEngine()
    {
        $objTELoader = self::_initializeViewLoader();
        $strCacheLocation = self::_determineViewCacheLocation();
        return new Twig_Environment($objTELoader,array('cache'=>$strCacheLocation,'auto_reload'=>true));

    }

    protected function _determineViewCacheLocation()
    {
        $strViewCacheLocation = '';

        if(defined('VIEW_CACHE_LOCATION')){
            $strViewCacheLocation = VIEW_CACHE_LOCATION;
        } else {
            //let's see if we have a cache directory
            $strParentThemePath = get_template_directory().DIRECTORY_SEPARATOR;
            $strPossibleCacheLocation = $strParentThemePath.'cache'.DIRECTORY_SEPARATOR;
            if(!is_dir($strPossibleCacheLocation) && !file_exists($strPossibleCacheLocation)){
                //we need to make a directory
                if(mkdir($strPossibleCacheLocation,'0755')){
                    $strViewCacheLocation = $strPossibleCacheLocation;
                }
            } elseif(!is_writable($strPossibleCacheLocation)) {
                //it exists but we cant write to it...
                if(chmod($strPossibleCacheLocation,'0755')){
                    $strViewCacheLocation = $strPossibleCacheLocation;
                }
            } else {
                $strViewCacheLocation = $strPossibleCacheLocation;
            }

        }

        if(''==$strViewCacheLocation){
            /**
             * @todo we need a more elegant way of handling this
             */
            echo 'view cache location is not available or is not writeable. I can\'t continue until you fix this. ';exit;
        } else {
            return $strViewCacheLocation;
        }
    }
}