var $ = jQuery.noConflict();

function updateOfficer(oid, cid, action) {
    var club_id = $("input[name='club_id']").val();
    var desc = '0';
    var auth = '0';
    if (action == 'update') {
        desc = $("input[id='desc-" + oid + '-' + cid + "']").val().trim();
        auth = $("select[id='sel-" + oid + '-' + cid + "']").val();
        if (desc.length == 0) {
            desc = '0';
        }
    } else {
        $result = confirm("Are you sure you want to remove this relationship?");
        if ($result == false) return;   
    }
    location.href = "/club_details/update_officer/" + club_id + '/' + oid + '/' + cid + '/' + action + '/' + desc + '/' + auth; 
}

function updateRegionalOfficer(oid, cid, action) {
    var region_id = $("input[name='region_id']").val();
    var desc = '0';
    var auth = '0';
    if (action == 'update') {
        desc = $("input[id='desc-" + oid + '-' + cid + "']").val().trim();
        auth = $("select[id='sel-" + oid + '-' + cid + "']").val();
        if (desc.length == 0) {
            desc = '0';
        }
    } else {
        $result = confirm("Are you sure you want to remove this relationship?");
        if ($result == false) return;   
    }
    location.href = "/club_details/update_regional_officer/" + region_id + '/' + oid + '/' + cid + '/' + action + '/' + desc + '/' + auth; 
}
