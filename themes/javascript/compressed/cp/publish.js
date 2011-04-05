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

EE.publish=EE.publish||{};
EE.publish.category_editor=function(){var b=[],d=$("<div />"),g=$('<div id="cat_modal_container" />').appendTo(d),i={},p={},q=EE.BASE+"&C=admin_content&M=category_editor&group_id=",r,k,j;d.dialog({autoOpen:false,height:450,width:600,modal:true});$(".edit_categories_link").each(function(){var e=this.href.substr(this.href.lastIndexOf("=")+1);$(this).data("gid",e);b.push(e)});for(j=0;j<b.length;j++){i[b[j]]=$("#cat_group_container_"+[b[j]]);i[b[j]].data("gid",b[j]);p[b[j]]=$("#cat_group_container_"+
[b[j]]).find(".cat_action_buttons").remove()}r=function(e){i[e].text("loading...").load(q+e+"&timestamp="+ +new Date+" .pageContents table",function(){k.call(i[e],i[e].html(),false)})};k=function(e,n){var f=$(this),a=f.data("gid");e=$.trim(e);if(f.hasClass("edit_categories_link"))f=$("#cat_group_container_"+a);if(e[0]!=="<"&&n)return r(a);f.closest(".cat_group_container").find("#refresh_categories").show();var c=$(e),h;if(c.find("form").length){g.html(c);c=g.find("input[type=submit]");h=g.find("form");
$category_name=h.find("#cat_name");$category_url_title=h.find("#cat_url_title");$category_name.keyup(function(){EE.cp.live_url_title($category_name,$category_url_title)});var l=function(o){var t=o||$(this);o=t.serialize();t=t.attr("action");$.ajax({url:t,type:"POST",data:o,dataType:"html",beforeSend:function(){f.html(EE.lang.loading)},success:function(m){m=$.trim(m);d.dialog("close");if(m[0]=="<"){m=$(m).find(".pageContents table");m.find("form").length==0&&f.html(m);k.call(f,m,true)}else k.call(f,
m,true)},error:function(m){d.dialog("close");k.call(f,m.error,true)}});return false};h.submit(l);var s={};s[c.remove().attr("value")]=function(){l(h)};d.dialog("open");d.dialog("option","buttons",s);d.one("dialogclose",function(){r(a)})}else p[a].clone().appendTo(f).show();return false};j=function(){$(this).hide();var e=$(this).data("gid"),n=".pageContents";if($(this).hasClass("edit_cat_order_trigger")||$(this).hasClass("edit_categories_link"))n+=" table";e||(e=$(this).closest(".cat_group_container").data("gid"));
i[e].text(EE.lang.loading);$.ajax({url:this.href+"&timestamp="+ +new Date+n,success:function(f){var a="";f=$.trim(f);if(f[0]=="<"){f=$(f).find(n);a=$("<div />").append(f).html();f.find("form").length==0&&i[e].html(a)}k.call(i[e],a,true)},error:function(f){f=eval("("+f.responseText+")");i[e].html(f.error);k.call(i[e],f.error,true)}});return false};$(".edit_categories_link").click(j);$(".cat_group_container a:not(.cats_done)").live("click",j);$(".cats_done").live("click",function(){var e=$(this).closest(".cat_group_container"),
n=e.data("gid");$(".edit_categories_link").each(function(){$(this).data("gid")==n&&$(this).show()});e.text("loading...").load(EE.BASE+"&C=content_publish&M=category_actions&group_id="+e.data("gid")+"&timestamp="+ +new Date,function(f){e.html($(f).html())});return false})};var selected_tab="";function get_selected_tab(){return selected_tab}
function tab_focus(b){$(".menu_"+b).parent().is(":visible")||$("a.delete_tab[href=#"+b+"]").trigger("click");$(".tab_menu li").removeClass("current");$(".menu_"+b).parent().addClass("current");$(".main_tab").hide();$("#"+b).show();$(".main_tab").css("z-index","");$("#"+b).css("z-index","5");selected_tab=b;$(".main_tab").sortable("refreshPositions")}EE.tab_focus=tab_focus;
function setup_tabs(){var b="";$(".main_tab").sortable({connectWith:".main_tab",appendTo:"#holder",helper:"clone",forceHelperSize:true,handle:".handle",start:function(d,g){g.item.css("width",$(this).parent().css("width"))},stop:function(d,g){g.item.css("width","100%")}});$(".tab_menu li a").droppable({accept:".field_selector, .publish_field",tolerance:"pointer",forceHelperSize:true,deactivate:function(){clearTimeout(b);$(".tab_menu li").removeClass("highlight_tab")},drop:function(d,g){field_id=g.draggable.attr("id").substring(11);
tab_id=$(this).attr("title").substring(5);setTimeout(function(){$("#hold_field_"+field_id).prependTo("#"+tab_id);$("#hold_field_"+field_id).hide().slideDown()},0);tab_focus(tab_id);return false},over:function(){tab_id=$(this).attr("title").substring(5);$(this).parent().addClass("highlight_tab");b=setTimeout(function(){tab_focus(tab_id);return false},500)},out:function(){b!=""&&clearTimeout(b);$(this).parent().removeClass("highlight_tab")}});$("#holder .main_tab").droppable({accept:".field_selector",
tolerance:"pointer",drop:function(d,g){field_id=g.draggable.attr("id")=="hide_title"||g.draggable.attr("id")=="hide_url_title"?g.draggable.attr("id").substring(5):g.draggable.attr("id").substring(11);tab_id=$(this).attr("id");$("#hold_field_"+field_id).prependTo("#"+tab_id);$("#hold_field_"+field_id).hide().slideDown()}});$(".tab_menu li.content_tab a, #publish_tab_list a.menu_focus").unbind(".publish_tabs").bind("mousedown.publish_tabs",function(d){tab_id=$(this).attr("title").substring(5);tab_focus(tab_id);
d.preventDefault()}).bind("click.publish_tabs",function(){return false})}setup_tabs();Number.prototype.is_integer=String.prototype.is_integer=function(){var b=parseInt(this,10);if(isNaN(b))return false;return this==b&&this.toString()==b.toString()};EE.publish.get_percentage_width=function(b){if(b.attr("data-width")&&b.attr("data-width").slice(0,-1).is_integer())return parseInt(b.attr("data-width"),10);return Math.round(b.width()/b.parent().width()*10)*10};
EE.publish.save_layout=function(){var b=0,d={},g={},i=0,p=false,q=$("#tab_menu_tabs li.current").attr("id");$(".main_tab").show();$("#tab_menu_tabs a:not(.add_tab_link)").each(function(){if($(this).parent("li").attr("id")&&$(this).parent("li").attr("id").substring(0,5)=="menu_"){var e=$(this).parent("li").attr("id").substring(5),n=$(this).parent("li").attr("id").substring(5),f=$(this).parent("li").attr("title");i=0;visible=true;if($(this).parent("li").is(":visible")){lay_name=e;d[lay_name]={};d[lay_name]._tab_label=
f}else{p=true;visible=false}$("#"+n).find(".publish_field").each(function(){var a=$(this),c=this.id.replace(/hold_field_/,"");a=EE.publish.get_percentage_width(a);var h=$("#sub_hold_field_"+c+" .markItUp ul li:eq(2)");if(a>100)a=100;h=h.html()!=="undefined"&&h.css("display")!=="none"?true:false;a={visible:$(this).css("display")==="none"||visible===false?false:true,collapse:$("#sub_hold_field_"+c).css("display")==="none"?true:false,htmlbuttons:h,width:a+"%"};if(visible===true){a.index=i;d[lay_name][c]=
a;i+=1}else g[c]=a});visible===true&&b++}});if(p==true){var r,k,j=0;for(darn in d){k=darn;for(r in d[k])if(d[k][r].index>j)j=d[k][r].index;break}$.each(g,function(){this.index=++j});jQuery.extend(d[k],g)}EE.tab_focus(q.replace(/menu_/,""));if(b===0)$.ee_notice(EE.publish.lang.tab_count_zero,{type:"error"});else $("#layout_groups_holder input:checked").length===0?$.ee_notice(EE.publish.lang.no_member_groups,{type:"error"}):$.ajax({type:"POST",dataType:"json",url:EE.BASE+"&C=content_publish&M=save_layout",
data:"XID="+EE.XID+"&json_tab_layout="+JSON.stringify(d)+"&"+$("#layout_groups_holder input").serialize()+"&channel_id="+EE.publish.channel_id,success:function(e){if(e.messageType==="success")$.ee_notice(e.message,{type:"success"});else e.messageType==="failure"&&$.ee_notice(e.message,{type:"error"})}})};
EE.publish.remove_layout=function(){if($("#layout_groups_holder input:checked").length===0)return $.ee_notice(EE.publish.lang.no_member_groups,{type:"error"});$.ajax({type:"POST",url:EE.BASE+"&C=content_publish&M=save_layout",data:"XID="+EE.XID+"&json_tab_layout={}&"+$("#layout_groups_holder input").serialize()+"&channel_id="+EE.publish.channel_id+"&field_group="+EE.publish.field_group,success:function(){$.ee_notice(EE.publish.lang.layout_removed+' <a href="javascript:location=location">'+EE.publish.lang.refresh_layout+
"</a>",{duration:0,type:"success"});return true}});return false};EE.publish.change_preview_link=function(){$select=$("#layout_preview select");$link=$("#layout_group_preview");base=$link.attr("href").split("layout_preview")[0];$link.attr("href",base+"layout_preview="+$select.val());$.ajax({url:EE.BASE+"&C=content_publish&M=preview_layout",type:"POST",dataType:"json",data:{XID:EE.XID,member_group:$select.find("option:selected").text()}})};
EE.date_obj_time=function(){var b=new Date,d=b.getHours();b=b.getMinutes();var g="";if(b<10)b="0"+b;if(EE.date.format=="us"){g=d<12?" AM":" PM";if(d!=0)d=(d+11)%12+1}return" '"+d+":"+b+g+"'"}();file_manager_context="";
function disable_fields(b){var d=$(".main_tab input, .main_tab textarea, .main_tab select, #submit_button"),g=$("#submit_button"),i=$("#holder").find("a");if(b){disabled_fields=d.filter(":disabled");d.attr("disabled",true);g.addClass("disabled_field");i.addClass("admin_mode");$("#holder div.markItUp, #holder p.spellcheck").each(function(){$(this).before('<div class="cover" style="position:absolute;width:100%;height:50px;z-index:9999;"></div>').css({})})}else{d.removeAttr("disabled");g.removeClass("disabled_field");
i.removeClass("admin_mode");$(".cover").remove();disabled_fields.attr("disabled",true)}}
$(document).ready(function(){function b(a){if(a){a=a.toString();a=a.replace(/\(\!\(([\s\S]*?)\)\!\)/g,function(c,h){var l=h.split("|!|");return altKey===true?l[1]!==undefined?l[1]:l[0]:l[1]===undefined?"":l[0]});return a=a.replace(/\[\!\[([\s\S]*?)\]\!\]/g,function(c,h){var l=h.split(":!:");if(n===true)return false;value=prompt(l[0],l[1]?l[1]:"");if(value===null)n=true;return value})}return""}function d(a,c){var h=$("input[name="+c+"]").closest(".publish_field");a.is_image==false?h.find(".file_set").show().find(".filename").html('<img src="'+
EE.PATH_CP_GBL_IMG+'default.png" alt="'+EE.PATH_CP_GBL_IMG+'default.png" /><br />'+a.name):h.find(".file_set").show().find(".filename").html('<img src="'+a.thumb+'" alt="'+a.name+'" /><br />'+a.name);$("input[name="+c+"_hidden]").val(a.name);$("select[name="+c+"_directory]").val(a.directory)}var g,i;$("#layout_group_submit").click(function(){EE.publish.save_layout();return false});$("#layout_group_remove").click(function(){EE.publish.remove_layout();return false});$("#layout_preview select").change(function(){EE.publish.change_preview_link()});
$("a.reveal_formatting_buttons").click(function(){$(this).parent().parent().children(".close_container").slideDown();$(this).hide();return false});$("#write_mode_header .reveal_formatting_buttons").hide();if(EE.publish.smileys==true){$("a.glossary_link").click(function(){$(this).parent().siblings(".glossary_content").slideToggle("fast");$(this).parent().siblings(".smileyContent .spellcheck_content").hide();return false});$("a.smiley_link").toggle(function(){$(this).parent().siblings(".smileyContent").slideDown("fast",
function(){$(this).css("display","")})},function(){$(this).parent().siblings(".smileyContent").slideUp("fast")});$(this).parent().siblings(".glossary_content, .spellcheck_content").hide();$(".glossary_content a").click(function(){var a=$(this).closest(".publish_field"),c=a.attr("id").replace("hold_field_","field_id_");a.find("#"+c).insertAtCursor($(this).attr("title"));return false})}if(EE.publish.autosave){var p=false;i=function(){if(!p){p=true;setTimeout(g,1E3*EE.publish.autosave.interval)}};g=
function(){var a;if($("#tools:visible").length===1)i();else{a=$("#publishForm").serialize();$.ajax({type:"POST",dataType:"json",url:EE.BASE+"&C=content_publish&M=autosave",data:a,success:function(c){if(c.error)console.log(c.error);else if(c.success){c.autosave_entry_id&&$("input[name=autosave_entry_id]").val(c.autosave_entry_id);$("#autosave_notice").text(c.success)}else console.log("Autosave Failed");p=false}})}};var q=$("textarea, input").not(":password,:checkbox,:radio,:submit,:button,:hidden"),
r=$("select, :checkbox, :radio, :file");q.bind("keypress change",i);r.bind("change",i)}if(EE.publish.pages){q=$("#pages__pages_uri");var k=EE.publish.pages.pagesUri;q.val()||q.val(k);q.focus(function(){this.value===k&&$(this).val("")}).blur(function(){this.value===""&&$(this).val(k)})}$.ee_filebrowser();var j="";EE.publish.show_write_mode===true&&$("#write_mode_textarea").markItUp(myWritemodeSettings);EE.publish.markitup.fields!==undefined&&$.each(EE.publish.markitup.fields,function(a){$("#"+a).markItUp(mySettings)});
write_mode_height=$(window).height()-117;$("#write_mode_writer").css("height",write_mode_height+"px");$("#write_mode_writer textarea").css("height",write_mode_height-67-17+"px");var e=$(".write_mode_trigger").overlay({mask:{color:"#262626",loadSpeed:200,opacity:0.85},onBeforeLoad:function(){var a=this.getTrigger()[0],c=$("#write_mode_textarea");j=a.id.match(/^id_\d+$/)?"field_"+a.id:a.id.replace(/id_/,"");c.val($("#"+j).val());c.focus()},onClose:function(){var a="#"+j.replace(/field_/,"");a=$(".write_mode_trigger").index(a);
$("#"+j).val($("#write_mode_textarea").val());e.eq(a).overlay().close()},top:"center",closeOnClick:false}),n=false;$.ee_filebrowser.add_trigger(".btn_img a, .file_manipulate",function(a){var c,h="",l="",s="",o="";textareaId=$(this).closest("#markItUpWrite_mode_textarea").length?"write_mode_textarea":$(this).closest(".publish_field").attr("id").replace("hold_field_","field_id_");if(textareaId!=undefined){c=$("#"+textareaId);c.focus()}if(a.is_image){l=EE.upload_directories[a.directory].properties;s=
EE.upload_directories[a.directory].pre_format;o=EE.upload_directories[a.directory].post_format;h=EE.filebrowser.image_tag.replace(/src="(.*)\[!\[Link:!:http:\/\/\]!\](.*)"/,'src="$1{filedir_'+a.directory+"}"+a.name+'$2"');h=h.replace(/\/?>$/,a.dimensions+" "+l+" />");h=s+h+o}else{l=EE.upload_directories[a.directory].file_properties;s=EE.upload_directories[a.directory].file_pre_format;s+='<a href="{filedir_'+a.directory+"}"+a.name+'" '+l+" >";o="</a>";o+=EE.upload_directories[a.directory].file_post_format}if(c.is("textarea")){if(!c.is(".markItUpEditor")){c.markItUp(myNobuttonSettings);
c.closest(".markItUpContainer").find(".markItUpHeader").hide();c.focus()}a.is_image?$.markItUp({replaceWith:h}):$.markItUp({key:"L",name:"Link",openWith:s,closeWith:o,placeHolder:a.name})}else c.val(function(t,m){m+=s+h+o;return b(m)})});$("input[type=file]","#publishForm").each(function(){var a=$(this).closest(".publish_field"),c=a.find(".choose_file");$.ee_filebrowser.add_trigger(c,$(this).attr("name"),d);a.find(".remove_file").click(function(){a.find("input[type=hidden]").val("");a.find(".file_set").hide();
return false})});$(".hide_field span").click(function(){var a=$(this).parent().parent().attr("id").substr(11),c=$("#hold_field_"+a);a=$("#sub_hold_field_"+a);if(a.css("display")=="block"){a.slideUp();c.find(".ui-resizable-handle").hide();c.find(".field_collapse").attr("src",EE.THEME_URL+"images/field_collapse.png")}else{a.slideDown();c.find(".ui-resizable-handle").show();c.find(".field_collapse").attr("src",EE.THEME_URL+"images/field_expand.png")}return false});$(".close_upload_bar").toggle(function(){$(this).parent().children(":not(.close_upload_bar)").hide();
$(this).children("img").attr("src",EE.THEME_URL+"publish_plus.png")},function(){$(this).parent().children().show();$(this).children("img").attr("src",EE.THEME_URL+"publish_minus.gif")});$(".ping_toggle_all").toggle(function(){$("input.ping_toggle").each(function(){this.checked=false})},function(){$("input.ping_toggle").each(function(){this.checked=true})});if(EE.user.can_edit_html_buttons){$(".markItUp ul").append('<li class="btn_plus"><a title="'+EE.lang.add_new_html_button+'" href="'+EE.BASE+"&C=myaccount&M=html_buttons&id="+
EE.user_id+'">+</a></li>');$(".btn_plus a").click(function(){return confirm(EE.lang.confirm_exit,"")})}$(".markItUpHeader ul").prepend('<li class="close_formatting_buttons"><a href="#"><img width="10" height="10" src="'+EE.THEME_URL+'images/publish_minus.gif" alt="Close Formatting Buttons"/></a></li>');$(".close_formatting_buttons a").toggle(function(){$(this).parent().parent().children(":not(.close_formatting_buttons)").hide();$(this).parent().parent().css("height","13px");$(this).children("img").attr("src",
EE.THEME_URL+"images/publish_plus.png")},function(){$(this).parent().parent().children().show();$(this).parent().parent().css("height","auto");$(this).children("img").attr("src",EE.THEME_URL+"images/publish_minus.gif")});$(".tab_menu li:first").addClass("current");EE.publish.title_focus==true&&$("#title").focus();EE.publish.which=="new"&&$("#title").bind("keyup blur",function(){EE.cp.live_url_title($("#title"),$("#url_title"))});EE.publish.versioning_enabled=="n"?$("#revision_button").hide():$("#versioning_enabled").click(function(){$(this).attr("checked")?
$("#revision_button").show():$("#revision_button").hide()});EE.publish.category_editor();if(EE.publish.hidden_fields){EE._hidden_fields=[];var f=$("input");$.each(EE.publish.hidden_fields,function(a){EE._hidden_fields.push(f.filter("[name="+a+"]")[0])});$(EE._hidden_fields).after('<p class="hidden_blurb">This module field only shows in certain circumstances. This is a placeholder to let you define it in your layout.</p>')}});
