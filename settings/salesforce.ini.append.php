<?php /* #?ini charset="utf-8"?

#[Settings]
#LoadBlock=SalesForceData_Test
#AlwaysLog=enabled
#SendErrorMails=enabled
#ReceiverArray[]
#ReceiverArray[]=xyz@test.com

#ExportFieldName[]
#ExportFieldName[e_mail_adresse]=Email
#ExportFieldName[vorname]=FirstName
#ExportFieldName[nachname]=LastName

#LoadFieldsInFormGenerator[]
#LoadFieldsInFormGenerator[]=Company
#LoadFieldsInFormGenerator[]=Email
#LoadFieldsInFormGenerator[]=FirstName
#LoadFieldsInFormGenerator[]=LastName
#LoadFieldsInFormGenerator[]=Salutation
#LoadFieldsInFormGenerator[]=Title

#GetFieldsFromClass[]
#GetFieldsFromClass[]=Lead

#WhereStrings[]
#WhereStrings[Campaign]=WHERE Status = 'Active'

# read this field and export to another
#ExportFieldIntoField[]
#ExportFieldIntoField[]=Country

#[ExportFieldIntoField_Country]
# export from e.g. Lead to CampaignMember
#ToClass=CampaignMember
#ToField=f42_Land__c


#[SalesForceData_Test]
#Username=xy@z.sandbox
#Password=
#Token=
#File=extension/xyz/share/salesforceTEST.enterprise.wsdl.xml

#[SalesForceData_Live]
#Username=xy@z
#Password=
#Token=
#File=extension/xyz/share/salesforce.enterprise.wsdl.xml

*/ ?>