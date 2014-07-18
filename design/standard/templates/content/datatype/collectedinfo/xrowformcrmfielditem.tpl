<th>{$item.label}:</th>
<td>
    {switch match=$item.type}
        {case match="crmfield:boolean"}{if $item.def}{"Yes"|i18n( 'xrowformgenerator/mail' )}{else}{"No"|i18n( 'xrowformgenerator/mail' )}{/if}{/case}
        {case match="crmfield:picklist"}
            {foreach $item.option_array as $opt_key => $opt_item}
                {if $opt_item.def}{$opt_item.name}{/if}
            {/foreach}
       {/case}
       {case}{$item.def}{/case}
    {/switch}
</td>