{literal}
    <script type="text/javascript">
      CRM.$(function($) {
        $('.crm-mosaico-form-block-mosaico_plugin').insertAfter('.crm-mosaico-form-block-mosaico_custom_templates_url');
        $('.crm-mosaico-form-block-mosaico_custom_toolbar').insertAfter('.crm-mosaico-form-block-mosaico_plugin');
        $('#wrapper-prefix').remove();
      });
    </script>
{/literal}
<table class="form-layout-compressed" id="wrapper-prefix">
    <tr class="crm-mosaico-form-block-mosaico_plugin">
        <td class="label">
            {$form.mosaico_custom_plugins.label}
        </td>
        <td>
            {$form.mosaico_custom_plugins.html|crmAddClass:'huge40'} {help id='mosaico_custom_plugins'}<br/>
            <span class="description">{$mosaico_custom_plugins_description}</span>
        </td>
    </tr>
    <tr class="crm-mosaico-form-block-mosaico_custom_toolbar">
        <td class="label">
            {$form.mosaico_custom_toolbar.label}
        </td>
        <td>
            {$form.mosaico_custom_toolbar.html|crmAddClass:'huge40'}  {help id='mosaico_custom_toolbar'}<br/>
            <span class="description">{$mosaico_custom_toolbar_description}</span>
        </td>
    </tr>
</table>