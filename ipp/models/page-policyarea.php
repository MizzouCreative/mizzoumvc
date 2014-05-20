<?php
/**
 * Model for pages using the policy area template
 *
 * @package WordPress
 * @subpackage IPP
 * @category theme
 * @category model
 * @author Paul Gilzow, Web Communications, University of Missouri
 * @copyright 2014 Curators of the University of Missouri
 * @uses is_user_logged_in()
 * @uses comments_template()
 * @todo move function calls out of this view
 */

//pull in the base model
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'people.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'publication.php';
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'project.php';

class PolicyArea
{
    protected $objPeople        = null;
    protected $objPublication   = null;
    protected $objProject       = null;

    protected $aryPolicyDefaults = array(
          'taxonomy'    => 'policy_area'
    );

    public function __construct()
    {
        $this->objPeople = new People();
        $this->objProject = new Project();
        $this->objPublication = new Publication();
    }

    public function retrievePublications($strTerm)
    {
        $aryArgs = array(
            'count'     => 4, //this needs to be retrieved from config variable or theme option
            'tax_term'  => $strTerm
        );

        return $this->objPublication->retrieveContent(array_merge($this->aryPolicyDefaults,$aryArgs));
    }

    public function retrieveProjects($strTerm)
    {
        $aryArgs = array(
            'count'     => 4,//this needs to be retrieved from config variable or theme option
            'tax_term'  => $strTerm
        );

        return $this->objProject->retrieveContent(array_merge($this->aryPolicyDefaults,$aryArgs));
    }

    public function retrieveContact($strTerm)
    {
        $aryTax = array(
            'relation'  => 'AND',
            array_merge($this->aryPolicyDefaults,array(
                'field'     => 'slug',
                'terms'     => $strTerm
            )),
            array(
                'taxonomy'  => 'person_type', //defined in Mizzou People plugin
                'field'     => 'slug',
                'terms'     => 'lead-analysts' //@todo this should be moved into config or theme option
            )
        );

        $aryArgs = array(
            'count'         => 1, //@todo this needs to be retrieved from config variable or theme option
            'complex_tax'   => $aryTax,
            'include_meta'  => true,
        );

        _mizzou_log($aryArgs,'aryArgs',false,array('func'=>__FUNCTION__));

        $aryMatches = $this->objPeople->retrieveContent($aryArgs);

        if(count($aryMatches) !== 1) { //@todo throw an exception, log it, something!
            //@todo ask if we should grab some default contact info to display?
            _mizzou_log($aryMatches,'Array Matches',false,array('func'=>__FUNCTION__));
        }

        return $aryMatches[0];
    }

    public function retrievePublicationsArchivePermalink()
    {
        return $this->objPublication->getPermalink();
    }

    public function retrieveProjectsArchivePermalink()
    {
        return $this->objProject->getPermalink();
    }
}