<div class="container">
  <div class="row top-row">
    <div class="col-sm-6 top-col">
      <h4 class="title"><i class="fa fa-download"></i> <?php echo $pageTitle; ?></h4>
    </div>
    <div class="col-sm-6">
      <p class="pull-right top-p">
        <button type="button" class="btn btn-sm btn-success" onclick="addNew()"><i class="fa fa-plus"></i> เพิ่มใหม่</button>
      </p>
    </div>
  </div>
  <hr/>
  <div class="row">
    <div class="col-sm-3 padding-5 first">
      <label>เลขที่</label>
      <input type="text" class="form-control input-sm text-center" name="sCode" id="sCode" placeholder="ค้นหาบิล" autofocus />
    </div>
    <div class="col-sm-1 padding-5">
      <label class="display-block not-show">Search</label>
      <button type="button" class="btn btn-sm btn-primary btn-block" onclick="getSearch()"><i class="fa fa-search"></i> ค้นหา</button>
    </div>
  </div>
</div><!--/ container -->
