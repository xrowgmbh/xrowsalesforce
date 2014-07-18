{if $crmFields|count|gt( 0 )}
{def $getFiledsFromClass = array( 'Lead' )}
{if ezini_hasvariable( 'Settings', 'GetFieldsFromClass', 'salesforce.ini' )}
    {set $getFiledsFromClass = ezini( 'Settings', 'GetFieldsFromClass', 'salesforce.ini' )}
{/if}
    <option value="" disabled="disabled">------------{"CRM fields"|i18n( 'xrowformgenerator/edit' )}---------</option>
    {foreach $crmFields as $crmClass => $crmItem}
    <optgroup label="{$crmClass}">
        {foreach $crmItem as $crmField}
        <option value="crmfield:{$crmClass}:{$crmField.type}:{$crmField.name}:{$crmField.label}">{$crmField.label}</option>
        {/foreach}
    </optgroup>
    {/foreach}
{/if}