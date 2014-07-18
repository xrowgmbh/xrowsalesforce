{def $labelName = $item.label|wash
     $fieldName = $item.name}
{switch match=$item.type}
    {case match="crmfield:picklist"}
        <label>{$labelName}{if $item.req}*{/if}</label>
        {foreach $item.option_array as $opt_key => $opt_item}
            {if $opt_item.def}{$opt_item.name}{break}{/if}
        {/foreach}
    {/case}
    {case match="crmfield:boolean"}
        <label>{$labelName}{if $item.req}*{/if}</label>
        {if $item.def}{"Yes"|i18n( 'xrowformgenerator/mail' )}{else}{"No"|i18n( 'xrowformgenerator/mail' )}{/if}
    {/case}
    {case}
        <label>{$labelName}{if $item.req}*{/if}</label>
        <p>{$item.def|wash}</p>
    {/case}
{/switch}