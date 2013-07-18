<?php

//use XROW\Salesforce\Salesforce;

$connection = Salesforce::factory();

//Describing the Leads object and printing the array
$describe = $connection->describeSObjects(array('Lead'));
file_put_contents( "sf.test.txt", print_r( $describe, true ));


//Creating the Lead Object
$lead = new stdClass;
$lead->FirstName = "Björn";
$lead->LastName = "Dieding";
$lead->Company = "xrow GmbH";
$lead->Email = "bjoern@fake.xrow.net";
$result = $connection->create(array($lead), 'Lead');


//Creating the Lead Object
$leadmember = new stdClass;
$leadmember->CampaignId = "701J0000000JfZf";
$leadmember->LeadId = $result[0]->id;

$result = $connection->create(array($leadmember), 'CampaignMember');


var_dump($result);

#CampaignMember
//$result = $connection->convertLead($lead);
//Submitting the Lead to Salesforce
