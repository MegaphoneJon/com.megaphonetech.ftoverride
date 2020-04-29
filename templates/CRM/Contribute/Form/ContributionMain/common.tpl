<div class="crm-public-form-item crm-section ft_override_designation-section">
  <div class="label">{$form.designation.label}</div>
  <div class="content">{$form.designation.html}</div>
  <div class="clear"></div>
  <div class="content designation_note-div">{$form.designation_note.html}</div>
  <div class="clear"></div>
</div>
{literal}
<script type="text/javascript">
  CRM.$(function($) {
    $('div#priceset:first').after($('div.ft_override_designation-section'));

    hideShowDesignation();
    $('#designation').change(hideShowDesignation);

    function hideShowDesignation() {
      var fT = $('#designation').val();
      if (fT == 'other_financial_type') {
        $('div.ft_override_designation-section div.designation_note-div').show();
      }
      else {
        $('div.ft_override_designation-section div.designation_note-div').hide();
      }
    }
  });
</script>
{/literal}
