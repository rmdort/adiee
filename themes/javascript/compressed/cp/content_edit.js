/*
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */

$(document).ready(function(){function y(b){if(l[b]===undefined)b=0;jQuery.each(l[b],function(a,d){switch(a){case "categories":$("select#f_cat_id").empty().append(d);break;case "statuses":$("select#f_status").empty().append(d);break}})}function p(){if($("#custom_date_start").val()!="yyyy-mm-dd"&&$("#custom_date_end").val()!="yyyy-mm-dd"){focus_number=$("#date_range").children().length;$("#date_range").append('<option id="custom_date_option">'+$("#custom_date_start").val()+" to "+$("#custom_date_end").val()+
"</option>");document.getElementById("date_range").options[focus_number].selected=true;$("#custom_date_picker").slideUp("fast");oTable.fnDraw()}}function s(b,a,d){for(var e=0,h=b.length;e<h;e++)if(b[e].name==a)b[e].value=d}function m(b,a){for(var d=0,e=b.length;d<e;d++)if(b[d].name==a)return b[d].value;return null}$(".paginationLinks .first").hide();$(".paginationLinks .previous").hide();$(".toggle_all").toggle(function(){$("input.toggle").each(function(){this.checked=true})},function(){$("input.toggle").each(function(){this.checked=
false})});$("#custom_date_start_span").datepicker({dateFormat:"yy-mm-dd",prevText:"<<",nextText:">>",onSelect:function(b){$("#custom_date_start").val(b);p()}});$("#custom_date_end_span").datepicker({dateFormat:"yy-mm-dd",prevText:"<<",nextText:">>",onSelect:function(b){$("#custom_date_end").val(b);p()}});$("#custom_date_start, #custom_date_end").focus(function(){$(this).val()=="yyyy-mm-dd"&&$(this).val("")});$("#custom_date_start, #custom_date_end").keypress(function(){$(this).val().length>=9&&p()});
var l=EE.edit.channelInfo,z=RegExp("!-!","g"),A=(new Date).getTime();(function(){jQuery.each(l,function(b,a){jQuery.each(a,function(d,e){var h=new String;jQuery.each(e,function(i,n){h+='<option value="'+n[0]+'">'+n[1].replace(z,String.fromCharCode(160))+"</option>"});l[b][d]=h})})})();$("#f_channel_id").change(function(){y(this.value)});$("#date_range").change(function(){if($("#date_range").val()=="custom_date"){$("#custom_date_start").val("yyyy-mm-dd");$("#custom_date_end").val("yyyy-mm-dd");$("#custom_date_option").remove();
$("#custom_date_picker").slideDown("fast")}else $("#custom_date_picker").hide()});$("#entries_form").submit(function(){if(!$("input:checkbox",this).is(":checked")){$.ee_notice(EE.lang.selection_required,{type:"error"});return false}});var c={iCacheLower:-1};if(EE.edit.tableColumns==9){MyCols=[null,null,{bSortable:false},null,null,null,null,null,{bSortable:false}];MySortCol=5}else{MyCols=[null,null,{bSortable:false},null,null,null,null,{bSortable:false}];MySortCol=4}oTable=$("#entries_form .mainTable").dataTable({sPaginationType:"full_numbers",
bLengthChange:false,aaSorting:[[MySortCol,"desc"]],bFilter:false,sWrapper:false,sInfo:false,bAutoWidth:false,iDisplayLength:+EE.edit.perPage,aoColumns:MyCols,oLanguage:{sZeroRecords:EE.lang.noEntries,oPaginate:{sFirst:'<img src="'+EE.edit.themeUrl+'images/pagination_first_button.gif" width="13" height="13" alt="&lt; &lt;" />',sPrevious:'<img src="'+EE.edit.themeUrl+'images/pagination_prev_button.gif" width="13" height="13" alt="&lt; &lt;" />',sNext:'<img src="'+EE.edit.themeUrl+'images/pagination_next_button.gif" width="13" height="13" alt="&lt; &lt;" />',
sLast:'<img src="'+EE.edit.themeUrl+'images/pagination_last_button.gif" width="13" height="13" alt="&lt; &lt;" />'}},bProcessing:true,bServerSide:true,sAjaxSource:EE.BASE+"&C=content_edit&M=edit_ajax_filter&time="+A,fnServerData:function(b,a,d){function e(){if($(t).data("user_data")=="n")return"";return t.value}var h=+EE.edit.pipe,i=false,n=m(a,"sEcho"),f=m(a,"iDisplayStart"),j=m(a,"iDisplayLength"),g=f+j,t=document.getElementById("keywords"),u=document.getElementById("f_status"),q=document.getElementById("f_channel_id"),
v=document.getElementById("f_cat_id"),r=document.getElementById("f_search_in"),w=document.getElementById("date_range"),x=document.getElementById("f_perpage"),o;o="&ajax=true&keywords="+e()+"&channel_id="+q.value;if(r.value=="comments")window.location=EE.BASE+"&C=content_edit&M=view_comments"+o;a.push({name:"keywords",value:e()},{name:"status",value:u.value},{name:"channel_id",value:q.value},{name:"cat_id",value:v.value},{name:"search_in",value:r.value},{name:"date_range",value:w.value},{name:"per_page",
value:x.value});c.iDisplayStart=f;if(c.iCacheLower<0||f<c.iCacheLower||g>c.iCacheUpper)i=true;if(c.lastRequest&&!i){g=0;for(o=a.length;g<o;g++)if(a[g].name!="iDisplayStart"&&a[g].name!="iDisplayLength"&&a[g].name!="sEcho")if(a[g].value!=c.lastRequest[g].value){i=true;break}}c.lastRequest=a.slice();if(i){if(f<c.iCacheLower){f-=j*(h-1);if(f<0)f=0}c.iCacheLower=f;c.iCacheUpper=f+j*h;c.iDisplayLength=m(a,"iDisplayLength");s(a,"iDisplayStart",f);s(a,"iDisplayLength",j*h);a.push({name:"keywords",value:e()},
{name:"status",value:u.value},{name:"channel_id",value:q.value},{name:"cat_id",value:v.value},{name:"search_in",value:r.value},{name:"date_range",value:w.value},{name:"per_page",value:x.value});$.getJSON(b,a,function(k){c.lastJson=jQuery.extend(true,{},k);c.iCacheLower!=c.iDisplayStart&&k.aaData.splice(0,c.iDisplayStart-c.iCacheLower);k.aaData.splice(c.iDisplayLength,k.aaData.length);d(k)})}else{json=jQuery.extend(true,{},c.lastJson);json.sEcho=n;json.aaData.splice(0,f-c.iCacheLower);json.aaData.splice(j,
json.aaData.length);d(json)}}});$("#keywords").keyup(function(){oTable.fnDraw()});$("select#f_channel_id").change(function(){oTable.fnDraw()});$("select#f_cat_id").change(function(){oTable.fnDraw()});$("select#f_status").change(function(){oTable.fnDraw()});$("select#f_search_in").change(function(){oTable.fnDraw()});$("select#date_range").change(function(){oTable.fnDraw()});$("select#f_perpage").change(function(){oTable.fnDraw()})});