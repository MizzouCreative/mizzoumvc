<?php
/**
 * Template file used to render pagination on archive pages
 * 
 * Called on other template files via 
 * <code>
 * get_template_part('aside','pagination'); 
 * </code>
 * 
 * Might need to change class names to match site css.
 * 
 * *NOTE* does not account for query parameters and maintaining them between
 * archive page links
 * 
 * @package WordPress
 * @subpackage SITENAME
 * @category theme
 * @category template-part
 * @author Paul F. Gilzow & Jason L Rollins, Web Communications, University of Missouri
 * @copyright 2013 Curators of the University of Missouri
 * 
 */

/**
 * @todo this section needs to be moved into a function placed in the 
 * @see functions.php file OR we need to make it a plugin
 * @todo add functionality to maintain query parameters between archive page links
 */
global $wp_query;
if(!defined('SITE_MAX_NUM_ARCHIVE_PAGE_LINKS')) define('SITE_MAX_NUM_ARCHIVE_PAGE_LINKS',6);

$intOnPage = (get_query_var('paged') != 0) ? get_query_var('paged') : 1;
$intMaxPages = (isset($wp_query->max_num_pages)) ? $wp_query->max_num_pages : 1;

$intMidPoint = round((SITE_MAX_NUM_ARCHIVE_PAGE_LINKS/2), $precision, PHP_ROUND_HALF_DOWN);

if(($intMaxPages - $intOnPage) < $intMidPoint){
    // we're close to the end. give the extra to the low end
    $intLowerLimit = $intOnPage - MIZZOU_MAX_NUM_ARCHIVE_PAGE_LINKS + ($intMaxPages - $intOnPage);
    $intUpperLimit = $intMaxPages;
} elseif($intOnPage - $intMidPoint < 1){
    //we're near the bottom, give the extra to the top
    $intLowerLimit = 1;
    $intUpperLimit = $intOnPage + $intMidPoint + abs($intOnPage - $intMidPoint);
    
} else {
    //we're in the middle somewhere
    $intLowerLimit = $intOnPage - $intMidPoint;
    $intUpperLimit = $intOnPage + $intMidPoint;
}

/**
 * If intLowerLimit is 3 or 2 we end up with uneeded ellipsis
 */
if($intLowerLimit <= 3) $intLowerLimit = 1;
/**
 * If the offset between max and upper limit is 2 or less, we end up with
 * uneeded ellipsis
 */
if(($intMaxPages - $intUpperLimit) <=2 ) $intUpperLimit = $intMaxPages;

if(get_query_var('paged') == 0 || FALSE === strpos($_SERVER['REQUEST_URI'],'/page/')){
    $strBase = $_SERVER['REQUEST_URI'];
} else {
    $strBase = substr($_SERVER['REQUEST_URI'], 0, (strpos($_SERVER['REQUEST_URI'], '/page/') + 1));
}

$strPattern = $strBase .'page/%d/';

if(isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != ''){
    $strPattern .= '?'.htmlentities($_SERVER['QUERY_STRING'],ENT_QUOTES,'UTF-8',false);
}

?>

                     <?php if($intMaxPages > 1):?>
                    <ul class="pagination">
                        <?php if($intOnPage != 1):?>
                        <li><a class="pagination-previous" href="<?php echo sprintf($strPattern,($intOnPage-1)); ?>">&#171;&#160;Prev</a></li>
                        <?php endif;?>
                        
                        <?php if($intLowerLimit != 1): ?>
                        <li><a href="<?php echo sprintf($strPattern,1);?>" >1</a></li>
                        <li>&#8230;</li>
                        <?php endif;?>
                        
                        <?php for($i=$intLowerLimit;$i<=$intUpperLimit;++$i): $strCurrentClass = ($i==$intOnPage)?' class="current"':'';?>
                        <li<?php echo $strCurrentClass;?>><a href="<?php echo sprintf($strPattern,$i);?>" class="page"><?php echo $i; ?></a></li>
                        <?php endfor;?>
                        
                        <?php if($intUpperLimit != $intMaxPages):?>
                        <li>&#8230;</li>
                        <li><a href="<?php echo sprintf($strPattern,$intMaxPages); ?>" class="page"><?php echo $intMaxPages; ?></a></li>
                        <?php endif; ?>
                        
                        <?php if($intOnPage != $intMaxPages):?>
                        <li class="page-next"><a href="<?php echo sprintf($strPattern,($intOnPage+1));?>">Next&#160;&#187;</a></li>
                        <?php endif;?>
                    </ul>
                    <?php endif;?>