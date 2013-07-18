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

        $file = "extension/xrowsalesforce/share/salesforce.enterprise.wsdl.xml";

        /*
         * $file = eZSys::cacheDirectory() . "/salesforce.enterprise.wsdl.xml"; if (!file_exists($file)) { file_put_contents( $file, file_get_contents( "https://cs10.salesforce.com/soap/wsdl.jsp?type=*" ) ); echo "$file"; }
         */
        self::$connection = new SforceEnterpriseClient();
        self::$connection->createConnection( $file );

        if ( self::$session and self::$location )
        {
            
            self::$connection->setEndpoint( self::$location );
            self::$connection->setSessionHeader( self::$session );
        }
        else
        {
            self::$connection->login( $ini->variable( 'Settings', 'Username' ), $ini->variable( 'Settings', 'Password' ) . $ini->variable( 'Settings', 'Token' ) );
            self::$location = self::$connection->getLocation();
            self::$session = self::$connection->getSessionId();
        }
        return self::$connection;
    }
}