
<div class="row">
  <div class="col-sm-2 padding-5 first">
    <label>บาร์โค้ดโซน</label>
    <input type="text" class="form-control input-sm" id="zone-code" placeholder="ระบุโซนที่จะรับเข้า" value="<?php echo $zoneCode; ?>"  <?php echo $inputActive; ?> autofocus />
  </div>
  <div class="col-sm-3 padding-5">
    <label>โซนรับสินค้า</label>
    <input type="text" class="form-control input-sm" id="zone-box" placeholder="ระบุโซนที่จะรับเข้า" value="<?php echo $zoneName; ?>" <?php echo $inputActive; ?> autofocus/>
  </div>

  <div class="col-sm-1 padding-5">
    <label class="display-block not-show">change zone</label>
    <button type="button" class="btn btn-sm btn-info btn-block" id="btn-change-zone" onclick="changeZone()" <?php echo $changeZoneActive; ?>>เปลี่ยนโซน</button>
  </div>
  <input type="hidden" id="id_zone" value="<?php echo $idZone; ?>" />
</div>
