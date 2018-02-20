<div class="modal fade" id="pause-bill-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal" style="width:300px;">
		<div class="modal-content">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modal_title">บิลที่พักไว้</h4>
			 </div>
			 <div class="modal-body" id="modal_body"></div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
			 </div>
		</div>
	</div>
</div>


<script id="pause-list-template" type="text/x-handlebarsTemplate">
<div class="row">
  <div class="col-sm-12">
    <table class="table table-striped">
      {{#each this}}
        {{#if nodata}}
        <tr>
          <td class="width-100 text-center">ไม่มีบิลที่พักไว้</td>
        </tr>
        {{else}}
        <tr>
          <td class="width-50">
            {{reference}}
          </td>
          <td class="width-50">
            <button type="button" class="btn btn-sm btn-info btn-block" onclick="viewOrder({{id}})">จัดการ</button>
          </td>
        </tr>
        {{/if}}
      {{/each}}
    </table>
  </div>
</div>
</script>
