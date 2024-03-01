<a class="modal-trigger" id="infomodel" href="#dialogbox" style="display: none;"></a>
<div id="dialogbox" class="modal modal-fixed-footer">
    <div class="modal-content">
        <h4 id='dialogTitle'>Netsutra - Leave Portal Says</h4>
        <div id='dialogContent'></div>
    </div>
    <div class="modal-footer">
       <input type="button" name="cancel_btn" id="close_dialogbox" value="Close" class="waves-effect waves-light btn red m-b-xs modal-close" />
    </div>
</div>


<a class="modal-trigger" id="updateLeavepopUp" href="#updateLeave" style="display: none;"></a>
<div id="updateLeave" class="modal modal-fixed-footer" style="width:300px;">
    <div class="modal-content">
        <h4 id='dialogTitle'>Netsutra - Leave Portal Says</h4>
        <div id='dialogContent'>
            <select class="browser-default" name="status" id="inputStatusDD">
                <option value="">Choose your option</option>
                <option value="1">Approved</option>
                <option value="2">Not Approved</option>
            </select>
            <br/><textarea id="inputDescription" name="inputDescription" class="materialize-textarea" placeholder="Description" length="500" maxlength="500"></textarea>
            <input type="hidden" name="hidden_leaveid" id="hidden_leaveid" value="" />
        </div>
    </div>
    <div class="modal-footer">
       <input type="button" id="btnCancel_updateLeave" value="Cancel" class="waves-effect waves-light btn red m-b-xs modal-close" />
       <input type="submit" class="waves-effect waves-light btn blue m-b-xs" onclick="validateUpdateLeave()" value="Submit" style="margin-right: 7px;" />
    </div>
</div>



<a class="modal-trigger" id="btnMissPunchPopUp" href="#missPunchPopUp" style="display: none;"></a>
<div id="missPunchPopUp" class="modal" style="width:300px;">
    <div class="modal-content">
        <h4 id='dialogTitle'>Netsutra - Leave Portal Says</h4>
        <div id='dialogContent'>
            <select class="browser-default" name="missPunchStatus" id="missPunchStatus">
                <option value="0">Choose your option</option>
                <option value="1">Approved</option>
                <option value="2">Not Approved</option>
            </select>
            <input type="hidden" name="hidden_misspunchid" id="hidden_misspunchid" value="" />
        </div>
    </div>
    <div class="modal-footer">
       <input type="button" id="btnCancel_updateMissPunch" value="Cancel" class="waves-effect waves-light btn red m-b-xs modal-close" />
       <input type="submit" class="waves-effect waves-light btn blue m-b-xs" onclick="updateMissPunch()" value="Submit" style="margin-right: 7px;" />
    </div>
</div>


<script>

function leaveDetail(leaveid){
    $("#overlay").show();
    $.ajax({
        type: "post",
        url: "leavedetail.php",
        data: {leaveid: leaveid},
        success: function(data){
            $('#dialogContent').html(data);
            showInfoModel('Leave Detail');
            $(".btn-open-edit").on('click', function(){
                editLeaveDates();
            });
            $("#dialogbox").css({"height": "550px"});
        }
    });
}

function leaveApproval(id,action){
    console.log(action);
    
    if(action == 'disapprove'){
        $.ajax({
            type: "post",
            url: "leavedetail.php",
            data: {leaveid: id, action:action},
            success: function(data){
                $('#dialogContent').html(data);
                showInfoModel('Leave Detail');
                $(".btn-open-edit").on('click', function(){
                    editLeaveDates();
                });
                $("#dialogbox").css({"height": "550px"});
            }
        });
    }

    if(action == 'approve'){
        $.ajax({
            type: "post",
            url: "update-leave-status.php",
            data: {leaveId: id, action:'leave-approval',status:1},
            success: function(data){
                var response = JSON.parse(data);
                alert(response.msg);
                filterData();
            }
        });
    }
}

function submitEditLeaveDates(){
    $.ajax({
        type : 'POST',
        url : 'update-leave-status.php',
        data : $('#editLeaveDates').serialize(),
        success: function(data){
            var response = JSON.parse(data);
            alert(response.msg);
            filterData();
            hideInfoModel();
        }
    });
}

function submitApproval(){
    $.ajax({
        type : 'POST',
        url : 'update-leave-status.php',
        data : $('#editLeaveDates').serialize(),
        success: function(data){
            var response = JSON.parse(data);
            alert(response.msg);
            filterData();
            hideInfoModel();
        }
    });
}

function editLeaveDates(){
    $("#editLeaveDates").fadeIn();
    $(".btn-open-edit").hide();
    $( ".datepicker_filter" ).datepicker();
    $( ".datepicker_filter" ).datepicker("option", "dateFormat", "dd-mm-yy");
    $( "#fDate" ).datepicker( "setDate" , $("#defaultFromDate").val() );
    $( "#toDate" ).datepicker( "setDate" , $("#defaultToDate").val() );
}
function cancelEditLeaveDates(){
    $("#editLeaveDates").fadeOut();
    $(".btn-open-edit").show();
}
function showRemark(remark){
    $("#overlay").show();
    $('#dialogContent').html(remark);
    showInfoModel('Admin Remark');
    $("#dialogbox").css('height',"300px");
}    
function getUserDetail(id){
    $("#overlay").show();
    $.ajax({
        type: "post",
        url: "userdetail.php",
        data: {id: id},
        success: function(data){
            $('#dialogContent').html(data);
            showInfoModel('User Detail');
            $("#dialogbox").css('height',"400px");
        }
    });
}

function openUpdateLeavePopup(encodedId){
    $("#hidden_leaveid").val(encodedId);
    $("#updateLeavepopUp").trigger('click');
}

function openMissPunchPopup(encodedId){
    $("#hidden_misspunchid").val(encodedId);
    $("#btnMissPunchPopUp").trigger('click');
}

function updateLeave(){
    $("#overlay").show();
    var leaveid = $("#hidden_leaveid").val();
    var description = $("#inputDescription").val();
    var status = $("#inputStatusDD").val();
    $.ajax({
        type: "post",
        url: "take-action.php",
        data: {'leaveid': leaveid,'description':description,'status':status,'update':true},
        success: function(data){
            result = JSON.parse(data);
            if(result.status == 'SUCCESS' || result.status == 'INFO'){
                hideModel('updateLeave');
                filterData();
            }
            alert(result.msg);
            $("#overlay").hide();
        }
    });
}

function updateMissPunch(){
    $("#overlay").show();
    var id = $("#hidden_misspunchid").val();
    var status = $("#missPunchStatus").val();
    if(status == '0'){
        alert("Please select any option.");
        $("#overlay").hide();
        return false;
    }
    saveMissPunchApproval(id, status);
}

function saveMissPunchApproval(id, status){
    $.ajax({
        type: "post",
        url: "misspunch_approval.php",
        data: {'id': id,'status':status,'update':true},
        success: function(data){
            $("#overlay").hide();
            hideModel('updateMissPunch');
            filterData();
            result = JSON.parse(data);
            alert(result.msg);
        }
    });
}

function showInfoModel(title){
    $("#overlay").hide();
    $("#dialogTitle").html(title);
    $("#infomodel").trigger('click');
}
function hideInfoModel(){
    $('#close_dialogbox').trigger('click');

}
function hideModel(id){
    $('#btnCancel_'+id).trigger('click');
}
function validateUpdateLeave(){
    if($("#inputStatusDD").val()== ""){
        $("#inputStatusDD").addClass("red_border_take_action");
        return false;
    }else{
        updateLeave();
    }
}
$( function() {
    $( ".datepicker_filter" ).datepicker();
    $( ".datepicker_filter" ).datepicker("option", "dateFormat", "dd-mm-yy");
});


function leaveCountsDetail(empId,month,year){
    $("#overlay").show();
    $.ajax({
        type: "post",
        url: "leave_adjustment_ajax.php",
        data: {showLeaveCounts:true,empId: empId, month: month, year:year},
        success: function(data){
            $('#dialogContent').html(data);
            showInfoModel('Adjust Leave');
            $("#btnUpdateLeaveAdjust").on('click', function(){
                updateLeaveCounts(empId,month,year);
            });
            $("#dialogbox").css({"height": "650px","width": "650px"});
        }
    });
}

function updateLeaveCounts(id,month,year){
    var totalIL = $("#empTotalIL").val();
    var totalCL = $("#empTotalCL").val();
    var totalCO = $("#empTotalCO").val();
    $.ajax({
        type: "post",
        url: "leave_adjustment_ajax.php",
        data: {updateLeaveCounts:true,empId: id, month: month, year:year, totalIL:totalIL, totalCL:totalCL, totalCO:totalCO},
        success: function(data){
            hideInfoModel();
            filterData();
            if(data){
                var response = JSON.parse(data);
                if(response.status == "200")
                    alert("Data saved successfully");
                else
                    alert("Oops !! updation failed. Please try again later.");
            }else{
                alert("Oops !! updation failed. Please try again later.")
            }
        }
    });
}

</script>