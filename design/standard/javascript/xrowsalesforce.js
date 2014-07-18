function xrow_add_form_crm(ol_con_id, attribute_id, opt, attr_value, crmclass, form_elements_key, version) {
    if(form_elements_key != undefined) {
        //opt = JSON.stringify(opt);
        var tmp_attr_value_array = attr_value.split(":");
            attr_value_new = new Array();
        attr_value_new[0] = tmp_attr_value_array[0];
        attr_value_new[1] = crmclass;
        attr_value_new[2] = tmp_attr_value_array[1];
        attr_value = attr_value_new.join(':');
    }
    var attr_value_array = attr_value.split(":"),
        crm_class = attr_value_array[1],
        attr_type = attr_value_array[2];
    if(attr_value_array.length >= 5) {
        var crm_name = attr_value_array[3],
            label = attr_value_array[4];
    } else if (opt.name != undefined) {
        var crm_name = opt.name,
            label = opt.label;
    }
    if (opt.crmclass != undefined)
        crm_class = opt.crmclass;

    var picklist_name = 'yyypicklistvalueyyy_' + crm_class + '_' + crm_name,
        attr_value_id = 'xrow-form-element-crmfield-' + attr_type + '-' + attribute_id;

    var li_tpl = document.getElementById(attr_value_id);
    var ol_con = document.getElementById(ol_con_id);

    if (li_tpl && ol_con) {
        var new_li = document.createElement('li');
        new_li.className = li_tpl.className;

        var pattern_index = /yyyxrowindexyyy/g;
        var pattern_namefield = /yyyxrownamefieldyyy/g;
        var pattern_name = /yyyxrownameyyy/g;
        var pattern_crmclass = /yyyxrowcrmclassyyy/g;
        var pattern_label = /yyyxrowlabelyyy/g;
        var pattern_desc = /yyyxrowdescyyy/g;
        var pattern_xrow = /x1Xrow/g;
        var pattern_def = /yyyxrowdefyyy/g;
        var pattern_picklist = /yyypicklistvalueyyy/g;

        var temphtml = ieInnerHTML(li_tpl);
        temphtml = temphtml.replace(pattern_index, xrow_form_index);
        temphtml = temphtml.replace(pattern_xrow, 'Xrow');
        temphtml = temphtml.replace(pattern_namefield, label);
        temphtml = temphtml.replace(pattern_name, crm_name);
        temphtml = temphtml.replace(pattern_crmclass, crm_class);
        var def = '';
        if (opt.def != undefined) {
            if (attr_type == 'boolean') {
                if (opt.def)
                    def = '1" checked="checked';
            }
            else
                def = opt.def;
        }
        temphtml = temphtml.replace(pattern_def, def);
        var desc = '';
        if (opt.desc != undefined)
            desc = opt.desc;
        temphtml = temphtml.replace(pattern_desc, desc);
        // get html from the picklist
        if(attr_type == 'picklist') {
            var picklist = document.getElementById(picklist_name);
            if(picklist != undefined) {
                var picklistHTML = picklist.outerHTML;
                picklistHTML = picklistHTML.replace('style="display: none"', '');
                picklistHTML = picklistHTML.replace(pattern_xrow, 'Xrow');
                temphtml = temphtml.replace(pattern_picklist, picklistHTML);
            }
        }

        new_li.innerHTML = temphtml;

        // set required checkbox
        var req_array = YAHOO.util.Dom.getElementsByClassName('xrow-form-element-required', 'input', new_li);
        if ( req_array[0] != undefined && opt && opt.req != undefined && opt.req)
        {
            req_array[0].checked = true;
        }

        // set validation checkbox
        var val_array = YAHOO.util.Dom.getElementsByClassName('xrow-form-element-validation', 'input', new_li);
        if ( val_array[0] != undefined && opt && opt.val != undefined && opt.val)
        {
            val_array[0].checked = true;
        }
        
         // set unique checkbox
        var unique_array = YAHOO.util.Dom.getElementsByClassName('xrow-form-element-unique', 'input', new_li);
        if ( unique_array[0] != undefined && opt && opt.unique != undefined && opt.unique)
        {
            unique_array[0].checked = true;
        }

        // add move event
        YAHOO.util.Event.addListener( YAHOO.util.Dom.getElementsByClassName('xrow-element-button-up', 'img', new_li ), 'click', xrow_element_button_up);
        YAHOO.util.Event.addListener( YAHOO.util.Dom.getElementsByClassName('xrow-element-button-down', 'img', new_li ), 'click', xrow_element_button_down);

        // add trash event
        YAHOO.util.Event.addListener( YAHOO.util.Dom.getElementsByClassName('xrow-form-element-trash-button', 'img', new_li ), 'click', xrow_form_element_trash_button);

        if (ol_con.hasChildNodes())
            insertAfter(new_li, ol_con.lastChild);
        else
            ol_con.appendChild(new_li);
        xrow_form_index++;
    }
};
function resetDefaultValue(name) {
    var elements = document.getElementsByName(name),
        i;
    for(i in elements) {
        if(elements.hasOwnProperty(i)) {
            elements[i].checked = false;
        }
    }
};
function selectDeselectAllValues(name, checkbox) {
    var elements = document.getElementsByName(name);
    for (var i = 0; i < elements.length; i++) {
        elements[i].checked = checkbox.checked;
    }
};