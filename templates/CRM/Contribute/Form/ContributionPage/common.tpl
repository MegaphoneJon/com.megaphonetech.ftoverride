{if $showElement}
<table class="ft_override_designation-block">
  <tr class="crm-contribution-contributionpage-settings-form-block-ft_override_designation">
    <td scope="row" class="label" width="20%">{$form.designation.label}</td>
    <td>{$form.designation.html}</td>
  </tr>
</table>
{literal}
<script type="text/javascript">
  CRM.$(function($) {
    $('tr.crm-contribution-contributionpage-settings-form-block-financial_type_id').after($('table.ft_override_designation-block tr'));
    $('#designation').change(function(e) {
      var data = $('#designation').select2('data');
      var selectedDesignations = new Array();
      $.each(data, function( index, value ) {
        selectedDesignations.push(value.id);
      });
      $('input[name="hidden_designation"]').val(selectedDesignations.join(","));
    });
  });
</script>
{/literal}
{/if}
