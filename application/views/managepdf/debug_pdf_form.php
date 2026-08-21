<div class="container-fluid p-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-warning">
                    <h4 class="mb-0"><i class="fas fa-bug"></i> Debug PDF Text Extraction</h4>
                </div>
                <div class="card-body">
                    <form action="<?php echo base_url('managepdf/debug_pdf_process'); ?>" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label><strong>เลือกไฟล์ PDF เพื่อดู text ที่ดึงออกมาได้</strong></label>
                            <input type="file" class="form-control" name="pdf_file" accept=".pdf" required>
                        </div>
                        <button type="submit" class="btn btn-warning btn-lg mt-3">
                            <i class="fas fa-search"></i> Debug
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
