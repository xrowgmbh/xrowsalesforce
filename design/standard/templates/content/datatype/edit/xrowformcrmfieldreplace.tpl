{def $getFiledsFromClass = array( 'Lead' )}
{if ezini_hasvariable( 'Settings', 'GetFieldsFromClass', 'salesforce.ini' )}
    {set $getFiledsFromClass = ezini( 'Settings', 'GetFieldsFromClass', 'salesforce.ini' )}
{/if}
{foreach $crmFields as $crmClass => $crmItem}
    {foreach $crmItem as $crmField}
    {if and( $crmField.type|eq( 'picklist' ), $crmField.picklistValues|count|gt( 0 ) )}
        {def $formElementItemOptionArray = array()}
        {if $content.form_elements|count|gt(0)}
            {foreach $content.form_elements as $key => $item}
            {if and( $item.name|eq( $crmField.name ), $item.option_array|count|gt( 0 ) )}{set $formElementItemOptionArray = $item.option_array}{break}{/if}
            {/foreach}
        {/if}
        <div id="yyypicklistvalueyyy_{$crmClass}_{$crmField.name}" style="display: none" class="xrowsaleforce-picklist-checkbox">
        {foreach $crmField.picklistValues as $picklistValue}
            {def $checkedString = '' $setDefault = ''}
            {if $formElementItemOptionArray|count|gt( 0 )}
                {foreach $formElementItemOptionArray as $formElementItemOptionItem}
                    {if $formElementItemOptionItem.value|eq( $picklistValue.value )}
                        {set $checkedString = ' checked="checked"'}
                        {if $formElementItemOptionItem.def}{set $setDefault = ' checked="checked"'}{/if}
                    {/if}
                {/foreach}
            {/if}
            <div class="float-break"><label><input name="x1XrowFormElementCRMField{$id}{$crmClass}{$crmField.name}[]" value="{$picklistValue.value|wash()|preg_replace( array( "/'/", '/"/' ), array( "\\'", '\\"' ) )}|{$picklistValue.label|wash()|preg_replace( array( "/'/", '/"/' ), array( "\\'", '\\"' ) )}" type="checkbox"{$checkedString} />{$picklistValue.label}</label> <input class="xrow-option-default-button" type="radio" name="x1XrowFormElementCRMField{$id}{$crmClass}{$crmField.name}Default" value="{$picklistValue.value|wash()|preg_replace( array( "/'/", '/"/' ), array( "\\'", '\\"' ) )}" title="{"Click here to select this value by default"|i18n( 'xrowformgenerator/edit' )}"{$setDefault} /> {"Default value"|i18n( 'xrowformgenerator/edit' )}</div>
            {undef $checkedString $setDefault}
        {/foreach}
        <div class="xrowsaleforce-optionnavi">
            <span><input type="checkbox" onclick="javascript:selectDeselectAllValues('x1XrowFormElementCRMField{$id}{$crmClass}{$crmField.name}[]', this);" />{"Select/deselect all values"|i18n( 'xrowsalesforce/edit' )}</span>
            <a onclick="javascript:resetDefaultValue('x1XrowFormElementCRMField{$id}{$crmClass}{$crmField.name}Default');">{"Reset default value"|i18n( 'xrowsalesforce/edit' )}</a></div>
        </div>
        {undef $formElementItemOptionArray}
    {/if}
    {/foreach}
{/foreach}