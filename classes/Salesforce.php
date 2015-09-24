<?php

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
        $container = ezpKernel::instance()->getServiceContainer();
        $wsdlName = $container->getParameter('salesforce.wsdl');
        $wsdl = $container->getParameter('kernel.root_dir').'/../src/wuv/SalesforceBundle/Resources/config/'.$wsdlName;
        $username = $container->getParameter('salesforce.username');
        $password = $container->getParameter('salesforce.password');
        $token = $container->getParameter('salesforce.token');
        if( file_exists( $wsdl ) )
        {
            self::$connection = new SforceEnterpriseClient();
            self::$connection->createConnection( $wsdl );

            if ( self::$session and self::$location )
            {
                self::$connection->setEndpoint( self::$location );
                self::$connection->setSessionHeader( self::$session );
            }
            else
            {
                self::$connection->login( $username, $password . $token );
                self::$location = self::$connection->getLocation();
                self::$session = self::$connection->getSessionId();
            }
            return self::$connection;
        }
        else
        {
            eZDebug::writeError( $wsdl . ' does not exist.', __METHOD__ );
            return false;
        }
    }
}