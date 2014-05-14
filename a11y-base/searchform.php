<?php
/**
 * Template file containing our site search form
 * 
 * This is not an official wordpress theme file, but is used by us to contain
 * the search form in one location.  Typically called by @see header.php
 *
 * @package WordPress
 * @subpackage SITENAME
 * @category theme
 * @category template-part
 * @author Charlie Triplett, Paul F. Gilzow & Jason L Rollins, Web Communications, University of Missouri
 * @copyright 2013 Curators of the University of Missouri
 */
    $arySearchParams = unserialize(GSA_SEARCH_PARAMS);
?>

<div class="search-container clearfix">
	
	<form id="search-form" action="/search/" method="get" role="search">
	
			<label class="hidden" for="search">Search</label>
			<input id="search" class="search-field" name="q" size="22" type="text" onfocus="this.value=''" value="Search <?php bloginfo( 'name' ); ?> "/>
		
			<input class="search-button" name="sa" type="submit" value="Search"/> 
		
			<?php foreach($arySearchParams as $intKey=>$strValue):?>
			<input name="<?php echo $intKey;?>" type="hidden" value="<?php echo $strValue;?>" />
			<?php endforeach;?>
	</form>

</div>