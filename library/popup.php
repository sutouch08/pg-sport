
<!-- Modal Popup Satrt -->
<?php 
echo "
<div class='modal fade' id='modal_popup' tabindex='-1' role='dialog'>
    <div class='modal-dialog' style='width: ".$width."px; max-width:1000px;'>
        <div class='modal-content' style='width: ".$width."px; max-width:1000px;'>
            <div class='modal-header'>
             	<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>
             </div>
            <div class='modal-body'>
           		  $popup_content
            </div>
             <div class='modal-footer'>
             <button type='button' class='btn btn-primary' data-dismiss='modal'>รับทราบ</button>
             </div>
        </div>
    </div>
</div>";
?>
<!-- // Modal Popup End -->