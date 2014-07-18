<?php

class xrowSFJscoreFunctions extends ezjscServerFunctions
{
    public static function getAttributeValue()
    {
        $http = eZHTTPTool::instance();
        if( $http->hasPostVariable( 'attribute_id' ) && $http->hasPostVariable( 'form_elements_key' ) )
        {
            $attribute_id = $http->postVariable( 'attribute_id' );
            $form_elements_key = $http->postVariable( 'form_elements_key' );
            $version = $http->postVariable( 'version' );
            $attribute = eZPersistentObject::fetchObject( eZContentObjectAttribute::definition(),
                                                          null,
                                                          array( "id" => $attribute_id, "version" => $version ),
                                                          true );
            if( $attribute instanceof eZContentObjectAttribute )
            {
                $content = $attribute->content();
                if( isset( $content['form_elements'][$form_elements_key] ) )
                {
                    $form_element = $content['form_elements'][$form_elements_key];
                    return json_decode( $form_element['json'] );
                }
            }
        }
    }
}