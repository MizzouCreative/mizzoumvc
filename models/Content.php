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
        'include_pagination'=>false,
        'return'            =>false,
        'bypass_init'       =>false,
        'include_breadcrumbs'=>false,
    );



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
    public static function render($strInnerViewFileName,$aryData, Twig_Environment $objViewEngine, Site $objSite=null, $aryOptions=array())
    {
	    //we need a copy of the original passed in options
        $aryPassedOptions = $aryOptions;
        $aryOptions = array_merge(self::aryDefaultOptions,$aryOptions);

        if(!$aryOptions['bypass_init']){
            if(is_null($objSite)){
                //we HAVE to Site if we arent skipping init stuff
                $strInnerViewFileName = 'framework-error';
                $aryData['Error'] = '';
            } else {
                //edit post link
                if('' != $objSite->{'site-wide'}['include_edit_link'] && self::_mixedToBool($objSite->{'site-wide'}['include_edit_link'])){
                    if(((is_single() || is_page()) && '' != $strPostLink = get_edit_post_link())){
                        $aryData['EditLink'] = $strPostLink;
                    }
                }

                //Pagination
                if($aryOptions['include_pagination']){
                    global $wp_query;
                    $aryPaginationArgs = array('wp_query'=>$wp_query);
                    if('' != $aryPaginationOptions = $objSite->pagination){
                        $aryPaginationArgs = array_merge($aryPaginationArgs,$aryPaginationOptions);
                    }

                    $aryData['Pagination'] = new Pagination($aryPaginationArgs);
                }

                //page title?
                if(!isset($aryData['PageTitle'])){
                    $aryData['PageTitle'] = self::_determinePageTitle();
                }

                //root ancestor?
                if(!isset($aryData['RootAncestor'])){
                    $aryData['RootAncestor'] = self::_determineRootAncestor((isset($aryData['MainPost'])) ? $aryData['MainPost'] : null,$aryData['PageTitle']);
                }

                //breadcrumbs
                /**
                 * ok, we could either have the site-wide option set to true and the controller did nothing, or the site wide option could be set to true but
                 * a controller overrode to false, OR the site-wide option could be set to false, and a controller overrode to true
                 */
                if(
                    (
                        '' != $objSite->{'site-wide'}['include_breadcrumbs']
                        && self::_mixedToBool($objSite->{'site-wide'}['include_breadcrumbs'])
                        && (!isset($aryPassedOptions['include_breadcrumbs']) || $aryPassedOptions['include_breadcrumbs']))
                    ||
                    ($aryOptions['include_breadcrumbs'])
                ){
                    $aryAncestors = (isset($aryData['MainPost'])) ? $aryData['MainPost']->retrieveAncestors() : array();
                    $aryBreadcrumbOptions = (isset($objSite->breadcrumbs) && '' != $objSite->breadcrumbs) ? $objSite->breadcrumbs : array();
                    $aryData['Breadcrumbs'] = new Breadcrumbs($aryData['PageTitle'],$aryAncestors,$aryBreadcrumbOptions);
                }

                //menu?
                if(!isset($aryData['Menu'])){
                    $aryData['Menu'] = new Menu($aryData);
                }

                //We need to pass Site down to the view
                $aryData['Site'] = $objSite;

            }
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

        self::$objView = $objViewEngine->loadTemplate($strInnerViewFileName);

        $strReturn = self::$objView->render($aryData);

        if(!$aryOptions['return']){
            echo $strReturn;
            $strReturn = null;
        }

        return $strReturn;

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
        return;
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

    protected static function _mixedToBool($mxdVal)
    {
        return filter_var($mxdVal,FILTER_VALIDATE_BOOLEAN);
    }


}