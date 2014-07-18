{if $crmFields|count|gt( 0 )}
{ezscript_require( array( 'json2.js', 'xrowsalesforce.js' ) )}
{ezcss_require( array( 'xrowsalesforce.css' ) )}
{def $crmtypes = hash( 'string', hash( 'name', 'String', 'default', true(), 'required', true() ),
                       'email', hash( 'name', 'Email', 'default', true(), 'required', true(), 'unique', true(), 'validation', true() ),
                       'textarea', hash( 'name', 'Text', 'required', true() ),
                       'phone', hash( 'name', 'Telephone number', 'default', true(), 'required', true(), 'validation', true() ),
                       'boolean', hash( 'name', 'Checkbox', 'default', true(), 'required', true() ) )}
{foreach $crmtypes as $type => $typeElements}
        <li class="xrow-form-element xrow-form-element-crmfield-{$type}" id="xrow-form-element-crmfield-{$type}-{$id}">
            <fieldset>
                <legend>CRM: {concat( $typeElements.name, " input field" )|i18n( 'xrowformgenerator/edit' )} "yyyxrownamefieldyyy"</legend>
                <div class="block">
                    <div class="element xrow-trash-width"><img class="xrow-form-element-trash-button" src={"trash-icon-16x16.gif"|ezimage} alt="{"Delete form element."|i18n( 'xrowformgenerator/edit' )}"  title="{"Delete form element."|i18n( 'xrowformgenerator/edit' )}" width="16" height="16" /></div>
                    <div class="element xrow-form-element-width">
                        <input type="hidden" name="x1XrowFormElementArray{$id}[yyyxrowindexyyy]" value="yyyxrowindexyyy" />
                        <input type="hidden" name="x1XrowFormElementType{$id}[yyyxrowindexyyy]" value="crmfield:{$type}" />
                        <input type="hidden" name="x1XrowFormElementName{$id}[yyyxrowindexyyy]" value="yyyxrownameyyy" />
                        <input type="hidden" name="x1XrowFormElementCRMClass{$id}[yyyxrowindexyyy]" value="yyyxrowcrmclassyyy" />
                        <div class="block">
                            <label>{"Name"|i18n( 'xrowformgenerator/edit' )}:</label>
                            <input class="box" type="text" name="x1XrowFormElementCRM{$id}[yyyxrowindexyyy]" value="yyyxrownamefieldyyy" />
                        </div>
                        {if and( is_set( $typeElements.default ), $typeElements.default )}
                        <div class="block">
                            <label>{"Default value"|i18n( 'xrowformgenerator/edit' )}:</label>
                            {if $type|eq( 'boolean' )}
                            <input name="x1XrowFormElementDefault{$id}[yyyxrowindexyyy]" value="yyyxrowdefyyy" title="{"Use this checkbox if the checkbox should be selected by default."|i18n( 'xrowformgenerator/edit' )}" type="checkbox" />
                            {else}
                            <input class="box" type="text" name="x1XrowFormElementDefault{$id}[yyyxrowindexyyy]" value="yyyxrowdefyyy" />
                            {/if}
                        </div>
                        {/if}
                        <div class="block">
                            <label>{"Description"|i18n( 'xrowformgenerator/edit' )}:</label>
                            <textarea class="box" rows="2" cols="70" name="x1XrowFormElementDesc{$id}[yyyxrowindexyyy]">yyyxrowdescyyy</textarea>
                        </div>
                        {if or( and( is_set( $typeElements.required ), $typeElements.required ), and( is_set( $typeElements.validation ), $typeElements.validation ), and( is_set( $typeElements.unique ), $typeElements.unique ) )}
                        <div class="block inline">
                            {if and( is_set( $typeElements.required ), $typeElements.required )}<label><input class="xrow-form-element-required" name="x1XrowFormElementReq{$id}[yyyxrowindexyyy]" value="yyyxrowreqyyy" title="{"Use this checkbox if the input of this form field is required."|i18n( 'xrowformgenerator/edit' )}" type="checkbox" />{"Required"|i18n( 'xrowformgenerator/edit' )}</label>{/if}
                            {if and( is_set( $typeElements.validation ), $typeElements.validation )}<label><input class="xrow-form-element-unique" name="x1XrowFormElementUnique{$id}[yyyxrowindexyyy]" value="yyyxrowuniqueyyy" title="{"Unique"|i18n( 'xrowformgenerator/edit' )}" type="checkbox" />{"Unique"|i18n( 'xrowformgenerator/edit' )}</label>{/if}
                            {if and( is_set( $typeElements.unique ), $typeElements.unique )}<label><input class="xrow-form-element-validation" name="x1XrowFormElementVal{$id}[yyyxrowindexyyy]" value="yyyxrowvalyyy" title="{"Use this checkbox if the input of this form field should be validated."|i18n( 'xrowformgenerator/edit' )}" type="checkbox" />{"Input requires validation"|i18n( 'xrowformgenerator/edit' )}</label>{/if}
                        </div>
                        {/if}
                    </div>
                    <div class="element xrow-move-width"><img class="xrow-element-button-up" src={"button-move_up.gif"|ezimage} alt="{"Move up"|i18n( 'xrowformgenerator/edit' )}"  title="{"Move up"|i18n( 'xrowformgenerator/edit' )}" width="16" height="16" />&nbsp;<img class="xrow-element-button-down" src={"button-move_down.gif"|ezimage} alt="{"Move down"|i18n( 'xrowformgenerator/edit' )}" width="16" height="16" title="{"Move down"|i18n( 'xrowformgenerator/edit' )}" /></div>
                </div>
                <div class="break"></div>
            </fieldset>
        </li>
{/foreach}
{undef $crmtypes}
{* options/picklist *}
        <li class="xrow-form-element xrow-form-element-crmfield-picklist" id="xrow-form-element-crmfield-picklist-{$id}">
            <fieldset>
                <legend>CRM: {"Options input field"|i18n( 'xrowformgenerator/edit' )} "yyyxrownamefieldyyy"</legend>
                <div class="block">
                    <div class="element xrow-trash-width"><img class="xrow-form-element-trash-button" src={"trash-icon-16x16.gif"|ezimage} alt="{"Delete form element."|i18n( 'xrowformgenerator/edit' )}"  title="{"Delete form element."|i18n( 'xrowformgenerator/edit' )}" width="16" height="16" /></div>
                    <div class="element xrow-form-element-width">
                        <input type="hidden" name="x1XrowFormElementArray{$id}[yyyxrowindexyyy]" value="yyyxrowindexyyy" />
                        <input type="hidden" name="x1XrowFormElementType{$id}[yyyxrowindexyyy]" value="crmfield:picklist" />
                        <input type="hidden" name="x1XrowFormElementName{$id}[yyyxrowindexyyy]" value="yyyxrownameyyy" />
                        <input type="hidden" name="x1XrowFormElementCRMClass{$id}[yyyxrowindexyyy]" value="yyyxrowcrmclassyyy" />
                        <div class="block">
                            <label>{"Name"|i18n( 'xrowformgenerator/edit' )}:</label>
                            <input class="box" type="text" name="x1XrowFormElementCRM{$id}[yyyxrowindexyyy]" value="yyyxrownamefieldyyy" />
                        </div>
                        <div class="block">
                            <label>{"Select values"|i18n( 'xrowsalesforce/edit' )}:</label>
                            yyypicklistvalueyyy
                        </div>
                        <div class="block">
                            <label>{"Description"|i18n( 'xrowformgenerator/edit' )}:</label>
                            <textarea class="box" rows="2" cols="70" name="x1XrowFormElementDesc{$id}[yyyxrowindexyyy]">yyyxrowdescyyy</textarea>
                        </div>
                        <div class="block inline">
                            <label><input class="xrow-form-element-required" name="x1XrowFormElementReq{$id}[yyyxrowindexyyy]" value="yyyxrowreqyyy" title="{"Use this checkbox if the input of this form field is required."|i18n( 'xrowformgenerator/edit' )}" type="checkbox" />{"Required"|i18n( 'xrowformgenerator/edit' )}
                            </label>
                        </div>
                    </div>
                    <div class="element xrow-move-width"><img class="xrow-element-button-up" src={"button-move_up.gif"|ezimage} alt="{"Move up"|i18n( 'xrowformgenerator/edit' )}"  title="{"Move up"|i18n( 'xrowformgenerator/edit' )}" width="16" height="16" />&nbsp;<img class="xrow-element-button-down" src={"button-move_down.gif"|ezimage} alt="{"Move down"|i18n( 'xrowformgenerator/edit' )}" width="16" height="16" title="{"Move down"|i18n( 'xrowformgenerator/edit' )}" /></div>
                </div>
                <div class="break"></div>
            </fieldset>
        </li>
{/if}