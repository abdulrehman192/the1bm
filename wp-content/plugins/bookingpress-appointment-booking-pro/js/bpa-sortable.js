"use strict";class BPASortable{constructor(){var e=document.getElementById("bpa-input-fields"),e=(null!=e&&(e=new Sortable(e,{group:{name:"bpa-fields",pull:"clone",put:!1},animation:0,sort:!1,filter:".bpa-restricted, .bpa-form-element-col__disabled"}),app.bpa_sortable_data.push(e)),document.getElementById("bpa-column-fields")),e=(null!=e&&(e=new Sortable(e,{group:{name:"bpa-fields",pull:"clone",put:!1},animation:0,sort:!1,filter:".bpa-restricted, .bpa-form-element-col__disabled"}),app.bpa_sortable_data.push(e)),BPASortable.bpa_load_customer_fields(),document.getElementById("bpa-draggable-container")),e=new Sortable(e,{group:{name:"bpa-fields"},animation:150,swapThreshold:.7,ghostClass:"bpa-sortable-placeholder",filter:".bpa-inner-field-container:not(.bpa-repeater-field-wrapper-container)",onAdd:BPASortable.add_field_from_list,onEnd:BPASortable.sort_field_inside_list});app.bpa_sortable_data.push(e);let t=document.querySelectorAll(".bpa-inner-field-container.inner-field-blank-container"),i=(0<t.length&&t.forEach((e,t)=>{e=new Sortable(e,{group:{name:"bpa-fields",put:function(){var e=arguments[1];let t=arguments[2];return!(null==t||!t.classList.contains("bpa-field-outer-container"))||(null==t||"repeater"!=t.getAttribute("data-type"))&&("bpa-input-fields"==e.el.id||"bpa-customer-fields"==e.el.id)}},direction:"horizontal",animation:150,swapThreshold:.7,ghostClass:"bpa-sortable-placeholder",onAdd:function(o){let i=o.item;if(void 0!==i.classList&&i.classList.contains("bpa-field-outer-container")){let d=i.getAttribute("data-id"),n=app.field_settings_fields[d],e=o.to.parentNode,s=e.getAttribute("data-id");n.is_blank=!1;var l=o.to.getAttribute("data-id");let a=BPASortable.BPAGetParents(o.target,".bpa-field-col-parent-container"),t=!1;if(t=0!=a.length&&0!=(a=BPASortable.BPAGetParents(o.target,".bpa-cfs-ic--body__repeater-field-preview")).length?!0:t){a[0].getAttribute("data-repeater-id");s=a[0].getAttribute("data-id");let e=BPASortable.BPAGetParents(o.target,".bpa-field-col-parent-container"),i=e[0].getAttribute("data-fkey");var r=o.target.getAttribute("data-id"),_=n.id,_=0!=parseInt(_)?"inner_field_"+_:"inner_field_"+(_=_.replace("inner_field_",""));n.id=_,app.field_settings_fields[s].field_options.inner_fields[i].field_options.inner_fields[r]=n;let l=.1,t=(app.field_settings_fields[s].field_options.inner_fields[i].field_options.inner_fields.forEach((e,t)=>{l+=i+.1,app.$set(app.field_settings_fields[s].field_options.inner_fields[i].field_options.inner_fields[t],"field_position",l)}),JSON.parse(JSON.stringify(app.field_settings_fields)));t=t.filter((e,t)=>{if(d!=t)return e}),app.$set(app,"field_settings_fields",t),app.bpa_sortable_data=[],app.$forceUpdate(),setTimeout(function(){new BPASortable},500)}else{app.field_settings_fields[s].field_options.inner_fields[l]=n,delete app.field_settings_fields[d];let e=app.field_settings_fields,a=[],t=document.getElementsByClassName("bpa-field-container");if(0<t.length){let i=0,l=(t.forEach((e,t)=>{e.setAttribute("data-id",i),i++}),0);e.forEach((e,t)=>{a[l]=e,a[l].field_position=t+1,l++})}app.field_settings_fields=a,app.bpa_sortable_data.forEach((e,t)=>{null!=e.el&&null!=Sortable.get(e.el)&&Sortable.get(e.el).destroy()}),app.bpa_sortable_data=[],app.$forceUpdate(),setTimeout(function(){new BPASortable},500)}}else BPASortable.add_to_inner_list(o)}});app.bpa_sortable_data.push(e)}),document.querySelectorAll(".bpa-cfs-ic--body__repeater-field-preview")),l=(0<i.length&&i.forEach((e,t)=>{e=new Sortable(e,{group:{name:"bpa-fields",put:function(){let e=arguments[2];var t=e.getAttribute("data-id")||null;if(null!=e&&(e.classList.contains("bpa-repeater-field")||"password"==e.getAttribute("data-type"))||"column"==e.getAttribute("data-type"))return!1;if(null!=t){t=app.field_settings_fields[t];if(null!=t&&void 0!==t&&("username"==t.field_name||"terms_and_conditions"==t.field_name||"Password"==t.field_type||"2_col"==t.field_type||"3_col"==t.field_type||"4_col"==t.field_type))return!1}}},animation:150,swapThreshold:.7,filter:".bpa-repeater-field",onAdd:function(e){BPASortable.add_field_from_list_into_repeater(e)},onEnd:BPASortable.sort_field_inside_repeater_list});app.bpa_sortable_data.push(e)}),document.querySelectorAll(".bpa-field-col-parent-container"));0<l.length&&l.forEach(e=>{e=new Sortable(e,{group:{name:"bpa-fields",put:!1},sort:!0,direction:"horizontal",animation:150,swapThreshold:.7,ghostClass:"bpa-sortable-placeholder",onEnd:function(e){var t=e.item,i=e.from;t.parentNode==i&&BPASortable.on_swap_fields(e)}});app.bpa_sortable_data.push(e)}),0<document.querySelectorAll(".bpa-cfs-ic--body__repeater-field-preview").length&&document.querySelectorAll(".bpa-cfs-ic--body__repeater-field-preview").forEach((e,t)=>{BPASortable.sort_repeater_inner_fields(e)})}static bpa_load_customer_fields(){var e=document.getElementById("bpa-customer-fields");null!==e&&(e=new Sortable(e,{group:{name:"bpa-fields",pull:"clone",put:!1},animation:0,sort:!1,filter:".bpa-restricted"}),app.bpa_sortable_data.push(e))}static add_field_from_list_into_repeater(e,t=0){let i=e.item;var l=i.getAttribute("data-type"),a=e.newIndex;let d=e.from;var n=e.target,s=e.target.getAttribute("data-repeater-id")||-1;(null===d||"2_col"!=d.getAttribute("data-type")&&"3_col"!=d.getAttribute("data-type")&&"4_col"!=d.getAttribute("data-type"))&&BPASortable.bpa_add_field_to_repeater_list(l,a,!1,e.item,s,n,d)}static add_field_from_list(t,e=0){let p=t.item;var i=p.getAttribute("data-type");let f=t.newIndex,c=t.from;if(null!==c&&("2_col"==c.getAttribute("data-type")||"3_col"==c.getAttribute("data-type")||"4_col"==c.getAttribute("data-type"))){let o=parseInt(p.getAttribute("data-field-id")),t=parseInt(c.getAttribute("data-field-id")),e=app.field_settings_fields,l=[];e.forEach((e,s)=>{var i=e.id;if(i==t){let n=e.field_options.inner_fields;n.forEach((i,t)=>{if(i.id==o){var l,a={is_blank:!0,id:BPASortable.bpa_generate_field_id(),field_options:{is_customer_field:!1},field_position:i.field_position};let e=document.createElement("div");e.setAttribute("class","bpa-inner-field-container bpa-two-col-container bpa-field-wrapper-container el-col el-col-24 el-col-xs-12 el-col-sm-12 el-col-md-12 el-col-lg-12 el-col-xl-12 inner-field-blank-container"),e.setAttribute("data-fkey",a.field_position),e.setAttribute("data-id",0),e.setAttribute("data-blank-elm","true"),e.setAttribute("data-field-id",BPASortable.bpa_generate_field_id()),0==a.field_position?c.prepend(e):a.field_position==n.length-1?c.appendChild(e):(l=i.field_position+1,l=c.querySelector('.bpa-inner-field-container[data-id="'+l+'"]'),c.insertBefore(e,l));var d=document.getElementsByClassName("bpa-inner-field-container");if(app.field_settings_fields[s].field_options.inner_fields[t]=a,0<d.length)for(let t=0;t<d.length;t++){let e=d[t].parentNode;null==e||e.classList.contains("bpa-field-container")||e.classList.contains("bpa-cfs-ic--body__field-preview")||e.classList.contains("bpa-field-col-parent-container")||(delete i.innerIndex,delete i.field_options.parent_field,app.field_settings_fields.push(i))}else app.$forceUpdate()}});e=n.length;let t=0;n.forEach(e=>{!e.is_blank||t++}),e==t&&l.push(i)}});var n=document.getElementsByClassName("bpa-field-wrapper-container");let a=[];for(let e=0;e<n.length;e++){var s=n[e],r=s.parentNode;null!=r&&"bpa-draggable-container"==r.id&&a.push(s)}let d=app.field_settings_fields;if(0<a.length){let i=0;a.forEach((e,t)=>{e.setAttribute("data-id",i),i++}),d.forEach((e,t)=>{let i=document.querySelector('.bpa-field-container[data-field-id="'+e.id+'"]');if(null==i)return!0;var l=i.getAttribute("data-id");e.field_position=parseInt(l)}),app.field_settings_fields.sort((e,t)=>e.field_position>t.field_position?1:t.field_position>e.field_position?-1:0)}return setTimeout(function(){BPASortable.remove_multicol_after_sort(l)},100),!1}if("repeater"==c.getAttribute("data-type")){let n=parseInt(p.getAttribute("data-field-id")),e=void parseInt(c.getAttribute("data-field-id")),t=app.field_settings_fields;e=parseInt(c.getAttribute("data-repeater-id"));let s="";let o={};return t.forEach((i,a)=>{if(i.id==e){let e=i.field_options.inner_fields;e.forEach((i,e)=>{if(i.id==n){var t={is_blank:!0,id:BPASortable.bpa_generate_field_id(),field_options:{is_customer_field:!1},field_position:i.field_position},l=document.getElementsByClassName("bpa-inner-field-container");if(app.field_settings_fields[a].field_options.inner_fields[e]=t,void 0===o[a]&&(o[a]=[]),o[a].push(e),0<l.length)for(let t=0;t<l.length;t++){let e=l[t].parentNode;if(null!=e&&!e.classList.contains("bpa-field-container")){delete i.innerIndex,delete i.field_options.parent_field,delete i.field_options.inner_fields,app.field_settings_fields.splice(f,0,i),f<=s?s+=1:s-=1;break}}else app.$forceUpdate()}});var l;e.length;let t=0;if(e.forEach(e=>{!e.is_blank||t++}),void 0!==o&&0<Object.keys(o).length){for(var d in o)void 0!==app.field_settings_fields[d].field_options.inner_fields&&(l=app.field_settings_fields[d].field_options.inner_fields.filter(BPASortable.bpa_check_for_blank),app.field_settings_fields[d].field_options.inner_fields=l);app.$forceUpdate()}app.$forceUpdate(),app.bpa_sortable_data=[],app.$forceUpdate(),setTimeout(function(){new BPASortable},1e3)}}),app.bpa_sortable_data=[],app.$forceUpdate(),setTimeout(function(){new BPASortable},500),!1}{let e=!1;c.getAttribute("id");let l=BPASortable.BPAGetParents(c,".bpa-field-col-parent-container");if(e=0!=l.length&&0!=(l=BPASortable.BPAGetParents(c,".bpa-cfs-ic--body__repeater-field-preview")).length?!0:e){parseInt(p.getAttribute("data-field-id"));let n=parseInt(c.getAttribute("data-field-id")),t=l[0].getAttribute("data-repeater-id"),e=app.field_settings_fields,i=[],s=parseInt(p.getAttribute("data-fkey")),o="",d=(e.forEach((e,d)=>{if(e.id==t){let a=e.field_options.inner_fields;a.forEach((t,i)=>{if(t.id==n){var l={is_blank:"true",id:BPASortable.bpa_generate_field_id(),field_options:{is_customer_field:!1},field_position:t.field_position+.1};let e=document.createElement("div");e.setAttribute("class","bpa-inner-field-container bpa-two-col-container bpa-field-wrapper-container bpa-field-col-inner-parent-container el-col el-col-24 el-col-xs-12 el-col-sm-12 el-col-md-12 el-col-lg-12 el-col-xl-12 inner-field-blank-container"),e.setAttribute("data-fkey",l.field_position),e.setAttribute("data-id",0),e.setAttribute("data-blank-elm","true"),e.setAttribute("data-field-id",BPASortable.bpa_generate_field_id()),0==s?c.prepend(e):s==a.length-1?c.appendChild(e):(t=t.field_position+1,t=c.querySelector('.bpa-inner-field-container[data-id="'+t+'"]'),c.insertBefore(e,t));document.getElementsByClassName("bpa-inner-field-container");o=JSON.parse(JSON.stringify(app.field_settings_fields[d].field_options.inner_fields[i].field_options.inner_fields[s])),app.$set(app.field_settings_fields[d].field_options.inner_fields[i].field_options.inner_fields,s,l)}});a.length;let t=0;a.forEach(e=>{!e.is_blank||t++}),t}}),""!=o&&(delete o.innerIndex,delete o.field_options.parent_field,delete o.field_options.inner_fields,app.field_settings_fields.splice(f,0,o)),app.field_settings_fields),r=[],_=document.getElementsByClassName("bpa-field-container");if(0<_.length){let i=1,l=(_.forEach((e,t)=>{e.setAttribute("data-id",i),i++}),0),a=1;d.forEach((e,t)=>{r[l]=e,r[l].field_position=a,l++,a++}),app.field_settings_fields=r}return setTimeout(function(){BPASortable.remove_multicol_after_sort(i)},100),app.$forceUpdate(),app.bpa_sortable_data=[],app.$forceUpdate(),setTimeout(function(){new BPASortable},1e3),!1}BPASortable.bpa_add_field_to_list(i,f,!1,p,t)}}static bpa_check_for_blank(e){return void 0!==e.is_blank&&0==e.is_blank}static remove_multicol_after_sort(l){if(0<l.length)for(let i=0;i<l.length;i++){var a=l[i],a=document.querySelector('.bpa-field-container[data-field-id="'+a+'"]').getAttribute("data-id"),n=(document.querySelector('.bpa-field-container[data-id="'+a+'"]').replaceWith(""),app.field_settings_fields[a].id);app.bpa_deleted_fields.push(n),delete app.field_settings_fields[a];let e=app.field_settings_fields,d=[],t=document.getElementsByClassName("bpa-field-container");if(0<t.length){let i=1,l=(t.forEach((e,t)=>{e.setAttribute("data-id",i),i++}),0),a=1;e.forEach((e,t)=>{d[l]=e,d[l].field_position=a,l++,a++}),app.field_settings_fields=d}}}static bpa_get_field_data(t,i,l){let a={error_message:"This field can not be blank"};if(a.field_position=i,a.id=BPASortable.bpa_generate_field_id(),a.is_edit=!1,a.is_hide=!1,a.is_default=0,a.is_required=!1,a.is_delete=!1,a.placeholder="",a.field_options={layout:"1col",inner_class:"1col",visibility:"always",is_customer_field:"false",selected_services:[]},-1<l){i=app.customer_fields[l];a.field_options.is_customer_field=!0,a.field_options.customer_field_id=i.bookingpress_form_field_id,"text"==t?a.field_type="Text":"textarea"==t?a.field_type="Textarea":"checkbox"==t?(a.field_type="Checkbox",a.enable_preset_fields=!1,a.is_edit_values=!1,a.preset_field_choice="",a.field_values=JSON.parse(i.bookingpress_field_values)):"radio"==t?(a.field_type="Radio",a.enable_preset_fields=!1,a.is_edit_values=!1,a.preset_field_choice="",a.field_values=JSON.parse(i.bookingpress_field_values)):"dropdown"==t?(a.field_type="Dropdown",a.enable_preset_fields=!1,a.is_edit_values=!1,a.preset_field_choice="",a.field_values=JSON.parse(i.bookingpress_field_values)):"datepicker"!=t&&"date"!=t||(a.field_type="Date",l=JSON.parse(i.bookingpress_field_options),a.field_options.enable_timepicker="true"==l.enable_timepicker||1==l.enable_timepicker),a.field_name=i.bookingpress_form_field_name,a.label=i.bookingpress_field_label,a.placeholder=i.bookingpress_field_placeholder,a.meta_key=i.bookingpress_field_meta_key;let e=document.querySelector(`.bpa-cs__item[data-customer-field-meta="${a.meta_key}"]`);e.classList.add("bpa-restricted")}else"single_line"==t||"text"==t?(a.field_name="Single Line",a.field_type="Text",a.label="Single Line",a.placeholder="Single Line"):"textarea"==t?(a.field_name="Multi Line",a.field_type="Textarea",a.label="Multi Line",a.placeholder="Multi Line"):"checkbox"==t?(a.field_name="Checkbox",a.field_type="Checkbox",a.label="Checkbox",a.field_values=[{value:"Option 1",label:"Option 1"},{value:"Option 2",label:"Option 2"}],a.enable_preset_fields=!1,a.is_edit_values=!1,a.preset_field_choice=""):"radio"==t?(a.field_name="Radio",a.field_type="Radio",a.label="Radio",a.field_values=[{value:"Option 1",label:"Option 1"},{value:"Option 2",label:"Option 2"}],a.enable_preset_fields=!1,a.is_edit_values=!1,a.preset_field_choice=""):"dropdown"==t?(a.field_name="Dropdown",a.field_type="Dropdown",a.placeholder="Dropdown",a.label="Dropdown",a.field_values=[{value:"Option 1",label:"Option 1"},{value:"Option 2",label:"Option 2"}],a.enable_preset_fields=!1,a.is_edit_values=!1,a.preset_field_choice=""):"datepicker"==t?(a.field_name="Datepicker",a.field_type="Date",a.label="Datepicker",a.placeholder="Datepicker",a.field_options.enable_timepicker=!1):"file_upload"==t?(a.field_name="File",a.field_type="File",a.label="File Upload",a.field_options.allowed_file_ext="jpg,png,gif,jpeg,ico,txt,doc,docx,pdf,csv,xls,xlsx,ods,odt",a.field_options.max_file_size=2,a.field_options.invalid_field_message="Invalid file selected"):"password"==t&&(a.field_name="Password",a.field_type="Password",a.label="Password",a.placeholder="Enter your password",a.is_required=!0),a.meta_key=BPASortable.bpa_generate_meta_key(a.field_type);return a.is_blank=!1,a.field_options.minimum="",a.field_options.maximum="",a.css_class="",a}static sort_field_inside_repeater_list(e){let t=e.target;null!=t&&t.classList.contains("bpa-cfs-ic--body__repeater-field-preview")&&BPASortable.sort_repeater_inner_fields(t)}static sort_repeater_inner_fields(t){let i=t.querySelectorAll(".bpa-inner-field-container.bpa-field-wrapper-container:not(.bpa-field-col-inner-parent-container)"),n=t.getAttribute("data-id");if(null!=i&&0<i.length){let a=0,d=0,e=(i.forEach((e,t)=>{var i=e.getAttribute("data-fkey");if(null==e||void 0===app.field_settings_fields[n].field_options.inner_fields[i]||null==app.field_settings_fields[n].field_options.inner_fields[i])return!0;let l=JSON.parse(JSON.stringify(app.field_settings_fields[n].field_options.inner_fields[i]));d+=1,l.innerIndex=a,l.field_position=d,app.$set(app.field_settings_fields[n].field_options.inner_fields,i,l),a++}),JSON.parse(JSON.stringify(app.field_settings_fields[n].field_options.inner_fields)));t=e.sort((e,t)=>e.innerIndex>t.innerIndex?1:t.innerIndex>e.innerIndex?-1:0);app.$set(app.field_settings_fields[n].field_options,"inner_fields",t)}}static sort_field_inside_list(e){let t=document.getElementsByClassName("bpa-field-container"),l=app.field_settings_fields;if(0<t.length){let i=0;t.forEach((e,t)=>{e.setAttribute("data-id",i),i++}),l.forEach((e,t)=>{let i=document.querySelector('.bpa-field-container[data-field-id="'+e.id+'"]');if(null==i)return!0;var l=i.getAttribute("data-id");e.field_position=parseInt(l)}),app.field_settings_fields.sort((e,t)=>e.field_position>t.field_position?1:t.field_position>e.field_position?-1:0)}}static init_multicolumn(e){e.forEach(e=>{var t="inner_sorting_"+Math.round(1e3*Math.random()),e=new Sortable(e,{group:{name:t,put:function(){var e=arguments[1];let t=arguments[2];return!(null==t||!t.classList.contains("bpa-field-outer-container"))||("bpa-input-fields"==e.el.id||"bpa-customer-fields"==e.el.id)}},direction:"horizontal",animation:150,swapThreshold:.7,ghostClass:"bpa-sortable-placeholder",onAdd:function(i){let l=i.item;if(void 0!==l.classList&&l.classList.contains("bpa-field-outer-container")){var s=l.getAttribute("data-id");let e=app.field_settings_fields[s],t=i.to.parentNode;var o=t.getAttribute("data-id"),r=(e.is_blank=!1,i.to.getAttribute("data-id"));app.field_settings_fields[o].field_options.inner_fields[r]=e,delete app.field_settings_fields[s];let a=app.field_settings_fields,d=[],n=document.getElementsByClassName("bpa-field-container");if(0<n.length){let i=0,l=(n.forEach((e,t)=>{e.setAttribute("data-id",i),i++}),0);a.forEach((e,t)=>{d[l]=e,d[l].field_position=t+1,l++})}app.field_settings_fields=d,app.bpa_sortable_data.forEach((e,t)=>{null!=e.el&&null!=Sortable.get(e.el)&&Sortable.get(e.el).destroy()}),app.bpa_sortable_data=[],app.$forceUpdate(),setTimeout(function(){new BPASortable},500)}else BPASortable.add_to_inner_list(i)}});app.bpa_sortable_data.push(e)})}static add_to_inner_list(e){let a=e.item;var d=a.getAttribute("data-type");let t=e.to;var i=t.getAttribute("data-id"),n=a.getAttribute("data-customer-field-id")||-1;let s=BPASortable.bpa_get_field_data(d,i,n);d=BPASortable.BPAGetParents(a,".bpa-field-col-parent-container"),n=BPASortable.BPAGetParents(a,".bpa-field-col-repeater-parent-container");let o=d[0],r=o.getAttribute("data-id");if(void 0!==n&&0!=n.length){let e=BPASortable.BPAGetParents(a,".bpa-field-col-inner-parent-container")[0];e.getAttribute("data-id");let t=n[0];d=t.getAttribute("data-id");r=o.getAttribute("data-fkey");let i=0,l=BPASortable.BPAGetParents(a,".inner-field-blank-container");null!=l&&void 0!==l[0]&&(i=l[0].getAttribute("data-fkey"),l[0].getAttribute("data-id")),app.field_settings_fields[d].field_options.inner_fields[r].field_options.inner_fields[i].is_blank=!1,s.id=parseInt(app.field_settings_fields[d].field_options.inner_fields[r].field_options.inner_fields[i].id),app.field_settings_fields[d].field_options.inner_fields[r].field_options.inner_fields[i]=s,a.replaceWith("")}else{e.item.getAttribute("data-is-customer-field");app.field_settings_fields[r].field_options.inner_fields[i].is_blank=!1,s.id=parseInt(app.field_settings_fields[r].field_options.inner_fields[i].id),app.field_settings_fields[r].field_options.inner_fields[i]=s,a.replaceWith("")}BPASortable.sort_field_inside_list(),app.bpa_sortable_data.forEach((e,t)=>{}),app.bpa_sortable_data=[],app.$forceUpdate(),setTimeout(function(){new BPASortable},500)}static BPAGetParents(e,t){Element.prototype.matches||(Element.prototype.matches=Element.prototype.matchesSelector||Element.prototype.mozMatchesSelector||Element.prototype.msMatchesSelector||Element.prototype.oMatchesSelector||Element.prototype.webkitMatchesSelector||function(e){for(var t=(this.document||this.ownerDocument).querySelectorAll(e),i=t.length;0<=--i&&t.item(i)!==this;);return-1<i});for(var i=[];e&&e!==document;e=e.parentNode)(!t||e.matches(t))&&i.push(e);return i}static on_swap_fields(e){e=e.item;let t=BPASortable.BPAGetParents(e,".bpa-field-col-parent-container"),i=t[0].getAttribute("data-id");null!=i&&void 0!==app.field_settings_fields[i].field_options.inner_fields&&(0<t[0].querySelectorAll(".bpa-inner-field-container").length&&(t[0].querySelectorAll(".bpa-inner-field-container").forEach((e,t)=>{e=e.getAttribute("data-id");void 0!==app.field_settings_fields[i].field_options.inner_fields[e]&&(app.field_settings_fields[i].field_options.inner_fields[e].innerIndex=t)}),app.field_settings_fields[i].field_options.inner_fields.sort((e,t)=>e.innerIndex>t.innerIndex?1:t.innerIndex>e.innerIndex?-1:0)),app.$forceUpdate(),setTimeout(function(){var e;"2_col"==t[0].getAttribute("data-type")&&2<t[0].querySelectorAll(".bpa-inner-field-container").length&&(e=t[0].querySelector('.bpa-inner-field-container[data-blank-elm="true"]'),t[0].removeChild(e))},10))}static bpa_add_customer_field_to_list(e){let t=e;var e=t.getAttribute("data-customer-field-meta"),i=t.getAttribute("data-customer-field-type");BPASortable.add_item_to_form(i,"customer_field",e)}static add_item_to_form(e,t,i=""){var l=e;if("password"==e){var e=document.querySelectorAll('.bpa-field-container[data-type="Password"]').length,a=document.querySelectorAll('.bpa-inner-field-container[data-type="Password"]').length;if(1<=e||1<=a)return!(app.bpa_password_field_exists="bpa-form-element-col__disabled")}let d=document.querySelectorAll(".bpa-field-container").length,n;if("2col"==t?((n=document.createElement("input")).type="hidden",n.setAttribute("data-value","2col")):"3col"==t?((n=document.createElement("input")).type="hidden",n.setAttribute("data-value","3col")):"4col"==t&&((n=document.createElement("input")).type="hidden",n.setAttribute("data-value","4col")),"customer_field"==t){let e=document.querySelector(`.bpa-cs__item[data-customer-field-meta="${i}"]`);(n=document.createElement("input")).type="hidden",n.setAttribute("data-customer-field-meta",e.getAttribute("data-customer-field-meta")),n.setAttribute("data-customer-field-id",e.getAttribute("data-customer-field-id")),e.classList.forEach(e=>{n.classList.add(e)})}if(void 0!==n&&n.classList.contains("bpa-restricted"))return!1;BPASortable.bpa_add_field_to_list(l,d,!1,n),setTimeout(function(){var e=document.querySelectorAll(".bpa-field-container")[d];window.scrollTo({top:e.offsetTop,left:0,behavior:"smooth"})},100)}static bpa_add_field_to_repeater_list(i,n,e,s,o,r,_){if("column"!=i){var p=s.getAttribute("data-type").toLowerCase(),f=r.getAttribute("data-id");let t=s.getAttribute("data-customer-field-id")||-1,e=BPASortable.BPAGetParents(s,".bpa-field-col-parent-container"),i=!1,l=(0==e.length&&(e=BPASortable.BPAGetParents(s,`.bpa-cfs-ic--body__repeater-field-preview[data-repeater-id="${o}"]`),i=!0),e[0]);var c=l.getAttribute("data-id");let a;var b=_.getAttribute("id")||0;let d;if("bpa-input-fields"==b)a=1==i?BPASortable.bpa_get_field_data(p,n,t):BPASortable.bpa_get_field_data(p,f,t);else if("bpa-draggable-container"==_.getAttribute("id")){d=s.getAttribute("data-id");let e=JSON.parse(JSON.stringify(app.field_settings_fields[d]));app.field_settings_fields[c].field_options.inner_fields;e.field_position=n+.1,e.is_blank=!1,null==app.field_settings_fields[c].field_options.inner_fields[n]?app.$set(app.field_settings_fields[c].field_options.inner_fields,n,e):app.field_settings_fields[c].field_options.inner_fields.splice(n,0,e);let t=JSON.parse(JSON.stringify(app.field_settings_fields));return t=t.filter((e,t)=>{if(d!=t)return e}),app.$set(app,"field_settings_fields",t),void app.field_settings_fields.forEach((e,l)=>{if("Repeater"==e.field_type){let i=l+.1;app.field_settings_fields[l].field_options.inner_fields.forEach((e,t)=>{app.$set(app.field_settings_fields[l].field_options.inner_fields[t],"field_position",i),app.$set(app.field_settings_fields[l].field_options.inner_fields[t],"is_blank",!1),i+=.1})}})}if(1==i){if(0<app.field_settings_fields[c].field_options.inner_fields.length){t=parseInt(t);let e=app.field_settings_fields[c].field_options.inner_fields;e.splice(a.field_position,0,a),e.forEach((e,t)=>{e.field_position=parseInt(t)+1}),app.field_settings_fields[c].field_options.inner_fields=e}else{void 0===app.field_settings_fields[c].field_options.inner_fields[0]&&(app.field_settings_fields[c].field_options.inner_fields[0]={});let e=JSON.parse(JSON.stringify(app.field_settings_fields));e=e.filter((e,t)=>{if(d!=t)return e}),app.$set(app,"field_settings_fields",e),app.field_settings_fields[c].field_options.inner_fields[0]=a,app.field_settings_fields[c].field_options.inner_fields[0].is_blank=!1}s.replaceWith("")}app.bpa_sortable_data.forEach((e,t)=>{}),app.bpa_sortable_data=[],app.$forceUpdate(),setTimeout(function(){new BPASortable},500)}else if("column"==i){b=s.getAttribute("data-value");let t={};p=BPASortable.bpa_generate_meta_key(i),f=s.getAttribute("data-customer-field-id")||-1,c=("2col"==b?(t.error_message="",t.field_name="2 Col",t.label="2 Col",t.field_type="2_col",t.bookingpress_field_placeholder="",t.field_position=n,t.id=BPASortable.bpa_generate_field_id(),t.is_edit=!1,t.is_hide=!1,t.is_default=0,t.is_delete=!1,_=[{is_blank:!(t.placeholder=""),id:BPASortable.bpa_generate_field_id(),field_options:{is_customer_field:!1}},{is_blank:!0,id:BPASortable.bpa_generate_field_id(),field_options:{is_customer_field:!1}}],t.field_options={layout:"2col",inner_class:"2col",is_customer_field:!1,inner_fields:_}):"3col"==b?(t.error_message="",t.field_name="3 Col",t.label="3 Col",t.field_type="3_col",t.bookingpress_field_placeholder="",t.field_position=n,t.id=BPASortable.bpa_generate_field_id(),t.is_edit=!1,t.is_hide=!1,t.is_default=0,t.is_delete=!1,c=[{is_blank:!(t.placeholder=""),id:BPASortable.bpa_generate_field_id(),field_options:{is_customer_field:!1}},{is_blank:!0,id:BPASortable.bpa_generate_field_id(),field_options:{is_customer_field:!1}},{is_blank:!0,id:BPASortable.bpa_generate_field_id(),field_options:{is_customer_field:!1}}],t.field_options={layout:"3col",inner_class:"3col",is_customer_field:!1,inner_fields:c}):"4col"==b&&(t.error_message="",t.field_position=n,t.field_name="4 Col",t.label="4 Col",t.field_type="4_col",t.bookingpress_field_placeholder="",t.id=BPASortable.bpa_generate_field_id(),t.is_edit=!1,t.is_hide=!1,t.is_default=0,t.is_delete=!1,_=[{is_blank:!(t.placeholder=""),id:BPASortable.bpa_generate_field_id(),field_options:{is_customer_field:!1}},{is_blank:!0,id:BPASortable.bpa_generate_field_id(),field_options:{is_customer_field:!1}},{is_blank:!0,id:BPASortable.bpa_generate_field_id(),field_options:{is_customer_field:!1}},{is_blank:!0,id:BPASortable.bpa_generate_field_id(),field_options:{is_customer_field:!1}}],t.field_options={layout:"4col",inner_class:"4col",is_customer_field:!1,inner_fields:_}),t.meta_key=p,t.field_options.minimum="",t.field_options.maximum="",t.field_options.enable_timepicker=!1,r.getAttribute("data-id"));let e=BPASortable.BPAGetParents(s,`.bpa-cfs-ic--body__repeater-field-preview[data-repeater-id="${o}"]`)[0];b=e.getAttribute("data-id");if(0<app.field_settings_fields[b].field_options.inner_fields.length){"column"!=i&&(f=parseInt(f),t=BPASortable.bpa_get_field_data(i,n,f));let e=app.field_settings_fields[b].field_options.inner_fields;e.splice(t.field_position,0,t),e.forEach((e,t)=>{e.field_position=parseInt(t)+1}),app.field_settings_fields[b].field_options.inner_fields=e}else void 0===app.field_settings_fields[b].field_options.inner_fields[c]&&(app.field_settings_fields[b].field_options.inner_fields[c]={}),app.field_settings_fields[b].field_options.inner_fields[c].is_blank=!1,t.id=BPASortable.bpa_generate_field_id(),app.field_settings_fields[b].field_options.inner_fields[c]=t;s.replaceWith(""),app.$forceUpdate(),app.bpa_sortable_data=[],app.$forceUpdate(),setTimeout(function(){new BPASortable},500)}}static bpa_add_field_to_list(i,l,e,a){if("column"==i){var d=a.getAttribute("data-value");let e={};var n=BPASortable.bpa_generate_meta_key(i);"2col"==d?(e.error_message="",e.field_name="2 Col",e.label="2 Col",e.field_type="2_col",e.bookingpress_field_placeholder="",e.field_position=l,e.id=BPASortable.bpa_generate_field_id(),e.is_edit=!1,e.is_hide=!1,e.is_default=0,e.is_delete=!1,s=[{is_blank:!(e.placeholder=""),id:BPASortable.bpa_generate_field_id(),field_options:{is_customer_field:!1}},{is_blank:!0,id:BPASortable.bpa_generate_field_id(),field_options:{is_customer_field:!1}}],e.field_options={layout:"2col",inner_class:"2col",is_customer_field:!1,inner_fields:s}):"3col"==d?(e.error_message="",e.field_name="3 Col",e.label="3 Col",e.field_type="3_col",e.bookingpress_field_placeholder="",e.field_position=l,e.id=BPASortable.bpa_generate_field_id(),e.is_edit=!1,e.is_hide=!1,e.is_default=0,e.is_delete=!1,s=[{is_blank:!(e.placeholder=""),id:BPASortable.bpa_generate_field_id(),field_options:{is_customer_field:!1}},{is_blank:!0,id:BPASortable.bpa_generate_field_id(),field_options:{is_customer_field:!1}},{is_blank:!0,id:BPASortable.bpa_generate_field_id(),field_options:{is_customer_field:!1}}],e.field_options={layout:"3col",inner_class:"3col",is_customer_field:!1,inner_fields:s}):"4col"==d&&(e.error_message="",e.field_position=l,e.field_name="4 Col",e.label="4 Col",e.field_type="4_col",e.bookingpress_field_placeholder="",e.id=BPASortable.bpa_generate_field_id(),e.is_edit=!1,e.is_hide=!1,e.is_default=0,e.is_delete=!1,s=[{is_blank:!(e.placeholder=""),id:BPASortable.bpa_generate_field_id(),field_options:{is_customer_field:!1}},{is_blank:!0,id:BPASortable.bpa_generate_field_id(),field_options:{is_customer_field:!1}},{is_blank:!0,id:BPASortable.bpa_generate_field_id(),field_options:{is_customer_field:!1}},{is_blank:!0,id:BPASortable.bpa_generate_field_id(),field_options:{is_customer_field:!1}}],e.field_options={layout:"4col",inner_class:"4col",is_customer_field:!1,inner_fields:s}),e.meta_key=n,e.field_options.minimum="",e.field_options.maximum="",e.field_options.enable_timepicker=!1;let t=app.field_settings_fields;t.splice(e.field_position,0,e),t.forEach((e,t)=>{e.field_position=parseInt(t)+1}),app.field_settings_fields=t,void 0!==a&&a.replaceWith(""),app.field_settings_fields.sort((e,t)=>e.field_position>t.field_position?1:t.field_position>e.field_position?-1:0),app.$forceUpdate(),setTimeout(function(){BPASortable.init_multicolumn(document.querySelectorAll('.bpa-field-col-parent-container[data-id="'+l+'"] .bpa-inner-field-container.inner-field-blank-container'))},100)}else if("repeater"==i){d=BPASortable.bpa_generate_meta_key(i);let e={label:wp.i18n.__("Guest","bookingpress-appointment-booking"),field_type:"Repeater",field_position:l,id:BPASortable.bpa_generate_field_id(),is_edit:!1,is_hide:!1,is_default:0,is_required:!1,is_delete:!1,meta_key:d,field_options:{layout:"1col",inner_class:"1col",inner_fields:[]},error_message:"",placeholder:"",field_name:"Repeater",bookingpress_field_placeholder:""},t=app.field_settings_fields;t.splice(e.field_position,0,e),t.forEach((e,t)=>{e.field_position=parseInt(t)+1}),app.field_settings_fields=t,void 0!==a&&a.replaceWith(""),app.field_settings_fields.sort((e,t)=>e.field_position>t.field_position?1:t.field_position>e.field_position?-1:0),app.$forceUpdate(),app.bpa_sortable_data=[],app.$forceUpdate(),setTimeout(function(){new BPASortable},500)}else{let e=-1;void 0!==a&&(e=a.getAttribute("data-customer-field-id")||-1),e=parseInt(e);var s=BPASortable.bpa_get_field_data(i,l,e);let t=app.field_settings_fields;t.splice(s.field_position,0,s),t.forEach((e,t)=>{e.field_position=parseInt(t)+1}),app.field_settings_fields=t,void 0!==a&&a.replaceWith(""),app.field_settings_fields.sort((e,t)=>e.field_position>t.field_position?1:t.field_position>e.field_position?-1:0),setTimeout(function(){var e,t;"password"==i&&(e=document.querySelectorAll('.bpa-field-container[data-type="Password"]').length,t=document.querySelectorAll('.bpa-inner-field-container[data-type="Password"]').length,(1<=e||1<=t)&&null!=document.querySelector('#bpa-input-fields .bpa-cs__item[data-type="password"]')&&document.querySelector('#bpa-input-fields .bpa-cs__item[data-type="password"]').classList.add("bpa-form-element-col__disabled"))},500)}}static bpa_generate_meta_key(e,t){var i=e.toLowerCase(),l="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";let a=5,d=(5<=(t=void 0===t?1:t)&&(a=7),[]);var n=l.length-1;for(let e=0;e<a;e++){var s=BPASortable.BPAgetRndInteger(0,n);d.push(l[s])}i=i+"_"+d.join("");return null!==document.querySelector('.bpa-field-wrapper-container[data-metakey="'+i+'"]')?(++t,BPASortable.bpa_generate_meta_key(e,t)):i}static BPAgetRndInteger(e,t){return Math.floor(Math.random()*(t-e+1))+e}static bpa_generate_field_id(e){let t=1e3,i=(5<=(e=void 0===e?1:e)&&(t=1e4),Math.round(Math.random()*t));return null!==document.querySelector('.bpa-field-wrapper-container[data-field-id="'+i+'"]')&&(++e,i=BPASortable.bpa_generate_field_id(e)),i}}