<?php

//namespace XROW\Salesforce;

//use eZINI;
//use SforceEnterpriseClient;

class Salesforce
{
    private static $connection = null;
    private static $location = null;
    private static $session = null;

    /**
     * return SforceBaseClient
     */
    public static function factory()
    {
        if ( self::$connection )
        {
            return self::$connection;
        }
        $ini = eZINI::instance( "salesforce.ini" );
        $loadBlock = $ini->variable( 'Settings', 'LoadBlock' );
        $dataBlock = $ini->BlockValues[$loadBlock];
        if( file_exists( $dataBlock['File'] ) )
        {
            self::$connection = new SforceEnterpriseClient();
            self::$connection->createConnection( $dataBlock['File'] );

            if ( self::$session and self::$location )
            {
                
                self::$connection->setEndpoint( self::$location );
                self::$connection->setSessionHeader( self::$session );
            }
            else
            {
                self::$connection->login( $dataBlock['Username'], $dataBlock['Password'] . $dataBlock['Token'] );
                self::$location = self::$connection->getLocation();
                self::$session = self::$connection->getSessionId();
            }
            return self::$connection;
        }
        else
        {
            eZDebug::writeError( $dataBlock['File'] . ' does not exist.', __METHOD__ );
            return false;
        }
    }
}