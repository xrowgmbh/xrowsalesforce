{def $labelName = $item.label|wash
     $fieldName = $item.name
     $type = $item.type
     $changeFieldType = array()}
{if ezini_hasvariable( 'Settings', 'ChangeFieldType', 'salesforce.ini' )}
    {set $changeFieldType = ezini( 'Settings', 'ChangeFieldType', 'salesforce.ini' )}
{/if}
{if is_set( $changeFieldType[$fieldName] )}
    {set $type = concat( 'crmfield:', $changeFieldType[$fieldName] )}
{/if}
{switch match=$type}
    {case match="crmfield:picklist"}
        {if $item.option_array|count|gt( 0 )}
            <label class="options">{$labelName}{if $item.req}<abbr class="required" title="{"Input required."|i18n( 'kernel/classes/datatypes' )}"> * </abbr>{/if}</label>
            <select class="xrow-form-{$item.type}{cond( $item.class|ne(''), concat( ' ', $item.class ), '')}" name="XrowFormInputCRM[{$id}][{$key}][{$fieldName}]">
                <option value="0"></option>
            {foreach $item.option_array as $opt_key => $opt_item}
                <option value="{$opt_item.value|wash}"{if $opt_item.def} selected="selected"{/if} title="{$opt_item.name|wash}">{$opt_item.name|wash}</option>
            {/foreach}
            </select>
            <p class="input_desc">{cond( is_set( $item.desc ), $item.desc, '')}</p>
        {/if}
    {/case}
    {case match="crmfield:boolean"}
        <label for="checkbox:{$id}:{$key}"{if $labelName|trim|eq('')} class="emptyname"{/if}><input id="checkbox:{$id}:{$key}" type="checkbox" name="XrowFormInputCRM[{$id}][{$key}][{$fieldName}]" value="1" autocomplete="off" {if $item.def}checked="checked" {/if}/>{if $labelName|trim|ne('')}&nbsp;{$labelName}{if $item.req}<abbr class="required" title="{"Input required."|i18n( 'kernel/classes/datatypes' )}"> * </abbr>{/if}{/if}</label>
        <div class="form-checkbox-padding{if $labelName|trim|eq('')} emptyname{/if}">{cond( is_set( $item.desc ), $item.desc, '')}{if $labelName|trim|eq('')}{if $item.req}<abbr class="required" title="{"Input required."|i18n( 'kernel/classes/datatypes' )}"> * </abbr>{/if}{/if}</div>
    {/case}
    {case match="crmfield:phone"}
        <label for="telephonenumber:{$id}:{$key}:number">{$labelName}{if $item.req}<abbr class="required" title="{"Input required."|i18n( 'kernel/classes/datatypes' )}"> * </abbr>{/if}</label>
        <input{if not( $content.has_error )} placeholder="{$item.def|wash}"{else} value="{$item.def|wash}"{/if} id="telephonenumber:{$id}:{$key}" type="tel" autocomplete="off" name="XrowFormInputCRM[{$id}][{$key}][{$fieldName}]" class="box xrow-form-{$item.type}{cond( $item.class|ne(''), concat( ' ', $item.class ), '')}" aria-required="true" /><br/>
        <p class="input_desc">{cond( is_set( $item.desc ), $item.desc, '')}</p>
    {/case}
    {case match="crmfield:email"}
        <label for="email:{$id}:{$key}">{$labelName}{if $item.req}<abbr class="required" title="{"Input required."|i18n( 'kernel/classes/datatypes' )}"> * </abbr>{/if}</label>
        <input id="email:{$id}:{$key}" type="email" autocomplete="off" name="XrowFormInputCRM[{$id}][{$key}][{$fieldName}]" class="box xrow-form-{$item.type}{cond( $item.class|ne(''), concat( ' ', $item.class ), '')}" aria-required="true" {if not($content.has_error)} placeholder="{$item.def|wash}" {else} value="{$item.def|wash}" {/if} />
        <p class="input_desc">{cond( is_set( $item.desc ), $item.desc, '')}</p>
    {/case}
    {case match="crmfield:textarea"}
        <label for="text:{$id}:{$key}">{$labelName}{if $item.req}<abbr class="required" title="{"Input required."|i18n( 'kernel/classes/datatypes' )}"> * </abbr>{/if}</label>
        <textarea cols="70" rows="10" id="text:{$id}:{$key}" name="XrowFormInputCRM[{$id}][{$key}]" class="box xrow-form-{$item.type}{cond( $item.class|ne(''), concat( ' ', $item.class ), '')}" aria-required="true"  {if not($content.has_error)}placeholder="{$item.def|wash}">{else}>{$item.def|wash}{/if}</textarea>
        <p class="input_desc">{cond( is_set( $item.desc ), $item.desc, '')}</p>
    {/case}
    {case}
        <label for="description:{$id}:{$key}">{$labelName}{if $item.req}<abbr class="required" title="{"Input required."|i18n( 'kernel/classes/datatypes' )}"> * </abbr>{/if}</label>
        <input id="description:{$id}:{$key}" type="text" autocomplete="off" name="XrowFormInputCRM[{$id}][{$key}][{$fieldName}]" class="box xrow-form-{$item.type}{cond( $item.class|ne(''), concat( ' ', $item.class ), '')}" aria-required="true" {if not($content.has_error)} placeholder="{$item.def|wash}" {else} value="{$item.def|wash}" {/if} />
        <p class="input_desc">{cond( is_set( $item.desc ), $item.desc, '')}</p>
    {/case}
{/switch}
<input class="formhidden" type="hidden" name="XrowFormInputTypeCRM[{$id}][{$key}][{$fieldName}]" value="{$item.type}" />
{undef $labelName $fieldName $type $changeFieldType}