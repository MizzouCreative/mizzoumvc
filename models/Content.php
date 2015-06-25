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
        'return'            =>false,
	    'bypass_init'       =>false,
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

    protected static $intCounter = 0;
    /**
     * @param string $strInnerViewFileName
     * @param array $aryData
     * @param array $aryOptions
     * @return void
     */
    public static function render($strInnerViewFileName,$aryData,$aryOptions=array())
    {
        /**
         * @todo why are we setting $aryViewVariables to $aryData instead of just using $aryData?
         */
        $aryViewVariables               = $aryData;

        $aryOptions = array_merge(self::$aryDefaultOptions,$aryOptions);

        /**
         * @todo don't like this since it creates a direct dependency, but we need data from the Site model in order
         * to know what to pass/not pass to the view. Dependency injection via render method? or
         */
        if(!isset($aryData['objSite']) || !is_object($aryData['objSite'])){
            //if this is the first run-through and we havent asked to bypass init (shortcodes, usually)
	        if(self::$intCounter == 0 && !$aryOptions['bypass_init']){
                $objSite = new Site();
            } else {
                /**
                 * Somehow we've called Content more than once and still dont have an objSite in site.
                 * @todo clean this up
                 */
                _mizzou_log($aryData,'Content has been called ' . self::$intCounter . ' times but objSite still hasnt been created',false,array('line'=>__LINE__,'file'=>basename(__FILE__)));
            }
        } else {
            $objSite = $aryData['objSite'];
        }

        if(FALSE !== $aryOptions['include_pagination'] && $aryOptions['include_pagination'] instanceof WP_Query){
            $aryPaginationArgs = array('wp_query'=>$aryOptions['include_pagination']);
            unset($aryOptions['include_pagination']);
            if(('' != $aryPaginationOptions = $objSite->pagination)){
                $aryPaginationArgs = array_merge($aryPaginationArgs,$aryPaginationOptions);
            }

            $aryViewVariables['Pagination'] = new Pagination($aryPaginationArgs);
        } elseif(!($aryOptions['include_pagination'] instanceof WP_Query)){
            _mizzou_log($aryOptions['include_pagination'],'you said you wanted to do pagination, but you didnt give me a WP_Query object',false,array('line'=>__LINE__,'file'=>__FILE__));
        }


	    /**
	     * Load up the template rendering engine
	     */
	    self::_initializeViewEngine();

		//do we need the EditPostLink?
        if(((is_single() || is_page()) && '' != $strPostLink = get_edit_post_link()) && !$aryOptions['bypass_init']){
            $strEditPostLink = ' ' . $strPostLink;
            $aryViewVariables['EditPostLink'] = $strEditPostLink;
        }



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

        /**
         * @todo i dont like this one bit.  The situation we have is that it's possible for a controller to override
         * what the page title is, instead of us determining the page title.  But we also have methods here (specifically
         * self::_getHeaderTitle() and self::_determineHeaderTitle() that need the page title whether or not it has been
         * determined manually, or overridden. Going to need to think on this a bit...
         * the page title
         */
        //$strPageTitle = (isset($strPageTitle)) ? $strPageTitle : '';
        //$strPageTitle = self::_getPageTitle();
        if(!isset($aryViewVariables['PageTitle']) && self::$intCounter == 0 && !$aryOptions['bypass_init']){
            $aryViewVariables['PageTitle'] = self::_getPageTitle();
        }

        if(!isset($aryData['RootAncestor']) && self::$intCounter == 0 && !$aryOptions['bypass_init']){
            $aryViewVariables['RootAncestor'] = self::_determineRootAncestor((isset($aryData['objMainPost'])) ? $aryData['objMainPost'] : null,$aryViewVariables['PageTitle']);
        }
        /**
         * If we're on the home page (which is where the blog posts are listed), or we are on an archive page for any
         * other CPTs, then we need to include Next & Previous page links
         * @deprecated this has been moved into the Pagination model.
         * @todo delete

        if((is_home() || is_archive()) && $aryOptions['include_pagination']){
            $strPaginationNext = get_next_posts_link('&laquo; Previous Entries ');
            $strPaginationPrevious = get_previous_posts_link('Newer Entries &raquo;');
            if(is_null($strPaginationNext)) $strPaginationNext = '';
            if(is_null($strPaginationPrevious)) $strPaginationPrevious = '';

            $aryViewVariables['PaginationNext'] = $strPaginationNext;
            $aryViewVariables['PaginationPrevious'] = $strPaginationPrevious;
        }*/


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

        /**
         * Now we need the data from our menu model
         */
        if(!isset($aryData['Menu']) && self::$intCounter == 0 && !$aryOptions['bypass_init']){
            if(self::$intCounter == 0){
                $aryViewVariables['Menu'] = new Menu($aryViewVariables);
            } else {
                _mizzou_log($aryData,'Content has been called ' . self::$intCounter . ' times but Menu still hasnt been created',false,array('line'=>__LINE__,'file'=>basename(__FILE__)));
            }
        }

        //increment our internal counter, but only if we havent instructed it to bypass init
        if(!$aryOptions['bypass_init']) ++self::$intCounter;

        if($aryOptions['return']){
            return self::$objView->render($aryViewVariables);
        } else {
            echo self::$objView->render($aryViewVariables);
        }

    }

    protected static function _determineRootAncestor($objMainPost=null,$strPageTitle='')
    {
        $strReturn = '';

        if(is_page()){
            //if it's a page, then it should have been converted into a MizzouPostObject
            if(!is_null($objMainPost)){
                $aryAncestors = $objMainPost->retrieveAncestors();
                if(count($aryAncestors) > 0){
                    $strReturn = end($aryAncestors);
                } else {
                    // it doesnt have any ancestors so it is the root
                    $strReturn = $objMainPost->title;
                }

            } else {
                //should we replicate the functionality here? or just log?
                _mizzou_log(null,'hey, youre on a page, but you didnt convert it to a MizzouPost first!',false,array('line'=>__LINE__,'file'=>basename(__FILE__),'func'=>__FUNCTION__));
                $aryAncestorIDs = get_post_ancestors(get_the_ID());
                if(count($aryAncestorIDs) > 0){
                    $intRootAncestor = end($aryAncestorIDs);
                    $strReturn = get_the_title($intRootAncestor);
                } elseif($strPageTitle != '') {
                    $strReturn = $strPageTitle;
                }
            }
        } elseif(is_front_page()) {
            $strReturn = "Home";
        } elseif(is_tax() || is_tag() || is_category()){
			global $wp_query;
	        if(isset($wp_query->query_vars['taxonomy'])){
				$objTaxonomy = get_taxonomy($wp_query->query_vars['taxonomy']);
		        if(false !== $objTaxonomy && isset($objTaxonomy->label) && '' != $objTaxonomy->label){
			        $strReturn = $objTaxonomy->label;
		        } else {
			        $strMsg = 'trying to get the label for taxonomy '. $wp_query->query_vars['taxonomy'] .'but the label isnt set or is empty in the taxonomy object';
			        _mizzou_log($objTaxonomy,$strMsg,false,array('line'=>__LINE__,'file'=>__FILE__));
		        }
		    } else {
		        $strMsg = 'we\'re on a taxonomy archive page, yet taxonomy property isnt set in wp_query';
		        _mizzou_log($wp_query->query_vars,$strMsg,false,array('line'=>__LINE__,'file'=>__FILE__));
	        }
	    } elseif(is_404()){
            $strReturn = 'Error 404, Page Not Found';
        } else {
            //what other situations do we have besides a page and everything else?
            if(FALSE !== $strPostType = get_post_type()){
                $objPostType = get_post_type_object($strPostType);
                if(is_object($objPostType) && isset($objPostType->labels->name)) {
	                $strReturn = $objPostType->labels->name;
                } else {
					$strMsg = 'tried to get the post object for ' . $strPostType . ' but I didnt receive what I was expecting';
	                _mizzou_log($objPostType,$strMsg,false,array('line'=>__LINE__,'file'=>__FILE__));
                }
	        }
        }

        return $strReturn;
    }

    /**
     * @return string
     */
    protected static function _getPageTitle()
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
     * @todo shouldnt this be moved into the Header model?
     */
    protected static function _determinePageTitle()
    {
        $strPageTitle = '';
        if(is_archive()){
	        global $wp_query;
            //_mizzou_log(post_type_archive_title(null,false),'we know we have an archive, here is the post_type_archive_title');
            if(is_date()){
                $strDateArchiveType = self::_getDateArchiveType();
                //_mizzou_log($strDateArchiveType,'our archive date type');
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
	            //_mizzou_log($objPagePostType,'objPagePostType',false,array('line'=>__LINE__,'file'=>__FILE__));
                $strPageTitle .= ' ' . $objPagePostType->labels->name;
                //_mizzou_log($strPageTitle,'we have a date archive. this is the date formatted title weve come up with',false,array('line'=>__LINE__,'file'=>__FILE__));

            } else {
                $strPageTitle = post_type_archive_title(null,false);
                _mizzou_log($strPageTitle,'we are a non-dated archive. this is what was returned from post_type_archive_title');
                /**
                 * If it isn't a dated archive, has it been filtered by a taxonomy?
                 */
                $objQueried = get_queried_object();
                if(is_object($objQueried) && count($wp_query->tax_query->queries) > 0){
                    $strPageTitle = ($strPageTitle == '') ? $objQueried->name : $objQueried->name . ' ' . $strPageTitle;
                }
            }

	        //now, are we in the midst of pagination?
	        _mizzou_log($wp_query,'wp_query is paged set?',false,array('line'=>__LINE__,'file'=>__FILE__));
	        if(isset($wp_query->query_vars['paged']) && $wp_query->query_vars['paged'] != 0){
		        $strPageTitle .= ', Page ' . $wp_query->query_vars['paged'];
	        }
        } else {
            $strPageTitle = wp_title('',false);
        }
        _mizzou_log($strPageTitle,'page title as determined',false,array('func'=>__FUNCTION__,'file'=>__FILE__));
        self::$strPageTitle = trim($strPageTitle);
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
    protected static function _determinePagePath($strPageTitle,$strSiteName='')
    {
        _mizzou_log(null,'deprecated function called',true,array('func'=>__FUNCTION__,'file'=>__FILE__));
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
    protected static function _getDateArchiveType()
    {
        if('' == self::$strDateArchiveType){
            self::_determineDateArchiveType();
        }

        return self::$strDateArchiveType;
    }

    /**
     *
     */
    protected static function _determineDateArchiveType()
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
     * @deprecated moved into Header model @see Header()
     */
    protected static function _getHeaderTitle($strPageTitle,$strSiteName)
    {
        _mizzou_log(null,'deprecated function called!',true,array('func'=>__FUNCTION__,'file'=>basename(__FILE__)));
        if('' == self::$strHeaderTitle){
            self::_determineHeaderTitle($strPageTitle,$strSiteName);
        }

        return self::$strHeaderTitle;
    }

    /**
     * @param $strPageTitle
     * @param $strSiteName
     * @deprecated moved into Header model @see Header()
     */
    protected static function _determineHeaderTitle($strPageTitle,$strSiteName)
    {
        _mizzou_log(null,'deprecated function called!',true,array('func'=>__FUNCTION__,'file'=>basename(__FILE__)));
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
     * Returns the post type of the page/post we are currently rendering
     * @return string
     */
    protected static function _getPagePostType()
    {
        if(is_null(self::$objPagePostType)){
            self::_determinePagePostType();
        }

        return self::$objPagePostType;
    }

    /**
     * Determines the post type of the current page we are dealing with
     */
    protected static function _determinePagePostType()
    {
        //$strPostType = get_post_type();

        /**
         * the normal method failed so fall back to a secondary method
         * @todo should we just do this method all the time instead of using it when get_post_type fails?
         */
        $strPostType = get_query_var('post_type');
        if(is_array($strPostType)){
            $strPostType = reset($strPostType);
        } elseif(''==$strPostType){
	        //still empty, let's try get_post_type
	        $strPostType = get_post_type();
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
            _mizzou_log($wp_query,'WARNING: We were unable to determine the post type we are dealing with. Here is wp_query',true);
        }
    }

    /**
     * Adjusts the labels on the default post type
     *
     * @param $strPostType
     * @todo I believe this is deprecated, and is now being handled by an extending theme
     */
    protected static function _adjustPostTypeLabels($strPostType)
    {
	    _mizzou_log(null,'deprecated function called',true,array('func'=>__FUNCTION__,'file'=>__FILE__));
	    /**
         * For the love of God, wordpress... why do you have such a hard-on for global variables????!?!#@#@!~$!@
         */
        global $wp_post_types;
	    _mizzou_log($wp_post_types[$strPostType],'looking for labels on ' . $strPostType,false,array('func'=>__FUNCTION__,'file'=>__FILE__));
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
     * @deprecated
     */
    protected static function _includeTaxonomyMenu()
    {
	    _mizzou_log(null,'deprecated function called',true,array('func'=>__FUNCTION__,'file'=>__FILE__));
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
    protected static function _initializeViewLoader()
    {
        $aryViewDirectories = array();
        $strParentThemePath = get_template_directory().DIRECTORY_SEPARATOR;
        $strChildThemePath = get_stylesheet_directory().DIRECTORY_SEPARATOR;

        if($strChildThemePath != $strParentThemePath){
            $aryViewDirectories[] = $strChildThemePath;
        }

        $aryViewDirectories[] = $strParentThemePath;

        /**
         * Last include the path to the framework, if it was defined
         */
        if(defined('MIZZOUMVC_ROOT_PATH')) {
            $aryViewDirectories[] = MIZZOUMVC_ROOT_PATH;
        }

        foreach($aryViewDirectories as $intDirectoryKey=>$strDirectory){
            $aryViewDirectories[$intDirectoryKey] = $strDirectory.'views'.DIRECTORY_SEPARATOR;
        }

        return new Twig_Loader_Filesystem($aryViewDirectories);

    }

	/**
	 * Loads the template rendering engine
	 */
	protected static function _initializeViewEngine()
    {
        $objTELoader = self::_initializeViewLoader();
        $strCacheLocation = self::_determineViewCacheLocation();
        $boolAutoReload = (defined('WP_DEBUG')) ? WP_DEBUG : false;
        /**
         * @todo move this into an options setting?
         */
        $aryTEOptions = array(
            'cache'=>$strCacheLocation,
            'auto_reload'=>$boolAutoReload,
            'autoescape'=>false,
        );
	    self::$objViewEngine = new Twig_Environment($objTELoader,$aryTEOptions);
	    self::_loadVEFilters();
	    self::_loadVEFunctions();
	}

	/**
	 * Determines where we need to store the cache from our template rendering engine
	 *
	 * @return string cache location
	 */
	protected static function _determineViewCacheLocation()
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

	/**
	 * Registers functions with the template rendering engine
	 * @todo this really needs to be somewhere else instead of in the Content class
	 */
	protected static function _loadVEFunctions()
	{
		/**
		 * @todo this needs to be moved out of here into somewhere else.  But where?
		 */
		self::$objViewEngine->addFunction('subview',new Twig_SimpleFunction('subview',function($mxdControllerName,$aryContext,$aryData = array()){
			_mizzou_log($mxdControllerName,'the controller we were asked to get',false,array('func'=>__FUNCTION__,'file'=>__FILE__));
			//_mizzou_log($aryContext,'the context data that was passed in',false,array('func'=>__FUNCTION__,'file'=>__FILE__));
			$strController = '';

            if(is_array($mxdControllerName)){
				$aryControllerNameParts = $mxdControllerName;
			} elseif(is_string($mxdControllerName)){
				$aryControllerNameParts = explode(' ',trim($mxdControllerName));
			} else {
				/**
				 * @todo should this be changed to a try catch with an exception?
				 * We're expecting a string (or an array), so getting something else WOULD be an exception
				 */
				_mizzou_log($mxdControllerName,'what the heck... what were we given instead of the name for a controller?',false,array('FUNC'=>__FUNCTION__,'line'=>__LINE__,'file'=>__FILE__));
				$aryControllerNameParts = array();
			}
			$strControllerName = implode('-',$aryControllerNameParts) . '.php';
            _mizzou_log($strControllerName,'the controller name before we run locate template',false,array('func'=>__FUNCTION__,'file'=>__FILE__,'line'=>__LINE__));
			if(count($aryData) != 0){
				extract($aryData);
			}

            if('' == $strController = locate_template(($strControllerName)) && defined('MIZZOUMVC_ROOT_PATH')){
                _mizzou_log(null,'we didnt find a controller in a parent or child theme. gonna look in the plugin framework',false,array('line'=>__LINE__,'file'=>__FILE__));
                //ok, we didnt find a controller in a parent or child theme, what about the plugin?
                if(is_readable(MIZZOUMVC_ROOT_PATH.$strControllerName)){
                    $strController = MIZZOUMVC_ROOT_PATH.$strControllerName;
                } else {
                    _mizzou_log(MIZZOUMVC_ROOT_PATH.$strControllerName,'we couldnt find this controller in the framework either',false,array('line'=>__LINE__,'file'=>__FILE__));
                }
            }

			if('' != $strController){
				require_once $strController;
			}
		}));
	}

	/**
	 * Registers filters with the template rendering engine
	 */
	protected static function _loadVEFilters()
	{
		$objTwigDebug = new Twig_SimpleFilter('var_export',function($string){
			return PHP_EOL.'<pre>'.var_export($string,true).'</pre>'.PHP_EOL;
		});

		$objTwigSanitize = new Twig_SimpleFilter('sanitize',function($strString){
			return sanitize_title_with_dashes($strString);
		});

		/**
		 * @todo all this stuff with the template engine needs to be moved out of here
		 */
		self::$objViewEngine->addFilter($objTwigDebug);
		self::$objViewEngine->addFilter($objTwigSanitize);


	}
}