<div class="xrowsaleforce-picklist-checkbox">
{foreach $crmField.picklistValues as $picklistValue}
    <div class="float-break"><label><input name="XrowFormElementCRMField{$id}{$crmField.name}[]" value="{$picklistValue.value}:{$picklistValue.label}" type="checkbox" data-replace="yyypicklistvalueyyy_{$crmField.name}_{$picklistValue.value}" />{$picklistValue.label}</label> <input class="xrow-option-default-button" type="radio" name="XrowFormElementCRMField{$id}{$crmField.name}Default" value="{$picklistValue.value}" title="{"Click here to select this value by default"|i18n( 'xrowformgenerator/edit' )}" data-replace="yyypicklistvaluedefaultyyy_{$crmField.name}_{$picklistValue.value}" /> {"Default value"|i18n( 'xrowformgenerator/edit' )}</div>
{/foreach}
</div>