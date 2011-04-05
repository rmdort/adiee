INSTALLATION

1) Download and extract the add-on
2) Copy the ajw_selecteditems folder to your system/expressionengine/third_party folder
3) In the Control Panel, got Add-Ons > Modules and click on Install on the AJW Selected Items line.


USAGE

Wrap the {exp:ajw_selecteditems} tag around a {exp:channel:entries} tag.

The name= parameter should correspond to the name of a selection created in the 
module’s Control Panel.

<ul>
{exp:ajw_selecteditems name="Test"}
{exp:channel:entries channel="default_site|feeds|twitter" fixed_order="{ajw_selecteditems}" dynamic="off" disable="pagination|custom_fields|categories|member_data|trackbacks"}
<li><a href="{comment_url_title_auto_path}">{title}</a></li>
{/exp:channel:entries}
{/exp:ajw_selecteditems}
</ul>

