<?php

class xrowSalesForceCRMPlugin implements xrowFormCRM
{
    public function getCampains()
    {
        try
        {
            $connection = Salesforce::factory();
            $describe = $connection->describeSObjects( array( 'Lead' ) );
        }
        catch( Exception $e )
        {
            eZDebug::writeError( $e->getMessage() . ' -> Salesforce::factory()' );
            return array();
        }
    }
    public function getFields()
    {}
    public function getFieldContent( $fieldID )
    {}
    public function sendExportData( $data, $objectAttribute  )
    {}
}