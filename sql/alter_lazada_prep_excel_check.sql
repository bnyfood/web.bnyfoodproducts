/*
  Excel Check prep: ALTER + migrate + view
  Database: bnyfoodproducts
  Run this in SSMS BEFORE the next Lazada Check.

  Buckets (exactly three):
    ignore = before pack (unpaid / pending / cancel before pack). Not in this month's tax total.
    tax    = after pack, still a sale (packed through confirmed).
    cn     = after pack, then reverse (Package Returned, cancel after pack, lost/damaged).

  Tax (gross) is the SUPERSET: everything after pack = bucket IN ('tax','cn').
  CN is a subset of Tax. Cancel after pack is Tax AND CN, not Ignore.
  Net tax = Tax(gross) - CN.
  taxable = AV + AW (unitPrice + sellerDiscountTotal)

  paid_price kept as legacy (old rows = Excel paidPrice AU;
    rows after the PHP Check change already stored AV+AW in paid_price)

  After this script, Check PHP will write unit_price, seller_discount, taxable, bucket.
*/

USE [bnyfoodproducts];
GO

/* ========== 1) lazada_prep : add columns ========== */

IF COL_LENGTH('dbo.lazada_prep', 'unit_price') IS NULL
  ALTER TABLE dbo.lazada_prep ADD unit_price DECIMAL(18,2) NULL;

IF COL_LENGTH('dbo.lazada_prep', 'seller_discount') IS NULL
  ALTER TABLE dbo.lazada_prep ADD seller_discount DECIMAL(18,2) NULL;

IF COL_LENGTH('dbo.lazada_prep', 'taxable') IS NULL
  ALTER TABLE dbo.lazada_prep ADD taxable DECIMAL(18,2) NULL;

IF COL_LENGTH('dbo.lazada_prep', 'bucket') IS NULL
  ALTER TABLE dbo.lazada_prep ADD bucket VARCHAR(20) NULL;
GO

/* ========== 2) lazada_prep_api : add taxable ========== */

IF COL_LENGTH('dbo.lazada_prep_api', 'taxable') IS NULL
  ALTER TABLE dbo.lazada_prep_api ADD taxable DECIMAL(18,2) NULL;
GO

/* ========== 3) migrate existing Excel prep rows ========== */

-- Copy whatever is in paid_price into taxable.
-- Old Check runs: paid_price was AU (includes platform discount + shipping).
-- New Check runs already stored AV+AW in paid_price.
UPDATE dbo.lazada_prep
SET taxable = ISNULL(taxable, paid_price)
WHERE taxable IS NULL;
GO

-- Best-effort bucket from Excel status (BO).
UPDATE dbo.lazada_prep
SET bucket = CASE
  WHEN LOWER(REPLACE(LTRIM(RTRIM(ISNULL(status,''))), '_', ' ')) IN (
    'package returned', 'returned', 'lost by 3pl', 'damaged by 3pl',
    'failed delivery', 'shipped back', 'shipped back success', 'shipped back failed',
    'package scrapped', 'lost', 'damaged'
  ) THEN 'cn'
  WHEN LOWER(LTRIM(RTRIM(ISNULL(status,'')))) IN ('unpaid', 'pending') THEN 'ignore'
  WHEN LOWER(LTRIM(RTRIM(ISNULL(status,'')))) IN ('canceled', 'cancelled', 'cancel') THEN 'ignore'
  WHEN LOWER(REPLACE(LTRIM(RTRIM(ISNULL(status,''))), '_', ' ')) IN (
    'packed', 'repacked', 'topack', 'toship', 'ready to ship', 'ready to ship pending',
    'shipped', 'shipping', 'delivered', 'confirmed'
  ) THEN 'tax'
  ELSE 'ignore'
END
WHERE bucket IS NULL;
GO

-- Cancel after pack: if API already has packed, this Excel cancel is CN.
UPDATE e
SET e.bucket = 'cn'
FROM dbo.lazada_prep e
WHERE e.bucket = 'ignore'
  AND LOWER(LTRIM(RTRIM(ISNULL(e.status,'')))) IN ('canceled', 'cancelled', 'cancel')
  AND EXISTS (
    SELECT 1
    FROM dbo.lazada_orders o
    WHERE o.order_number = e.order_number
      AND o.status = 'packed'
  );
GO

-- After-pack reason on Excel even if status is canceled.
UPDATE dbo.lazada_prep
SET bucket = 'cn'
WHERE bucket = 'ignore'
  AND LOWER(LTRIM(RTRIM(ISNULL(status,'')))) IN ('canceled', 'cancelled', 'cancel')
  AND (
    cancel_reason LIKE N'%ปฏิเสธการรับ%'
    OR cancel_reason LIKE N'%ติดต่อลูกค้าไม่ได้%'
    OR cancel_reason LIKE N'%การจัดส่งไม่สำเร็จ%'
  );
GO

/* ========== 4) migrate existing API prep rows ========== */

UPDATE dbo.lazada_prep_api
SET taxable = ISNULL(price, 0) - ISNULL(voucher_seller, 0)
WHERE taxable IS NULL;
GO

/* ========== 5) view for Excel vs API Check ========== */

IF OBJECT_ID('dbo.vw_lazada_prep_check', 'V') IS NOT NULL
  DROP VIEW dbo.vw_lazada_prep_check;
GO

CREATE VIEW dbo.vw_lazada_prep_check
AS
SELECT
  e.lazada_prep_id,
  e.code,
  e.order_number,
  e.createtime,
  e.status,
  e.initiator,
  e.cancel_reason,
  e.bucket,
  CASE WHEN e.bucket IN ('tax','cn') THEN 1 ELSE 0 END AS in_tax,
  CASE WHEN e.bucket = 'cn' THEN 1 ELSE 0 END AS is_cn,
  CASE WHEN e.bucket IN ('tax','cn') THEN ISNULL(e.taxable, e.paid_price) ELSE 0 END AS excel_tax_gross,
  CASE WHEN e.bucket = 'cn' THEN ISNULL(e.taxable, e.paid_price) ELSE 0 END AS excel_cn,
  e.unit_price,
  e.seller_discount,
  e.taxable AS excel_taxable,
  e.shippingFee AS excel_shipping_fee,
  e.paid_price AS paid_price_legacy,
  a.transactiondate,
  a.price AS api_price,
  a.voucher_seller AS api_voucher_seller,
  a.voucher_platform AS api_voucher_platform,
  a.voucher AS api_voucher,
  a.shipping_fee AS api_shipping_fee,
  a.taxable AS api_taxable,
  (ISNULL(e.taxable, e.paid_price) - ISNULL(a.taxable, ISNULL(a.price, 0) - ISNULL(a.voucher_seller, 0))) AS diff_taxable
FROM dbo.lazada_prep e
LEFT OUTER JOIN dbo.lazada_prep_api a
  ON e.order_number = a.order_number
 AND e.code = a.code;
GO

/* ========== 6) sanity checks (optional) ========== */

-- SELECT bucket, COUNT(*) cnt, SUM(taxable) amt FROM dbo.lazada_prep GROUP BY bucket;
-- Tax gross = SUM(excel_tax_gross) = tax rows + cn rows
-- SELECT SUM(excel_tax_gross) tax_gross, SUM(excel_cn) cn, SUM(excel_tax_gross)-SUM(excel_cn) net FROM dbo.vw_lazada_prep_check;
-- SELECT TOP 50 * FROM dbo.vw_lazada_prep_check ORDER BY lazada_prep_id DESC;
