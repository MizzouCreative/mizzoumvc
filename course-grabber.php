<?php
   /**
   
   * Department Course Info Web Service object Interacts with the Application Development Network Web Service with the same name
   * 
   * Developed by the Application Development Network (http://adn.missouri.edu)
   * 
   * @copyright �� 2011 - Curators of the University of Missouri (http://www.umsystem.edu).
   *       You have the right to use and modify this code as you see fit as long as it if for official University of Missouri work.
   *       All other rights reserved.
   * 
   * @author James "Gates" Richardson richardsonja@missouri.edu    
   * 
   * @version 1.0
   */
   class DepartmentCourseInfoWSObject
   {
       //This is your private passcode.  
       //if you do not have one or you believe yours has been compromised, 
       //contact the ADN at http://adn.missouri.edu/ 
       private $YourDepartmentPrivatePasscode = '';

       //the location of the web service. dont forget the ?WSDL!
       
       //PROD
       private $WebServiceURL = 'https://webservices.doit.missouri.edu/DepartmentCourseInfows/DepartmentCourseInfo.asmx?WSDL';
       
       //DEV
       //private $WebServiceURL = 'https://adn-webdev.col.missouri.edu/DepartmentCourseInfoWS/DepartmentCourseInfo.asmx?WSDL';

       public function __construct(){ 
           ini_set('memory_limit','64M');
           $this->YourDepartmentPrivatePasscode = 'J2SThgKZY4EQU4VP2ZmKqsw9';
       }

       /**
       * Cleans the item to reduce SQL Inject etc...
       * 
       * @author James Richardson
       * @dateCreated Dec 2005
       * @version 3.2
       * @released under GNU License V3.  (basically do as you please, just note the original author)
       * 
       * @param string $item
       * @return string
       */
       private function cleanItem($item) {
           if(is_bool($item) || is_numeric($item) || $item == 0 || $item == 1){ return $item; }  //if boolean or numeric, return it now, it is clean
           if($item == '' || $item == null){    return '';    }  //oops it is empty, so return blank

           //if array, recursively go through it and clean it
           if(is_array($item)){
               $temp = $item;
               foreach($temp as $keys => $values){
                   $item[$keys] = $this->cleanItem($values);    
               }
               return $item;
           } else {
               return filter_var($item, FILTER_SANITIZE_STRING);
               //return mysql_real_escape_string($item);
           }
       }

       /**
       * This function will make the request to the web service and return the xml format.
       * 
       * NOTE: This is where the SOAP call is made!
       * 
       * @return XML formatted response from the webservice
       */
       public function getTermList_XML(){
           $SoapObject = @new SoapClient($this->WebServiceURL); 

           //parameters
           $parameterArray =  array('passcode' => $this->YourDepartmentPrivatePasscode);

           //send request to the web service and store the response
           $ResponseFromWS = $SoapObject->TermListXml($parameterArray);
           //Load the response into an XML structure
           $XML_ResponseFromWS = simplexml_load_string($ResponseFromWS->TermListXmlResult->any);

           return $XML_ResponseFromWS;
       }

       /**
       * Will return formatted array of results instead of XML
       * 
       * @return Array with all the data parsed out of XML
       */
       public function getTermList_Array() {
           //get the list of terms in XML format (it also does the SOAP calling)
           $XML_ResponseFromWS = $this->getTermList_XML();    
           //if it is not an object or if it is invalid passcode message, then return the message
           //I tried to make the second condition generic but nothing works.  you have to llitterally say == this specific message.
           if(!is_object($XML_ResponseFromWS) || $XML_ResponseFromWS == 'invalid passcode') {
               return $XML_ResponseFromWS;
           }
           $ResultsFormattedIntoArray = array();
           //retreive the rowcount
           $rowCount = $XML_ResponseFromWS->attributes()->rowcount;
           //place into the array
           $ResultsFormattedIntoArray['rowcount'] = $rowCount; 

           $ResultsFormattedIntoArray['item'] = array();
           foreach($XML_ResponseFromWS->item as $IndividualItem){
               $ResultsFormattedIntoArray['item'][] = array(
               'termcode'      => $IndividualItem->termcode,
               'termdescr'     => $IndividualItem->{'termdescr'}
               );    
           }

           return $ResultsFormattedIntoArray;
       }

       /**
       * This function will make the request to the web service and return the xml format.
       * 
       * NOTE: This is where the SOAP call is made!
       * 
       * @param    int     $SectionID      The Section ID
       * @param    int     $Term           The Term Code to narrow your search down
       * @param    ???     $Reference      ???
       * @return XML formatted response from the webservice
       */
       public function getSectionList_XML($SectionID = '', $Term = '', $Reference = ''){
           $SoapObject = @new SoapClient($this->WebServiceURL); 

           //parameters.  be sure to clean them so that no bad things get into the request stream
           $parameterArray =  array(
           'passcode' => $this->YourDepartmentPrivatePasscode, 
           'SectionID'=> $this->cleanItem($SectionID), 
           'Term' => $this->cleanItem($Term),  
           'Reference' => $this->cleanItem($Reference)
           );

           //send request to the web service and store the response
           $ResponseFromWS = $SoapObject->SectionListXml($parameterArray);

           //Load the response into an XML structure
           $XML_ResponseFromWS = simplexml_load_string($ResponseFromWS->SectionListXmlResult->any);

           return $XML_ResponseFromWS;
       }

       /**
       * Will return formatted array of results instead of XML
       * 
       * @param    int     $SectionID      The Section ID
       * @param    int     $Term           The Term Code to narrow your search down
       * @param    ???     $Reference      ???
       * @return Array with all the data parsed out of XML
       */
       public function getSectionList_Array($SectionID = '', $Term = '', $Reference = '') {
           //get the list of terms in XML format (it also does the SOAP calling)
           $XML_ResponseFromWS = $this->getSectionList_XML($SectionID, $Term, $Reference);    

           //if it is not an object or if it is invalid passcode message, then return the message
           //I tried to make the second condition generic but nothing works.  you have to llitterally say == this specific message.
           if(!is_object($XML_ResponseFromWS) || $XML_ResponseFromWS == 'invalid passcode') {
               return $XML_ResponseFromWS;
           }

           $ResultsFormattedIntoArray = array();
           //retreive the rowcount
           $rowCount = $XML_ResponseFromWS->attributes()->rowcount;
           //place into the array
           $ResultsFormattedIntoArray['rowcount'] = $rowCount;

           $ResultsFormattedIntoArray['Section'] = array();
           foreach($XML_ResponseFromWS->Sections as $IndividualSections){                 
               $ResultsFormattedIntoArray['Section'][] = $this->ProcessSectionList_Array($IndividualSections);
           }

           return $ResultsFormattedIntoArray;
       }

       /**
       * Processes an array of sections.  Course List and Section List use the same format
       * 
       * NOTE!!! Due to formating discrepancy in the format of the two XML structures, a special condition had to be created for Instructors.  The end result is the instructors being outside of the meeting subarray. 
       * 
       * @param mixed $SectionItem  The section of XML that will be parsed into an array
       * @return array of Sections
       */
       private function ProcessSectionList_Array($SectionItem) {
           //if it is not an object or if it is invalid passcode message, then return the message
           //I tried to make the second condition generic but nothing works.  you have to llitterally say == this specific message.
           if(!is_object($SectionItem) || $SectionItem == 'invalid passcode') {
               return $SectionItem;
           }

           $ResultsFormattedIntoArray = array();

           //Loop through each section
           //foreach($objXML->Sections as $IndividualSection2){
           foreach($SectionItem->Section as $IndividualSection){ 

               //get all the Enrollment information for this section
               $EnrollmentArray = array();
               foreach($IndividualSection->Enrollment as $objEnrollmentItem){  
                   $EnrollmentArray = array(
                   'Cap'      => $objEnrollmentItem->Cap,
                   'Seats'     => $objEnrollmentItem->Seats,
                   'Consent'     => $objEnrollmentItem->Consent,
                   'Status'     => $objEnrollmentItem->Status
                   );
               }

               //get all meeting information for this section
               $MeetArray = array();
               //used to determine where the instructor is.  if it is inside meeting then it will be TRUE, else it will be hte default of FALSE
               $InstructorIsInsideMeeting = false;            
               //get all instructor information for this section (may be multiple)
               $InstructorArray = array();
               foreach($IndividualSection->Meet as $objMeetItem){
                   foreach($objMeetItem->Instructors as $objInstructorsItem){   
                       foreach($objInstructorsItem->Instructor as $objInstructorItem){     
                           $InstructorArray[] = array(
                           'FirstName'      => $objInstructorItem->FirstName,
                           'LastName'     => $objInstructorItem->LastName
                           );
                           //this will make it so the next ifstatement outside of the meeting foreach loop will not execute
                           $InstructorIsInsideMeeting = true;   
                       }
                   }

                   $MeetArray = array(
                   'Days'      => $objMeetItem->Days->Day,
                   'StartTime'     => $objMeetItem->Time->StartTime,
                   'EndTime'     => $objMeetItem->Time->EndTime,
                   'Location'     => $objMeetItem->Location->Building
                   //'Instructors'     => $InstructorArray  //removed instructors out of meeting
                   );
               }
               //if false, then the forloops inside of the meeting loop did not execute, which means instructors are outside of the loop.
               if(!$InstructorIsInsideMeeting) {
                   $InstructorArray = array(); //clear it out just to be sure
                   foreach($IndividualSection->Instructors as $objInstructorsItem){   
                       foreach($objInstructorsItem->Instructor as $objInstructorItem){     
                           $InstructorArray[] = array(
                           'FirstName'      => $objInstructorItem->FirstName,
                           'LastName'     => $objInstructorItem->LastName
                           );
                       }
                   }
               }

               $SectionAttibuteList = array();
               foreach($IndividualSection->attributes() as $a => $b) {
                   $SectionAttibuteList[$a] = $b;
               }

               //package the all the information into its final format, which is an array
               $ResultsFormattedIntoArray[] = array(    
               'SectionAttibuteList' => $SectionAttibuteList,
               'Title'      => $IndividualSection->Title,
               'Description'     => $IndividualSection->Description,
               'Topic'     => $IndividualSection->Topic,
               'Notes'     => $IndividualSection->Notes,
               'Enrollment'     => $EnrollmentArray,
               'Meet'     => $MeetArray,
               'Instructors'     => $InstructorArray     //placed instructors outside of the meeting
               );    
           }
           //}

           return $ResultsFormattedIntoArray;
       }

       /**
       * This function will make the request to the web service and return the xml format.
       * 
       * NOTE: This is where the SOAP call is made!
       * 
       * @param    int     $Term           The Term Code to narrow your search down
       * @param    string  $SubjectCode    The Subject area such as CMP_SC
       * @return XML formatted response from the webservice
       */
       public function getCourseInformation_XML($Term = '', $SubjectCode = ''){
           $SoapObject = @new SoapClient($this->WebServiceURL); 

           //parameters.  be sure to clean them so that no bad things get into the request stream
           $parameterArray =  array(
           'passcode' => $this->YourDepartmentPrivatePasscode,  
           'TermCode' => $this->cleanItem($Term),  
           'SubjectCode' => $this->cleanItem($SubjectCode)
           );

           //send request to the web service and store the response
           $ResponseFromWS = $SoapObject->CourseInfoXml($parameterArray);

           //Load the response into an XML structure
           $XML_ResponseFromWS = simplexml_load_string($ResponseFromWS->CourseInfoXmlResult->any);

           return $XML_ResponseFromWS;
       }

       /**
       * Will return formatted array of results instead of XML
       * 
       * @param    int     $Term           The Term Code to narrow your search down
       * @param    string  $SubjectCode    The Subject area such as CMP_SC
       * @return Array with all the data parsed out of XML
       */
       public function getCourseInformation_Array($Term = '', $SubjectCode = '') {
           //get the list of terms in XML format (it also does the SOAP calling)
           $XML_ResponseFromWS = $this->getCourseInformation_XML($Term, $SubjectCode);    

           //if it is not an object or if it is invalid passcode message, then return the message
           //I tried to make the second condition generic but nothing works.  you have to llitterally say == this specific message.
           if(!is_object($XML_ResponseFromWS) || $XML_ResponseFromWS == 'invalid passcode') {
               return $XML_ResponseFromWS;
           }

           $ResultsFormattedIntoArray = array();
           //retreive the rowcount
           $rowCount = $XML_ResponseFromWS->attributes()->rowcount;
           //place into the array
           $ResultsFormattedIntoArray['rowcount'] = $rowCount; 

           $ResultsFormattedIntoArray['item'] = array();
           foreach($XML_ResponseFromWS->item as $IndividualItem){
               $ResultsFormattedIntoArray['item'][] = array(
               'subject'      => $IndividualItem->subject,
               'catalognumber'     => $IndividualItem->catalognumber,
               'title'     => $IndividualItem->title,
               'hours'     => $IndividualItem->hours,
               'description'     => $IndividualItem->description
               );        
           }

           return $ResultsFormattedIntoArray;//*/
       }
       
       /**
       * This function will make the request to the web service and return the xml format.
       * 
       * NOTE: This is where the SOAP call is made!
       * 
       * @param    int     $Term           The Term Code to narrow your search down
       * @param    string  $SubjectCode    The Subject area such as CMP_SC
       * @return XML formatted response from the webservice
       */
       public function getUniqueCourseInformation_XML($Term = '', $SubjectCode = ''){
           $SoapObject = @new SoapClient($this->WebServiceURL); 

           //parameters.  be sure to clean them so that no bad things get into the request stream
           $parameterArray =  array(
           'passcode' => $this->YourDepartmentPrivatePasscode,  
           'TermCode' => $this->cleanItem($Term),  
           'SubjectCode' => $this->cleanItem($SubjectCode)
           );

           //send request to the web service and store the response
           $ResponseFromWS = $SoapObject->UniqueCourseInfoXml($parameterArray);

           //Load the response into an XML structure
           $XML_ResponseFromWS = simplexml_load_string($ResponseFromWS->UniqueCourseInfoXmlResult->any);

           return $XML_ResponseFromWS;
       }

       /**
       * Will return formatted array of results instead of XML
       * 
       * @param    int     $Term           The Term Code to narrow your search down
       * @param    string  $SubjectCode    The Subject area such as CMP_SC
       * @return Array with all the data parsed out of XML
       */
       public function getUniqueCourseInformation_Array($Term = '', $SubjectCode = '') {
           //get the list of terms in XML format (it also does the SOAP calling)
           $XML_ResponseFromWS = $this->getUniqueCourseInformation_XML($Term, $SubjectCode);    

           //if it is not an object or if it is invalid passcode message, then return the message
           //I tried to make the second condition generic but nothing works.  you have to llitterally say == this specific message.
           if(!is_object($XML_ResponseFromWS) || $XML_ResponseFromWS == 'invalid passcode') {
               return $XML_ResponseFromWS;
           }

           $ResultsFormattedIntoArray = array();
           //retreive the rowcount
           $rowCount = $XML_ResponseFromWS->attributes()->rowcount;
           //place into the array
           $ResultsFormattedIntoArray['rowcount'] = $rowCount; 

           $ResultsFormattedIntoArray['item'] = array();
           foreach($XML_ResponseFromWS->item as $IndividualItem){
               $ResultsFormattedIntoArray['item'][] = array(
               'subject'      => $IndividualItem->subject,
               'catalognumber'     => $IndividualItem->catalognumber,
               'title'     => $IndividualItem->title,
               'hours'     => $IndividualItem->hours,
               'description'     => $IndividualItem->description
               );        
           }

           return $ResultsFormattedIntoArray;//*/
       }

       /**
       * This function will make the request to the web service and return the xml format.
       * 
       * NOTE: This is where the SOAP call is made!
       * 
       * @param    int     $Term           The Term Code to narrow your search down
       * @param    string  $SubjectCode    The Subject area such as CMP_SC
       * @return XML formatted response from the webservice
       */
       public function getCourseList_XML($CourseID = '', $CourseLevel = '', $CourseCredits = '', $Term = ''){
           $SoapObject = @new SoapClient($this->WebServiceURL); 

           //parameters.  be sure to clean them so that no bad things get into the request stream
           $parameterArray =  array(
           'passcode' => $this->YourDepartmentPrivatePasscode,  
           'CourseID' => $this->cleanItem($CourseID),  
           'CourseLevel' => $this->cleanItem($CourseLevel),  
           'CourseCredits' => $this->cleanItem($CourseCredits),  
           'Term' => $this->cleanItem($Term)
           );

           //send request to the web service and store the response
           $ResponseFromWS = $SoapObject->CourseListXml($parameterArray);

           //Load the response into an XML structure
           $XML_ResponseFromWS = simplexml_load_string($ResponseFromWS->CourseListXmlResult->any);
           //print_r($XML_ResponseFromWS);
           return $XML_ResponseFromWS;
       }

       /**
       * Will return formatted array of results instead of XML
       * 
       * @param    int     $Term           The Term Code to narrow your search down
       * @param    string  $SubjectCode    The Subject area such as CMP_SC
       * @return Array with all the data parsed out of XML
       */
       public function getCourseList_Array($CourseID = '', $CourseLevel = '', $CourseCredits = '', $Term = '') {
           //get the list of terms in XML format (it also does the SOAP calling)
           $XML_ResponseFromWS = $this->getCourseList_XML($CourseID, $CourseLevel, $CourseCredits, $Term);    
           //if it is not an object or if it is invalid passcode message, then return the message
           //I tried to make the second condition generic but nothing works.  you have to llitterally say == this specific message.
           if(!is_object($XML_ResponseFromWS) || $XML_ResponseFromWS == 'invalid passcode') {
               return $XML_ResponseFromWS;
           }

           $ResultsFormattedIntoArray = array(); 
           //retreive the rowcount
           $rowCount = $XML_ResponseFromWS->attributes()->rowcount;
           //place into the array
           $ResultsFormattedIntoArray['rowcount'] = $rowCount;

           $ResultsFormattedIntoArray['Course'] = array();

           //Loop through each section
           foreach($XML_ResponseFromWS->Course as $IndividualCourse){
               //get all Sections information for this section
               //$SectionsArray[] = convertSectionsIntoArray($IndividualCourse->Sections);
               foreach($IndividualCourse->Sections as $objectSectionItem){
                   $SectionsArray = $this->ProcessSectionList_Array($objectSectionItem);
               }

               $CourseAttibuteList = array();
               foreach($IndividualCourse->attributes() as $a => $b) {
                   $CourseAttibuteList[$a] = $b;
               }

               //package the all the information into its final format, which is an array
               $ResultsFormattedIntoArray['Course'][] = array(    
               'CourseAttibuteList' => $CourseAttibuteList,
               'Title'      => $IndividualCourse->Title,
               'Description'     => $IndividualCourse->Description,
               'Sections'     => $SectionsArray
               );    
           }

           return $ResultsFormattedIntoArray;//*/
       }
   }
?>