/*
  Shopee Excel Check prep: ALTER + migrate + view
  Database: bnyfoodproducts
  Run this in SSMS BEFORE the next Shopee Check.

  Same pattern as alter_lazada_prep_excel_check.sql.

  Excel tax (once per order, do not sum SKU rows):
    AO + AA + AD + AH + AP
    = buyer_prod + platform_disc + shipping_fee
    = ราคาสินค้าที่ชำระโดยผู้ซื้อ
      + ส่วนลดจาก Shopee
      + โค้ดส่วนลดชำระโดย Shopee
      + ส่วนลด bundle deal ชำระโดย Shopee
      + ค่าจัดส่งที่ชำระโดยผู้ซื้อ

  Seller-funded AB/AG are stored in seller_discount for reference only.
  They are NOT added into taxable.

  API taxable (same money as Excel):
    priceVATincluded
    (= price - seller_discount + shipping_fee on the Check SP)
    Do NOT add voucher_platform (double-counts platform; mismatch diff was platform - seller voucher).

  Buckets (exactly three):
    ignore = before pack (unpaid timeout / cancel before ship). Not in this month's tax total.
    tax    = after pack, still a sale (สำเร็จแล้ว / การจัดส่ง).
    cn     = after pack, then reverse (return accepted / การจัดส่งไม่สำเร็จ).
*/

USE [bnyfoodproducts];
GO

/* ========== 1) shopee_prep : Excel lanes + taxable + bucket ========== */

IF COL_LENGTH('dbo.shopee_prep', 'buyer_prod') IS NULL
  ALTER TABLE dbo.shopee_prep ADD buyer_prod DECIMAL(18,2) NULL;

IF COL_LENGTH('dbo.shopee_prep', 'platform_disc') IS NULL
  ALTER TABLE dbo.shopee_prep ADD platform_disc DECIMAL(18,2) NULL;

IF COL_LENGTH('dbo.shopee_prep', 'taxable') IS NULL
  ALTER TABLE dbo.shopee_prep ADD taxable DECIMAL(18,2) NULL;

IF COL_LENGTH('dbo.shopee_prep', 'bucket') IS NULL
  ALTER TABLE dbo.shopee_prep ADD bucket VARCHAR(20) NULL;
GO

/* ========== 2) shopee_prep_api : taxable + seller_discount ========== */

IF COL_LENGTH('dbo.shopee_prep_api', 'taxable') IS NULL
  ALTER TABLE dbo.shopee_prep_api ADD taxable DECIMAL(18,2) NULL;

IF COL_LENGTH('dbo.shopee_prep_api', 'seller_discount') IS NULL
  ALTER TABLE dbo.shopee_prep_api ADD seller_discount DECIMAL(18,2) NULL;
GO

/* ========== 3) migrate existing Excel prep rows ========== */

-- New Check already stores AO+platform+AP in paid_price.
UPDATE dbo.shopee_prep
SET taxable = ISNULL(taxable, paid_price)
WHERE taxable IS NULL;
GO

UPDATE dbo.shopee_prep
SET bucket = CASE
  WHEN cancel_reason LIKE N'%คำขอได้รับการยอมรับ%' THEN 'cn'
  WHEN cancel_reason LIKE N'%การจัดส่งไม่สำเร็จ%' THEN 'cn'
  WHEN cancel_reason LIKE N'%ไม่มีการชำระเงิน%' THEN 'ignore'
  WHEN status = N'ยกเลิกแล้ว' THEN 'ignore'
  WHEN status IN (N'สำเร็จแล้ว', N'การจัดส่ง') THEN 'tax'
  ELSE 'ignore'
END
WHERE bucket IS NULL;
GO

-- Cancel after pack: Excel cancel is CN if API already shipped/processed.
UPDATE e
SET e.bucket = 'cn'
FROM dbo.shopee_prep e
WHERE e.bucket = 'ignore'
  AND e.status = N'ยกเลิกแล้ว'
  AND EXISTS (
    SELECT 1
    FROM dbo.shopee_orders o
    WHERE o.order_sn = e.order_sn
      AND o.order_status IN (
        'READY_TO_SHIP', 'PROCESSED', 'SHIPPED', 'TO_CONFIRM_RECEIVE',
        'COMPLETED', 'TO_RETURN', 'RETURNED'
      )
  );
GO

/* ========== 4) migrate existing API prep rows ========== */

-- Match Excel: SP priceVATincluded already = sell net + buyer shipping.
UPDATE dbo.shopee_prep_api
SET taxable = ISNULL(priceVATincluded, ISNULL(price, 0) - ISNULL(seller_discount, 0) + ISNULL(shipping_fee, 0))
WHERE taxable IS NULL;
GO

/* ========== 5) view for Excel vs API Check ========== */

IF OBJECT_ID('dbo.vw_shopee_prep_check', 'V') IS NOT NULL
  DROP VIEW dbo.vw_shopee_prep_check;
GO

CREATE VIEW dbo.vw_shopee_prep_check
AS
SELECT
  e.shopee_prep_id,
  e.code,
  e.order_sn,
  e.order_date,
  e.status,
  e.cancel_reason,
  e.bucket,
  CASE WHEN e.bucket IN ('tax','cn') THEN 1 ELSE 0 END AS in_tax,
  CASE WHEN e.bucket = 'cn' THEN 1 ELSE 0 END AS is_cn,
  CASE WHEN e.bucket IN ('tax','cn') THEN ISNULL(e.taxable, e.paid_price) ELSE 0 END AS excel_tax_gross,
  CASE WHEN e.bucket = 'cn' THEN ISNULL(e.taxable, e.paid_price) ELSE 0 END AS excel_cn,
  e.buyer_prod AS excel_ao,
  e.platform_disc AS excel_platform,
  e.shipping_fee AS excel_ap,
  e.seller_discount AS excel_seller_funded,
  e.taxable AS excel_taxable,
  e.paid_price AS paid_price_legacy,
  a.transactiondate,
  a.price AS api_price,
  a.seller_discount AS api_seller_discount,
  a.voucher_seller AS api_voucher_seller,
  a.voucher_platform AS api_voucher_platform,
  a.shipping_fee AS api_shipping_fee,
  a.priceVATincluded AS api_priceVATincluded,
  a.taxable AS api_taxable,
  (
    ISNULL(e.taxable, e.paid_price)
    - ISNULL(
        a.taxable,
        ISNULL(a.priceVATincluded, ISNULL(a.price, 0) - ISNULL(a.seller_discount, 0) + ISNULL(a.shipping_fee, 0))
      )
  ) AS diff_taxable
FROM dbo.shopee_prep e
LEFT OUTER JOIN dbo.shopee_prep_api a
  ON e.order_sn = a.order_sn
 AND e.code = a.code;
GO

/* ========== 6) sanity checks (optional) ========== */

-- SELECT bucket, COUNT(*) cnt, SUM(taxable) amt FROM dbo.shopee_prep GROUP BY bucket;
-- SELECT SUM(excel_tax_gross) tax_gross, SUM(excel_cn) cn, SUM(excel_tax_gross)-SUM(excel_cn) net FROM dbo.vw_shopee_prep_check;
-- SELECT TOP 50 * FROM dbo.vw_shopee_prep_check WHERE ABS(diff_taxable) >= 0.01 ORDER BY shopee_prep_id DESC;
