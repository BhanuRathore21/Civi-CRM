{*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2014                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*}
{literal}
    <style type="text/css">
        #col-groups, #custom_filter_by_type_op{display:none;}
        .report-layout .crm-report-criteria-filter-civicrm_contact{display:none;}
        #crm-container .report-contents-right{border-right: none;}
    </style>
{/literal}
{include file="CRM/Report/Form1.tpl"}
{literal}
<script type="text/javascript">
  CRM.$(function($){
    var filterOptionsShow = function(arg) {
        if(0 <= $.inArray('event', arg)) {
          $('#event_id_op').parents('tr').show('slow');
        } else {
          $("#event_id_value").val('');
          $('#event_id_op').parents('tr').hide();
        }

        if(0 <= $.inArray('contribution', arg)) {
          $('#donation_list_op').parents('tr').show();
        } else {
          $("#donation_list_value").val('');
          $('#donation_list_op').parents('tr').hide();
        }

        if(0 <= $.inArray('pledge', arg)) {
          $('#financial_type_id_op').parents('tr').show('slow');
        } else {
          $('#financial_type_id_value').val('');
          $('#financial_type_id_op').parents('tr').hide();
        }
    };

    //On change
    $("#custom_filter_by_type_value").on('change', function(){
      var str = $(this).val();
      filterOptionsShow(str);
    });
    
    //Page onload
    var ddValue = $("#custom_filter_by_type_value").val();
    filterOptionsShow(ddValue);
    
    $('#custom_filter_by_value').on('change', function(){
        if($(this).val() != '') {
            $('#register_date_relative').parents('tr').show('slow');
        } else {
            $('#register_date_relative').val('').trigger('change');
            $('#register_date_relative').parents('tr').hide();
        }
    });
    $('#custom_filter_by_value').trigger('change');
    
    $("#_qf_FinancialDetail_submit").click(function(){
        var dateFilter = $('#custom_filter_by_value').val();
        var ddValue = $("#custom_filter_by_type_value").val();
        if(dateFilter == '' && $.isArray(ddValue) == false) {
            alert('Please choose atleast one filter.');
            return false;
        } else if(dateFilter == 'reg_date' && ddValue.length >1) {
            alert('Please choose only one Filter By while choosing Transaction Date.');
            return false;
        }
        return true;
    });
  });
</script>
{/literal}
