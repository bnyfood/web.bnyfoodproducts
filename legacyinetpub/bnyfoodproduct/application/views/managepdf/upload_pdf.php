<div class="container-fluid p-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-file-pdf"></i> PDF to Excel - Receipt Extractor</h4>
                </div>
                <div class="card-body">

                    <?php if($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $this->session->flashdata('error'); ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                    <?php endif; ?>

                    <?php if($this->session->flashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle"></i> <?php echo $this->session->flashdata('success'); ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                    <?php endif; ?>

                    <form action="<?php echo base_url('managepdf/upload_process'); ?>" method="post" enctype="multipart/form-data" id="uploadForm">
                        <div class="form-group">
                            <label><strong>เลือกไฟล์ PDF (1 ไฟล์ = 1 Receipt)</strong></label>
                            <input type="file" class="form-control" id="pdf_files" name="pdf_files[]" accept=".pdf" multiple required>
                            <small class="form-text text-muted">เลือกหลายไฟล์พร้อมกันได้ (กด Ctrl ค้างแล้วคลิกเลือก)</small>
                        </div>

                        <div id="fileCount" class="mb-3" style="display:none;">
                            <span class="badge badge-info" id="fileCountBadge"></span>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-success btn-lg" id="btnSubmit">
                                <i class="fas fa-file-excel"></i> สร้าง Excel และ Download
                            </button>
                        </div>
                    </form>

                    <hr>
                    <div class="mt-3">
                        <h5><i class="fas fa-info-circle text-info"></i> รายละเอียด</h5>
                        <ul class="list-unstyled ml-3">
                            <li><i class="fas fa-check text-success"></i> เลือกไฟล์ PDF หลายไฟล์พร้อมกัน (1 ไฟล์ = 1 Receipt)</li>
                            <li><i class="fas fa-check text-success"></i> ระบบจะดึง <strong>Receipt Number</strong> และ <strong>Total Amount</strong> จากแต่ละไฟล์</li>
                            <li><i class="fas fa-check text-success"></i> สร้างไฟล์ Excel (.xlsx) พร้อม download อัตโนมัติ</li>
                        </ul>
                    </div>

                    <div class="mt-3">
                        <h5><i class="fas fa-table text-primary"></i> ตัวอย่าง Output Excel</h5>
                        <table class="table table-bordered table-sm" style="max-width:500px;">
                            <thead class="thead-light">
                                <tr>
                                    <th>No.</th>
                                    <th>Receipt Number</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>TTSTHAC20250013402995</td>
                                    <td>16.90</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>TTSTHAC20250013542421</td>
                                    <td>187.30</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('pdf_files').addEventListener('change', function() {
    var count = this.files.length;
    var el = document.getElementById('fileCount');
    var badge = document.getElementById('fileCountBadge');
    if (count > 0) {
        el.style.display = 'block';
        badge.textContent = 'เลือกแล้ว ' + count + ' ไฟล์';
    } else {
        el.style.display = 'none';
    }
});

document.getElementById('uploadForm').addEventListener('submit', function() {
    var btn = document.getElementById('btnSubmit');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> กำลังประมวลผล...';
});
</script>
