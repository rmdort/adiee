/*!
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

(function(a){function l(b,c){if(isNaN(c))c=0;if(isNaN(b))n[b.id]=b;else b=n[b];if(!(!b in j)){j[b.id]=b.files;o[b.id]=b.url;var d=p,k=b.files.length;a.each(b.files,function(u,g){g.img_id=u+"";g.directory=b.id+"";g.is_image=!(g.mime.indexOf("image")<0);if(g.is_image)g.thumb=b.url+"/_thumbs/thumb_"+g.name});var e=a("#tableView").detach(),h=a("#viewSelectors").detach();e.find("tbody").empty();a("#file_chooser_body").empty().append(e);a("#file_chooser_footer").empty().append(h);e=Math.ceil(k/d);h=[];
for(var f=0;f<e;f++)h[f]=f+1;c*=d;k={directory:b.id,pages_from:c,pages_to:c+d,pages_total:k,pages_current:Math.floor(c/d)+1,pages:h};e=b.files.slice(c,c+d);if(m!="list"){h=[];f=e.length;for(var q=0;q<d&&f;){f--;if(e[f].has_thumb){h.push(e[f]);q++}}a("#tableView").hide();a.tmpl("thumb",h).appendTo("#file_chooser_body")}else{a("#tableView").show();a.tmpl("fileRow",e).appendTo("#tableView tbody")}a.tmpl("pagination",k).appendTo("#file_chooser_footer")}}function r(b){j[b]==""&&a.ee_filebrowser.endpoint_request("directory_contents",
{directory:b},l)}function v(){i.dialog({width:730,height:495,resizable:false,position:["center","center"],modal:true,draggable:true,title:EE.filebrowser.window_title,autoOpen:false,zIndex:99999,open:function(){var b=a("#dir_choice").val();r(b)}});m="list";a("input[name=view_type]").click(function(){m=this.value;l(a("#dir_choice").val(),0)});a("#dir_choice").change(function(){r(this.value);l(this.value,0)});a.template("fileRow",a("<tbody />").append(a("#rowTmpl").remove().attr("id","")));a.template("noFilesRow",
a("#noFilesRowTmpl").remove());a.template("pagination",a("#paginationTmpl").remove());a.template("thumb",a("#thumbTmpl").remove());a("#upload_form",i).submit(a.ee_filebrowser.upload_start)}var s=0,j,o,i,p,t,n={},m;a.ee_filebrowser=function(){p=10;a.ee_filebrowser.endpoint_request("setup",function(b){j={};o={};i=a(b.manager).appendTo(document.body);for(var c in b.directories){s||(s=c);j[c]=""}v()})};a.ee_filebrowser.endpoint_request=function(b,c,d){if(!d&&a.isFunction(c)){d=c;c={}}c=a.extend(c,{action:b});
a.getJSON(EE.BASE+"&"+EE.filebrowser.endpoint_url+"&"+a.param(c),d)};a.ee_filebrowser.add_trigger=function(b,c,d){if(!d&&a.isFunction(c)){d=c;c="userfile"}a(b).click(function(){var k=this;a("#upload_file",i).attr("name",c);i.dialog("open");t=function(e){d.call(k,e,c)};return false})};a.ee_filebrowser.change_dim=function(b,c){if(a("#cloned #constrain:checked").length!=0)if(c.attr("id")=="resize_width"){var d=b.height/b.width;a("#resize_height").val(Math.floor(d*c.val()))}else{d=b.width/b.height;a("#resize_width").val(Math.floor(d*
c.val()))}};a.ee_filebrowser.submit_image_edit=function(b,c){a.ajax({type:"POST",url:EE.BASE+"&"+EE.filebrowser.endpoint_url+"&action=edit_image",data:a("#image_edit_form").serialize(),success:function(d){b.name=d;b.dimensions='width="'+b.width+'" height="'+b.height+'" ';a.ee_filebrowser.clean_up(b,c)},error:function(d){if(a.ee_notice)a.ee_notice(d.responseText,{type:"error"});else{d.responseText=d.responseText.replace(/<p>/,"");alert(d.responseText.replace(/<\/p>/,""))}}})};a.ee_filebrowser.placeImage=
function(b,c){a.ee_filebrowser.clean_up(j[b][c],"");return false};a.ee_filebrowser.clean_up=function(b,c){a("#page_0 .items").html(c);i.dialog("close");t(b)};a.ee_filebrowser=a.extend(a.ee_filebrowser,{upload_start:function(){a("input[name=upload_dir]").val(a("#dir_choice").val())},upload_success:function(b){a.ee_filebrowser.clean_up(b,"")},upload_error:function(b){a("#progress",i).hide();if(a.ee_notice)a.ee_notice(b.error,{type:"error"});else{b.error=b.error.replace(/<p>/,"");alert(b.error.replace(/<\/p>/,
""))}console.log(b)}});a.ee_filebrowser.setPage=l})(jQuery);
