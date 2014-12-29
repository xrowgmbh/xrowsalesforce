<?php

class xrowSalesForceCRMPlugin implements xrowFormCRM
{
    static private $connection = null;
    static $fields = null;

    public function getCampaigns()
    {
        $salesforceini = eZINI::instance('salesforce.ini');
        $whereStr = '';
        $sortCampaignDropDown = array();
        if( $salesforceini->hasVariable( 'Settings', 'WhereStrings' ) )
        {
            $filter = $salesforceini->variable( 'Settings', 'WhereStrings' );
            if( isset( $filter['Campaign'] ) )
            {
                $whereStr = " " . $filter['Campaign'];
            }
        }
        try
        {
            $connection = self::getConnection();
            $query = "SELECT Id, Name, Status, Type FROM Campaign" . $whereStr;
            $response = $connection->query( $query );
            $campaignArray = array();
            if( isset( $response->records ) && is_array( $response->records ) && count( $response->records ) > 0 )
            {
                // get types
                foreach( $response->records as $record )
                {
                    $sortCampaignsDropDown[$record->Type] = $record->Type;
                }
                
                if( count( $sortCampaignsDropDown ) > 0 )
                {
                    $campaignArray['optiongroups'] = array();
                    foreach( $sortCampaignsDropDown as $sortCampaignDropDown )
                    {
                        $tmpCampaignArray = array();
                        foreach( $response->records as $key => $record )
                        {
                            if( $record->Type == $sortCampaignDropDown )
                            {
                                $tmpCampaignArray[$record->Id] = $record->Name;
                            }
                        }
                        $campaignArray['optiongroups'][] = array( 'optiongroupname' => $sortCampaignDropDown,
                                                                  'campaigns' => $tmpCampaignArray );
                    }
                }
            }
            return $campaignArray;
        }
        catch( Exception $e )
        {
            throw new xrowSalesForceException( $e->getMessage() . ' -> xrowSalesForceCRMPlugin::getCampaigns' );
        }
    }

    public function getFields()
    {
        if( self::$fields === null )
        {
            $salesforceini = eZINI::instance('salesforce.ini');
            try
            {
                $connection = self::getConnection();
                $fieldsArray = array();
                $getFieldsFromClass = array( 'Lead' );
                if( $salesforceini->hasVariable( 'Settings', 'GetFieldsFromClass' ) )
                {
                    $getFieldsFromClass = $salesforceini->variable( 'Settings', 'GetFieldsFromClass' );
                }
                foreach( $getFieldsFromClass as $class )
                {
                    $sObject = $connection->describeSObjects( array( $class ) );
                    if( isset( $sObject[0] ) )
                    {
                        $sObject = $sObject[0];
                        $fieldsArray = array();
                        if( isset( $sObject ) && isset( $sObject->fields ) && is_array( $sObject->fields ) && count( $sObject->fields ) > 0 )
                        {
                            if( $salesforceini->hasGroup( 'ShowOnlyFieldsInFormGenerator_' . $class ) )
                            {
                                $showOnlyFields = $salesforceini->variable( 'ShowOnlyFieldsInFormGenerator_' . $class, 'ShowOnlyFieldsInFormGenerator' );
                                foreach( $sObject->fields as $field ) 
                                {
                                    if( $field->deprecatedAndHidden === false && in_array( $field->name, $showOnlyFields ) )
                                    {
                                        $field = (array)$field;
                                        if( $field['type'] == 'picklist' )
                                        {
                                            $tmpPicklist = $field['picklistValues'];
                                            unset( $field['picklistValues'] );
                                            foreach( $tmpPicklist as $picklistValue )
                                            {
                                                $field['picklistValues'][] = (array)$picklistValue;
                                            }
                                        }
                                        $fieldsArray[] = (array)$field;
                                    }
                                }
                            }
                            else
                            {
                                foreach( $sObject->fields as $field ) 
                                {
                                    if( $field->deprecatedAndHidden === false )
                                    {
                                        $fieldsArray[] = (array)$field;
                                    }
                                }
                            }
                        }
                        usort( $fieldsArray, array( "xrowSalesForceCRMPlugin", "cmp" ) );
                    }
                    $classFieldsArray[$class] = $fieldsArray;
                }
                #die(var_dump($classFieldsArray['CampaignMember']));
                self::$fields = $classFieldsArray;
            }
            catch( Exception $e )
            {
                throw new xrowSalesForceException( $e->getMessage() . ' -> xrowSalesForceCRMPlugin::getFields' );
            }
        }
        return self::$fields;
    }

    public function setAttributeDataForCRMField( $data, $http, $id, $crm )
    {
        if( $crm )
        {
            $data['label'] = $crm;
        }
        if( strpos( $data['type'], 'boolean' ) !== false )
        {
            if( $data['def'] !== null )
            {
                $data['def'] = true;
            }
            else
            {
                $data['def'] = false;
            }
        }
        if( strpos( $data['type'], 'picklist' ) !== false )
        {
            $data['option_array'] = array();
            $suffix = $id . $data['crmclass'] . $data['name'];
            if( $http->hasPostVariable( 'XrowFormElementCRMField' . $suffix ) )
            {
                $pickList = $http->postVariable( 'XrowFormElementCRMField' . $suffix );
                $options = array();
                foreach ( $pickList as $optKey => $pickListValue )
                {
                    $explodePickListValue = explode( '|', $pickListValue );
                    $item = array( 'value' => $explodePickListValue[0], 'name' => $explodePickListValue[1], 'def' => false );
                    if( $http->hasPostVariable( 'XrowFormElementCRMField' . $suffix . 'Default' ) )
                    {
                        $crmFieldValueDefault = $http->postVariable( 'XrowFormElementCRMField' . $suffix . 'Default' );
                        if( $crmFieldValueDefault == $explodePickListValue[0] )
                        {
                            $item['def'] = true;
                        }
                    }
                    $options[$optKey] = $item;
                }
                $data['option_array'] = $options;
            }
        }
        return $data;
    }

    public function setAttributeDataForCollectCRMField( $content, $key, $item, $inputContentCollection, $contentobject_id, $trans )
    {
        switch ( $item['type'] )
        {
            case "crmfield:string":
            case "crmfield:textarea":
            {
                $data = '';
                if( isset( $inputContentCollection[$item['name']] ) )
                {
                    $data = trim( $inputContentCollection[$item['name']] );
                }
                if ( $item['req'] == true && trim( $data ) == '' )
                {
                    $content['form_elements'][$key]['error'] = true;
                    $content['has_error'] = true;
                    $content['error_array'][mb_strtolower( $trans->transformByGroup( $item['label'], 'urlalias' ) )] = $item['label'] . ": " . ezpI18n::tr( 'kernel/classes/datatypes', "Input required." );
                }
                $content['form_elements'][$key]['def'] = $data;
            }break;
            case "crmfield:email":
            {
                $data = '';
                if( isset( $inputContentCollection[$item['name']] ) )
                {
                    $data = trim( $inputContentCollection[$item['name']] );
                }
                if ( $item['req'] == true )
                {
                    if ( $data == '' )
                    {
                        $content['form_elements'][$key]['error'] = true;
                        $content['has_error'] = true;
                        $content['error_array'][mb_strtolower( $trans->transformByGroup( $item['label'], 'urlalias' ) )] = $item['label'] . ": " . ezpI18n::tr( 'kernel/classes/datatypes', "Input required." );
                    }
                    elseif( $item['val'] == true )
                    {
                        if ( !xrowFormGeneratorType::validate( $data ) )
                        {
                            $content['form_elements'][$key]['error'] = true;
                            $content['has_error'] = true;
                            $content['error_array'][mb_strtolower( $trans->transformByGroup( $item['label'], 'urlalias' ) )] = $item['label'] . ": " . ezpI18n::tr( 'kernel/classes/datatypes', "Email address is not valid." );
                        }
                        elseif( $item['unique'] == true )
                        {
                            if ( !xrowFormGeneratorType::email_unique( $data, $contentobject_id ) )
                            {
                                $content['form_elements'][$key]['error'] = true;
                                $content['has_error'] = true;
                                $content['error_array'][mb_strtolower( $trans->transformByGroup( $item['label'], 'urlalias' ) )] = $item['label'] . ": " . ezpI18n::tr( 'kernel/classes/datatypes', "Your email was already submitted to us. You can't use the form twice." );
                            }
                        }
                    }
                }
                elseif ( $item['val'] == true && $data != '' )
                {
                    if ( !xrowFormGeneratorType::validate( $data ) )
                    {
                        $content['form_elements'][$key]['error'] = true;
                        $content['has_error'] = true;
                        $content['error_array'][mb_strtolower( $trans->transformByGroup( $item['label'], 'urlalias' ) )] = $item['label'] . ": " . ezpI18n::tr( 'kernel/classes/datatypes', "Email address is not valid." );
                    }
                    elseif( $item['unique'] == true ) 
                    {
                        if ( !xrowFormGeneratorType::email_unique( $data, $contentobject_id ) )
                        {
                            $content['form_elements'][$key]['error'] = true;
                            $content['has_error'] = true;
                            $content['error_array'][mb_strtolower( $trans->transformByGroup( $item['label'], 'urlalias' ) )] = $item['label'] . ": " . ezpI18n::tr( 'kernel/classes/datatypes', "Your email was already submitted to us. You can't use the form twice." );
                        }
                    }
                }
                elseif( $item['unique'] == true && $data != '' )
                {
                    if ( !xrowFormGeneratorType::email_unique( $data, $contentobject_id ) )
                    {
                        $content['form_elements'][$key]['error'] = true;
                        $content['has_error'] = true;
                        $content['error_array'][mb_strtolower( $trans->transformByGroup( $item['label'], 'urlalias' ) )] = $item['label'] . ": " . ezpI18n::tr( 'kernel/classes/datatypes', "Your email was already submitted to us. You can't use the form twice." );
                    }   
                }
                $content['form_elements'][$key]['def'] = $data;
            }break;
            case "crmfield:boolean":
            {
                $data = false;
                if( isset( $inputContentCollection[$item['name']] ) && $inputContentCollection[$item['name']] != "crmfield:boolean" )
                {
                    $data = true;
                }
                if ( $item['req'] == true )
                {
                    if ( !$data )
                    {
                        $content['form_elements'][$key]['error'] = true;
                        $content['has_error'] = true;
                        if(isset($item['label']) && $item['label'] != 'undefined')
                            $content['error_array'][mb_strtolower( $trans->transformByGroup( $item['label'], 'urlalias' ) )] = $item['label'] . ": " . ezpI18n::tr( 'kernel/classes/datatypes', "You need to select this checkbox." );
                        else
                            $content['error_array'][mb_strtolower( $trans->transformByGroup( $item['name'], 'urlalias' ) )] = ezpI18n::tr( 'kernel/classes/datatypes', "You need to select this checkbox." );
                    }
                }
                $content['form_elements'][$key]['def'] = $data;
            }break;
            case "crmfield:phone":
            {
                $data = '';
                $checkTelephone = false;
                if( isset( $inputContentCollection[$item['name']] ) )
                {
                    $data = trim( $inputContentCollection[$item['name']] );
                }
                if ( $item['req'] == true )
                {
                    if ( $data == '' )
                    {
                        $content['form_elements'][$key]['error'] = true;
                        $content['has_error'] = true;
                        $content['error_array'][mb_strtolower( $trans->transformByGroup( $item['label'], 'urlalias' ) )] = $item['label'] . ": " . ezpI18n::tr( 'kernel/classes/datatypes', "Input required." );
                    }
                    else
                    {
                        $checkTelephone = true;
                    }
                }
                
                if( $checkTelephone )
                {
                    if( !xrowFormGeneratorType::telephone_validate( $data ) || strlen( $data ) >= 25 )
                    {
                        $content['form_elements'][$key]['error'] = true;
                        $content['has_error'] = true;
                        $content['error_array'][mb_strtolower( $trans->transformByGroup( $item['label'], 'urlalias' ) )] = $item['label'] . ": " . ezpI18n::tr( 'kernel/classes/datatypes', "Please enter a valid phone number." );
                    }
                }
                $content['form_elements'][$key]['def'] = $data;
            }break;
            case "crmfield:picklist":
            {
                if( isset( $inputContentCollection[$item['name']] ) )
                {
                    $data = $inputContentCollection[$item['name']];
                    $optSelected = false;
                    
                    foreach ( $item['option_array'] as $optKey => $optItem )
                    {
                        $content['form_elements'][$key]['option_array'][$optKey]['def'] = false;
                        if ( $optItem['name'] == $data )
                        {
                            $content['form_elements'][$key]['option_array'][$optKey]['def'] = true;
                            $optSelected = true;
                        }
                    }
                    if ( $item['req'] == true )
                    {
                        if ( !$optSelected )
                        {
                            $content['form_elements'][$key]['error'] = true;
                            $content['has_error'] = true;
                            $content['error_array'][mb_strtolower( $trans->transformByGroup( $item['label'], 'urlalias' ) )] = $item['label'] . ": " . ezpI18n::tr( 'kernel/classes/datatypes', "Please select at least one option." );
                        }
                    }
                }
                else
                {
                    if ( $item['req'] == true )
                    {
                        $content['form_elements'][$key]['error'] = true;
                        $content['has_error'] = true;
                        $content['error_array'][mb_strtolower( $trans->transformByGroup( $item['label'], 'urlalias' ) )] = $item['label'] . ": " . ezpI18n::tr( 'kernel/classes/datatypes', "Please select at least one option." );
                    }
                }
            }break;
        }
        return $content;
    }

    public static function sendExportData( $objectAttribute, $collection )
    {
        // export only if campaign ID is set
        if( isset( $objectAttribute->Content['campaign_id'] ) && (int)$objectAttribute->Content['campaign_id'] > 0 && $objectAttribute->Content['campaign_id'] != '' )
        {
            $campaign_id = $objectAttribute->Content['campaign_id'];
            if( isset( $objectAttribute->Content['form_elements'] ) )
            {
                $form_elements = $objectAttribute->Content['form_elements'];
                $classObjects = array();
                $foundCRMField = false;
                $ini = eZINI::instance( 'salesforce.ini' );
                $exportFieldIntoFieldArrayTmp = $exportFieldIntoFieldArray = array();
                if( $ini->hasVariable( 'Settings', 'ExportFieldIntoField' ) )
                {
                    $exportFieldIntoFieldArrayTmp = $ini->variable( 'Settings', 'ExportFieldIntoField' );
                    foreach( $exportFieldIntoFieldArrayTmp as $exportFieldIntoField )
                    {
                        if( $ini->hasGroup( 'ExportFieldIntoField_' . $exportFieldIntoField ) )
                        {
                            $exportFieldIntoFieldArray[$exportFieldIntoField] = $ini->group( 'ExportFieldIntoField_' . $exportFieldIntoField );
                        }
                    }
                }
                foreach( $form_elements as $item )
                {
                    $crmClass = $item['crmclass'];
                    if( count( $exportFieldIntoFieldArray ) > 0 && isset( $exportFieldIntoFieldArray[$item['name']] ) )
                    {
                        if( isset( $exportFieldIntoFieldArray[$item['name']]['ToClass'] ) )
                            $crmClass = $exportFieldIntoFieldArray[$item['name']]['ToClass'];
                        $item['name'] = $exportFieldIntoFieldArray[$item['name']]['ToField'];
                    }
                    if( !isset( $classObjects[$crmClass] ) || ( isset( $classObjects[$crmClass] ) && !is_object( $classObjects[$crmClass] ) ) )
                        $classObjects[$crmClass] = new stdClass();
                    switch ( $item['type'] )
                    {
                        case "crmfield:string":
                        case "crmfield:phone":
                        case "crmfield:email":
                        case "crmfield:boolean":
                        case "crmfield:textarea":
                        {
                            $name = $item['name'];
                            $classObjects[$crmClass]->$name = $item['def'];
                            $foundCRMField = true;
                        }break;
                        case "crmfield:picklist":
                        {
                            foreach ( $item['option_array'] as $optKey => $optItem )
                            {
                                if ( $optItem['def'] )
                                {
                                    $name = $item['name'];
                                    $classObjects[$crmClass]->$name = $optItem['value'];
                                    $foundCRMField = true;
                                    break;
                                }
                            }
                        }break;
                    }
                }
                if( $foundCRMField && ( !isset( $classObjects['Lead']->Company ) || ( isset( $classObjects['Lead']->Company ) && ( $classObjects['Lead']->Company === null || $classObjects['Lead']->Company == '' ) ) ) )
                {
                    // Company ist ein Pflichtfeld. Soll hier nachträglich gesetzt werden, falls es noch nicht gefüllt wurde
                    $classObjects['Lead']->Company = 'nicht angegeben';
                }
                if( $foundCRMField )
                {
                    if( isset( $classObjects['Lead'] ) )
                    {
                        $result = self::saveStandardObjectData( $classObjects['Lead'], 'Lead', 'create', $objectAttribute, $collection );
                        if( $result->success !== false )
                        {
                            if( isset( $classObjects['CampaignMember'] ) )
                                $leadmember = $classObjects['CampaignMember'];
                            else
                                $leadmember = new stdClass;
                            $leadmember->CampaignId = $campaign_id;
                            $leadmember->LeadId = $result->id;
                            $result_member = self::saveStandardObjectData( $leadmember, 'CampaignMember', 'create', $objectAttribute, $collection );
                            if( $result_member->success === false )
                            {
                                if($ini->hasVariable('Settings', 'SendLogMails') && $ini->variable('Settings', 'SendLogMails') == 'enabled')
                                {
                                    self::sendMail( $result_member, 'CampaignMember ERROR' );
                                }
                                eZDebug::writeError( $result_member, __METHOD__ );
                                return false;
                            }
                        }
                        else
                        {
                            if($ini->hasVariable('Settings', 'SendLogMails') && $ini->variable('Settings', 'SendLogMails') == 'enabled')
                            {
                                self::sendMail( $result, 'Lead ERROR' );
                            }
                            eZDebug::writeError( $result, __METHOD__ );
                            return false;
                        }
                    }
                }
            }
        }
    }

    public static function saveStandardObjectData( $StandardObject, $class, $type, $ContentObject = false, $collection = false )
    {
        if( $StandardObject && $class != '' && ( $type == 'create' || $type == 'update' ) )
        {
            $ini = eZINI::instance( 'salesforce.ini' );
            if( $ini->hasVariable( 'UTMSettings', 'SaveInClass' ) )
            {
                $SaveInClass = $ini->variable( 'UTMSettings', 'SaveInClass' );
                if( $SaveInClass == $class )
                {
                    if( $ini->hasVariable( 'UTMSettings_' . $SaveInClass, 'SaveFields' ) )
                    {
                        $utmData = new GA_Parse();
                        $utmSaveFieldIntoFields = $ini->variable( 'UTMSettings_' . $SaveInClass, 'SaveFields' );
                        foreach( $utmSaveFieldIntoFields as $utmSaveFieldIntoFieldKey => $utmSaveFieldIntoField )
                        {
                            if( isset( $utmData->$utmSaveFieldIntoFieldKey ) && $utmData->$utmSaveFieldIntoFieldKey !== null )
                            {
                                $StandardObject->$utmSaveFieldIntoField = $utmData->$utmSaveFieldIntoFieldKey;
                            }
                        }
                    }
                }
            }

            $resultError = new stdClass;
            try
            {
                $connection = self::getConnection();
                $result = $connection->$type( array( $StandardObject ), $class );
                if ( $ini->hasVariable( 'Settings', 'AlwaysLog' ) && $ini->variable( 'Settings', 'AlwaysLog' ) == "enabled" )
                {
                    $log = "Class: " . $class . ", Type: " . $type . ", Collection-ID:" . $collection->ID .", ContentobjectID: " .  $ContentObject->ContentObjectID . ", Campaign ID: " . $ContentObject->Content["campaign_id"];
                    eZLog::write($log, 'salesforce_transactions.log');
                }
                if( is_array( $result ) && isset( $result[0] ) )
                {
                    $resultItem = $result[0];
                    if( isset( $resultItem->errors ) && count( $resultItem->errors ) > 0 )
                    {
                        $resultItem->errors = $resultItem->errors[0]->message;
                        eZDebug::writeError( $resultItem->errors, 'xrowSalesForceCRMPlugin::saveStandardObjectData::' . $class . '::' . $type );
                        if ( $ini->hasVariable( 'Settings', 'AlwaysLog' ) && $ini->variable( 'Settings', 'AlwaysLog' ) == "enabled" )
                        {
                            $log = "ERROR: Exception " . $resultItem->errors . ", Class: " . $class . " function " . $type . ', Collection-ID: ' . $collection->ID . ', ContentobjectID: ' .  $ContentObject->ContentObjectID . ', Campaign ID: ' . $ContentObject->Content["campaign_id"];
                            eZLog::write($log, 'salesforce_error.log');
                        }
                    }
                    elseif( $ini->hasVariable( 'Settings', 'AlwaysLog' ) && $ini->variable( 'Settings', 'AlwaysLog' ) == "enabled" )
                    {
                        $log = "Success: " . (string)$resultItem->success . ", Request-ID: " . (string)$resultItem->id . " Collection-ID:" . $collection->ID ." ContentobjectID: " .  $ContentObject->ContentObjectID . " Campaign ID: " . $ContentObject->Content["campaign_id"];
                        eZLog::write($log, 'salesforce_success.log');
                    }
                    return $resultItem;
                }
                else
                {
                    eZDebug::writeError( 'result is not set', 'xrowSalesForceCRMPlugin::saveStandardObjectData::' . $class . '::' . $type );
                    $resultError->errors = 'result is not set for class ' . $class . ' function ' . $type;
                    if ( $ini->hasVariable( 'Settings', 'AlwaysLog' ) && $ini->variable( 'Settings', 'AlwaysLog' ) == "enabled" )
                    {
                        $log = 'ERROR: ' . $resultError->errors . ', Collection-ID: ' . $collection->ID . ', ContentobjectID: ' .  $ContentObject->ContentObjectID . ', Campaign ID: ' . $ContentObject->Content["campaign_id"];
                        eZLog::write($log, 'salesforce_error.log');
                    }
                    return $resultError;
                }
            }
            catch( Exception $e )
            {
                eZDebug::writeError( $e->getMessage(), 'xrowSalesForceCRMPlugin::saveStandardObjectData' );
                $resultError->errors = $e->getMessage();
                if ( $ini->hasVariable( 'Settings', 'AlwaysLog' ) && $ini->variable( 'Settings', 'AlwaysLog' ) == "enabled" )
                {
                    $log = "ERROR: Exception " . $e->getMessage() . ", Class: " . $class . " function " . $type . ', Collection-ID: ' . $collection->ID . ', ContentobjectID: ' .  $ContentObject->ContentObjectID . ', Campaign ID: ' . $ContentObject->Content["campaign_id"];
                    eZLog::write($log, 'salesforce_error.log');
                }
                return $resultError;
            }
        }
    }

    static public function executeQuery( $query )
    {
        try
        {
            $connection = self::getConnection();
            //$query = "SELECT Id, Name FROM Campaign WHERE Id = '" . $campaignID . "'";
            $response = $connection->query( $query );
            if( isset( $response->records ) && is_array( $response->records ) && count( $response->records ) > 0 )
            {
                return $response->records;
            }
            elseif( isset( $response->records ) && is_array( $response->records ) && count( $response->records ) == 0 )
            {
                return false;
            }
        }
        catch( Exception $e )
        {
            throw new xrowSalesForceException( $e->getMessage() );
        }
    }

    static private function getConnection()
    {
        if( self::$connection === null )
        {
            try
            {
                self::$connection = Salesforce::factory();
            }
            catch( Exception $e )
            {
                throw new xrowSalesForceException( $e->getMessage() . ' -> xrowSalesForceCRMPlugin::getConnection' );
            }
        }
        return self::$connection;
    }

    static function cmp( $a, $b )
    {
        $al = strtolower($a['label']);
        $bl = strtolower($b['label']);
        if ($al == $bl) {
            return 0;
        }
        return ($al > $bl) ? +1 : -1;
    }

    static function sendMail( $result, $subject )
    {
        $ini = eZINI::instance( 'site.ini' );
        $salesforce_ini = eZINI::instance( 'salesforce.ini' );
        if( $salesforce_ini->hasVariable( 'Settings', 'ReceiverArray' ) )
        {
            $transport = $ini->variable( 'MailSettings', 'Transport' );
            $sender = $ini->variable( 'MailSettings', 'EmailSender' );
            $receiverArray = $salesforce_ini->variable( 'Settings', 'ReceiverArray' );
            ezcMailTools::setLineBreak( "\n" );
            $mail = new ezcMailComposer();
            $mail->charset = 'utf-8';
            $mail->from = new ezcMailAddress( $sender, '', $mail->charset );
            $mail->returnPath = $mail->from;
            foreach ( $receiverArray as $receiver )
            {
                $mail->addTo( new ezcMailAddress( $receiver, '', $mail->charset ) );
            }
            $mailText = '';
            $mailText = self::makeText( $result, $mailText );
            $mail->subject = 'Salesforce ' . $subject;
            $mail->subjectCharset = $mail->charset;
            $mail->plainText = $mailText;
            $mail->build();
            $mailsettings = array();
            $mailsettings["transport"] = $ini->variable( "MailSettings", "Transport" );
            $mailsettings["server"] = $ini->variable( "MailSettings", "TransportServer" );
            $mailsettings["port"] = $ini->variable( "MailSettings", "TransportPort" );
            $mailsettings["user"] = $ini->variable( "MailSettings", "TransportUser" );
            $mailsettings["password"] = $ini->variable( "MailSettings", "TransportPassword" );
            $mailsettings["connectionType"] = $ini->variable( 'MailSettings', 'TransportConnectionType' );
            if( trim( $mailsettings["port"] ) == "" )
            {
                $mailsettings["port"] = null;
            }
            if ( strtolower( $mailsettings["transport"]) == "smtp" )
            {
                $options = new ezcMailSmtpTransportOptions();
                if( trim( $mailsettings["password"]) === "" )
                {
                    $transport = new ezcMailSmtpTransport( $mailsettings["server"], "", "", $mailsettings["port"], $options );
                }
                else
                {
                    $options->preferredAuthMethod = ezcMailSmtpTransport::AUTH_AUTO;
                    $transport = new ezcMailSmtpTransport( $mailsettings["server"], $mailsettings["user"], $mailsettings["password"], $mailsettings["port"], $options );
                }
            }
            else if ( strtolower($mailsettings["transport"]) == "sendmail" )
            {
                $transport = new ezcMailMtaTransport();
            }
            else
            {
                eZDebug::writeError( "Wrong Transport in MailSettings in", 'xrowFormGeneratorType::xrowSendFormMail' );
                return null;
            }
            
            //ezcmail sending
            if ( strtolower($mailsettings["transport"]) != "file" )
            {
                try
                {
                    $transport->send( $mail );
                }
                catch ( ezcMailTransportException $e )
                {
                    eZDebug::writeError( $e->getMessage(), 'xrowFormGeneratorType::xrowSendFormMail' );
                }
            }
        }
    }

    static function makeText($object, &$mailText)
    {
        foreach($object as $objectItemName =>  $objectItem)
        {
            if(is_string($objectItem))
                $mailText .= $objectItemName . ":" . $objectItem . "\n";
            else
                self::makeText($objectItem, $mailText);
        }
        return $mailText;
    }
}