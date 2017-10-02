<?php
/**
 * Created by PhpStorm.
 * User: gilzowp
 * Date: 5/16/16
 * Time: 9:09 AM
 */

namespace MizzouMVC\models;
use MizzouMVC\models\Base;


class RenderType extends Base
{
    
    
    public function __construct(\WP_Query $objWPQuery)
    {
        $aryActions = $this->_retrieveIsActions($objWPQuery);
        /**
         * interestingly, is_front_page is not a property of WP_Query like most other is_* properties
         * @see https://core.trac.wordpress.org/browser/tags/4.8.1/src/wp-includes/class-wp-query.php#L3694
         * It's always dynamically generated. We will store it as a property
         */
        $aryActions['is_front_page'] = $objWPQuery->is_front_page();
        $this->_setCurrentAction($aryActions);
        $this->aryData = array_merge($this->aryData,$aryActions);
    }

    protected function _setCurrentAction($aryActions)
    {
        $aryAction = $this->_determineCurrentAction($aryActions);
        if(count($aryAction) > 1){
            $this->add_data('current', 'multi');
            $this->add_data('multi', $aryAction);
        } else {
            $this->add_data('current', reset($aryAction));
        }
    }


    /**
     * Returns all of the is_<action> properties from WP_Query
     * This is basically a preg_grep on array keys instead of values. let me explain what's going on.
     * 1. Get all the properties from the Wp_Query object
     * 2. grab the keys from wp_query
     * 3. preg_grep for those keys that match is_*
     * 4. flip the resulting array so the matches are keys of an array
     * 5. grab the intersection of keys between the original wp_query and our resulting array
     * 6. PROFIT! ok, really we end up with an array containing all of the items from wp_query where the keys match
     * $is_something and then return it as an object
     *
     * @param \WP_Query $objWPQuery
     * @return array
     */
    protected function _retrieveIsActions(\WP_Query $objWPQuery)
    {
        $aryWPQueryProps = get_object_vars($objWPQuery);
        return array_intersect_key($aryWPQueryProps,array_flip(preg_grep('/^is_/',array_keys($aryWPQueryProps))));
    }

    protected function _determineCurrentAction($aryActions)
    {
        $aryAction = array_keys($aryActions,true,true);
        foreach ($aryAction as $intKey => $strAction){
            $aryAction[$intKey] = $this->_retrieveJustAction($strAction);
        }

        return $aryAction;
    }

    /**
     * Strips off the 'is_' part of an action
     * @param string $strAction
     * @return string
     * @todo i wonder if ltrim is faster than substr + strlen ?
     */
    protected function _retrieveJustAction($strAction)
    {
        if(1 === preg_match('/^is_(?P<action>[a-z_]+)$/',$strAction,$aryMatches)){
            $strAction = $aryMatches['action'];
        }
        return $strAction;
    }
}