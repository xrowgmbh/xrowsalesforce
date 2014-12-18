{def $labelName = ''
     $fieldName = $item.name
     $type = $item.type
     $changeFieldType = array()
     $labelOff = false()}
{if is_set($item.label)}{set $labelName = $item.label|wash}{/if}
{if ezini_hasvariable( 'Settings', 'ChangeFieldType', 'salesforce.ini' )}
    {set $changeFieldType = ezini( 'Settings', 'ChangeFieldType', 'salesforce.ini' )}
{/if}
{if is_set( $changeFieldType[$fieldName] )}
    {set $type = concat( 'crmfield:', $changeFieldType[$fieldName] )}
{/if}
{if is_set($labelOffOverwrite)}
    {set $labelOff = $labelOffOverwrite}
{/if}
{switch match=$type}
    {case match="crmfield:picklist"}
        {if $item.option_array|count|gt( 0 )}
            {switch match=$labelName}
                {case match="Land"}{def $emptyText = "Bitte wählen Sie Ihr Land aus."}{/case}
                {case}{def $emptyText = concat("Bitte wählen Sie Ihre ", $labelName, " aus.")}{/case}
            {/switch}
            {include uri='design:content/datatype/fields.tpl' 
                     fieldType=options
                     itemNameOverwrite=$labelName
                     overwriteNameValue=concat('XrowFormInputTypeCRM[', $id, '][', $key, '][', $fieldName, ']')
                     emptyText=$emptyText
                     labelOff=$labelOff
                     underFieldType=select-one
                     startWithEmptyValue=true()
                     autocompleteOff=true()
                     cssClass=concat("xrow-form-", $item.type, cond( $item.class|ne(''), concat( ' ', $item.class ), ''))}
            {undef $emptyText}
        {/if}
    {/case}
    {case match="crmfield:boolean"}
        {def $emptyText = ''}
        {if $item.desc|contains('Datenschutz')}{set $emptyText = concat("Bitte akzeptieren Sie die Datenschutzbedingungen.") $labelName = ''}{/if}
        {include uri='design:content/datatype/fields.tpl' 
                 fieldType=checkbox
                 itemNameOverwrite=$labelName
                 overwriteNameValue=concat('XrowFormInputTypeCRM[', $id, '][', $key, '][', $fieldName, ']')
                 emptyText=$emptyText
                 labelOff=$labelOff
                 autocompleteOff=true()}
        {undef $emptyText}
    {/case}
    {case match="crmfield:phone"}
        {include uri='design:content/datatype/fields.tpl'
                 fieldType=telephonenumber
                 itemNameOverwrite=$labelName
                 overwriteNameValue=concat('XrowFormInputTypeCRM[', $id, '][', $key, '][', $fieldName, ']')
                 emptyText="Bitte geben Sie Ihre Telefonnummer ein."
                 invalidText="Bitte geben Sie eine korrekte Telefonnummer ein."
                 labelOff=$labelOff
                 autocompleteOff=true()
                 cssClass=concat("box xrow-form-", $item.type, cond( $item.class|ne(''), concat( ' ', $item.class ), ''))}
    {/case}
    {case match="crmfield:email"}
        {include uri='design:content/datatype/fields.tpl' 
                 fieldType=email
                 itemNameOverwrite=$labelName
                 overwriteNameValue=concat('XrowFormInputTypeCRM[', $id, '][', $key, '][', $fieldName, ']')
                 emptyText="Bitte geben Sie Ihre E-Mail-Adresse ein."
                 invalidText="Bitte geben Sie eine korrekte E-Mail-Adresse ein."
                 labelOff=$labelOff
                 autocompleteOff=true()
                 cssClass=concat("box xrow-form-", $item.type, cond( $item.class|ne(''), concat( ' ', $item.class ), ''))}
    {/case}
    {case match="crmfield:textarea"}
        {include uri='design:content/datatype/fields.tpl' 
                 fieldType='textarea'
                 itemNameOverwrite=$labelName
                 overwriteNameValue=concat('XrowFormInputTypeCRM[', $id, '][', $key, '][', $fieldName, ']')
                 emptyText=concat("Bitte geben Sie ", $labelName, " ein.")
                 labelOff=$labelOff
                 cssClass=concat("box xrow-form-", $item.type, cond( $item.class|ne(''), concat( ' ', $item.class ), ''))
                 cols=70
                 rows=10
                 autocompleteOff=true()}v
    {/case}
    {case}
        {switch match=$item.name}
            {case match="Vorname"}{def $emptyText = "Bitte geben Sie Ihren Vornamen ein."}{/case}
            {case match="Nachname"}{def $emptyText = "Bitte geben Sie Ihren Nachnamen ein."}{/case}
            {case}{def $emptyText = concat("Bitte geben Sie Ihre ", $labelName, " ein.")}{/case}
        {/switch}
        {include uri='design:content/datatype/fields.tpl' 
                 fieldType='text'
                 itemNameOverwrite=$labelName
                 overwriteNameValue=concat('XrowFormInputTypeCRM[', $id, '][', $key, '][', $fieldName, ']')
                 emptyText=$emptyText
                 labelOff=$labelOff
                 autocompleteOff=true()
                 cssClass=concat("box xrow-form-", $item.type, cond( $item.class|ne(''), concat( ' ', $item.class ), ''))}
        {undef $emptyText}
    {/case}
{/switch}
<input class="formhidden" type="hidden" name="XrowFormInputTypeCRM[{$id}][{$key}][{$fieldName}]" value="{$item.type}" />
{undef $labelName $fieldName $type $changeFieldType $labelOff}