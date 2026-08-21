/*
  TikTok Excel Check prep — FIXED
  Database: bnyfoodproducts

  Your error Msg 4909 means dbo.tiktok_prep is NOT a table (it is a VIEW).
  This script:
    1) Detects view vs table
    2) If VIEW → DROP VIEW, CREATE real TABLE dbo.tiktok_prep
    3) Alters tiktok_prep_api (taxable) if needed
    4) Creates vw_tiktok_prep_check

  Safe to re-run. Does NOT delete tiktok_data or tiktok_orders.
  Excel staging table tiktok_data stays as-is; modern Check will write tiktok_prep.
*/

USE [bnyfoodproducts];
GO

/* ========== 0) What is tiktok_prep? (read results) ========== */
SELECT
  o.name,
  o.type_desc,
  CASE WHEN o.type = 'U' THEN 'TABLE'
       WHEN o.type = 'V' THEN 'VIEW'
       ELSE o.type_desc END AS kind
FROM sys.objects o
WHERE o.name IN ('tiktok_prep', 'tiktok_prep_api', 'tiktok_data', 'vw_tiktok_prep_check')
ORDER BY o.name;
GO

/* Show view definition if it is a view (optional read) */
IF OBJECT_ID('dbo.tiktok_prep', 'V') IS NOT NULL
BEGIN
  SELECT OBJECT_DEFINITION(OBJECT_ID('dbo.tiktok_prep')) AS tiktok_prep_view_definition;
END
GO

/* ========== 1) Replace VIEW with real TABLE ========== */

IF OBJECT_ID('dbo.tiktok_prep', 'V') IS NOT NULL
BEGIN
  DROP VIEW dbo.tiktok_prep;
END
GO

-- Drop broken check view from the failed previous run (if any)
IF OBJECT_ID('dbo.vw_tiktok_prep_check', 'V') IS NOT NULL
  DROP VIEW dbo.vw_tiktok_prep_check;
GO

IF OBJECT_ID('dbo.tiktok_prep', 'U') IS NULL
BEGIN
  CREATE TABLE dbo.tiktok_prep (
    tiktok_prep_id            INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
    order_sn                  VARCHAR(64) NULL,          -- TikTok order_id
    order_date                DATETIME NULL,
    status                    NVARCHAR(100) NULL,
    cancel_type               NVARCHAR(200) NULL,
    cancel_reason             NVARCHAR(500) NULL,
    paid_price                DECIMAL(18,2) NULL,        -- legacy / mirror of taxable
    cn_paid_price             DECIMAL(18,2) NULL,
    logistic_price            DECIMAL(18,2) NULL,
    shipping_fee              DECIMAL(18,2) NULL,        -- Excel Q once per order
    unit_price                DECIMAL(18,2) NULL,        -- SUM(L)
    seller_discount           DECIMAL(18,2) NULL,        -- SUM(O)
    subtotal_after_discount   DECIMAL(18,2) NULL,        -- P
    order_amount_w            DECIMAL(18,2) NULL,        -- raw Excel W (legacy)
    taxable                   DECIMAL(18,2) NULL,        -- Shopee-style: W + N + PaymentPlat (= P + N + Q)
    bucket                    VARCHAR(20) NULL,          -- ignore|tax|cn
    code                      VARCHAR(32) NULL
  );
END
GO

/* If somehow already a table with old shape, add missing columns */
IF OBJECT_ID('dbo.tiktok_prep', 'U') IS NOT NULL
BEGIN
  IF COL_LENGTH('dbo.tiktok_prep', 'order_sn') IS NULL
    ALTER TABLE dbo.tiktok_prep ADD order_sn VARCHAR(64) NULL;
  IF COL_LENGTH('dbo.tiktok_prep', 'order_date') IS NULL
    ALTER TABLE dbo.tiktok_prep ADD order_date DATETIME NULL;
  IF COL_LENGTH('dbo.tiktok_prep', 'status') IS NULL
    ALTER TABLE dbo.tiktok_prep ADD status NVARCHAR(100) NULL;
  IF COL_LENGTH('dbo.tiktok_prep', 'cancel_type') IS NULL
    ALTER TABLE dbo.tiktok_prep ADD cancel_type NVARCHAR(200) NULL;
  IF COL_LENGTH('dbo.tiktok_prep', 'cancel_reason') IS NULL
    ALTER TABLE dbo.tiktok_prep ADD cancel_reason NVARCHAR(500) NULL;
  IF COL_LENGTH('dbo.tiktok_prep', 'paid_price') IS NULL
    ALTER TABLE dbo.tiktok_prep ADD paid_price DECIMAL(18,2) NULL;
  IF COL_LENGTH('dbo.tiktok_prep', 'cn_paid_price') IS NULL
    ALTER TABLE dbo.tiktok_prep ADD cn_paid_price DECIMAL(18,2) NULL;
  IF COL_LENGTH('dbo.tiktok_prep', 'logistic_price') IS NULL
    ALTER TABLE dbo.tiktok_prep ADD logistic_price DECIMAL(18,2) NULL;
  IF COL_LENGTH('dbo.tiktok_prep', 'shipping_fee') IS NULL
    ALTER TABLE dbo.tiktok_prep ADD shipping_fee DECIMAL(18,2) NULL;
  IF COL_LENGTH('dbo.tiktok_prep', 'unit_price') IS NULL
    ALTER TABLE dbo.tiktok_prep ADD unit_price DECIMAL(18,2) NULL;
  IF COL_LENGTH('dbo.tiktok_prep', 'seller_discount') IS NULL
    ALTER TABLE dbo.tiktok_prep ADD seller_discount DECIMAL(18,2) NULL;
  IF COL_LENGTH('dbo.tiktok_prep', 'subtotal_after_discount') IS NULL
    ALTER TABLE dbo.tiktok_prep ADD subtotal_after_discount DECIMAL(18,2) NULL;
  IF COL_LENGTH('dbo.tiktok_prep', 'order_amount_w') IS NULL
    ALTER TABLE dbo.tiktok_prep ADD order_amount_w DECIMAL(18,2) NULL;
  IF COL_LENGTH('dbo.tiktok_prep', 'taxable') IS NULL
    ALTER TABLE dbo.tiktok_prep ADD taxable DECIMAL(18,2) NULL;
  IF COL_LENGTH('dbo.tiktok_prep', 'bucket') IS NULL
    ALTER TABLE dbo.tiktok_prep ADD bucket VARCHAR(20) NULL;
  IF COL_LENGTH('dbo.tiktok_prep', 'code') IS NULL
    ALTER TABLE dbo.tiktok_prep ADD code VARCHAR(32) NULL;
END
GO

/* ========== 2) tiktok_prep_api : taxable (may already be done — 6928 rows) ========== */

IF COL_LENGTH('dbo.tiktok_prep_api', 'taxable') IS NULL
  ALTER TABLE dbo.tiktok_prep_api ADD taxable DECIMAL(18,2) NULL;

IF COL_LENGTH('dbo.tiktok_prep_api', 'seller_discount') IS NULL
  ALTER TABLE dbo.tiktok_prep_api ADD seller_discount DECIMAL(18,2) NULL;
GO

UPDATE dbo.tiktok_prep_api
SET taxable = ISNULL(
  taxable,
  ISNULL(priceVATincluded, ISNULL(price, 0) - ISNULL(voucher_seller, ISNULL(voucher, 0)))
)
WHERE taxable IS NULL;
GO

/* ========== 3) Check view ========== */

IF OBJECT_ID('dbo.vw_tiktok_prep_check', 'V') IS NOT NULL
  DROP VIEW dbo.vw_tiktok_prep_check;
GO

CREATE VIEW dbo.vw_tiktok_prep_check
AS
SELECT
  e.tiktok_prep_id,
  e.code,
  e.order_sn,
  e.order_date,
  e.status,
  e.cancel_type,
  e.cancel_reason,
  e.bucket,
  CASE WHEN e.bucket IN ('tax','cn') THEN 1 ELSE 0 END AS in_tax,
  CASE WHEN e.bucket = 'cn' THEN 1 ELSE 0 END AS is_cn,
  CASE WHEN e.bucket IN ('tax','cn') THEN ISNULL(e.taxable, e.paid_price) ELSE 0 END AS excel_tax_gross,
  CASE WHEN e.bucket = 'cn' THEN ISNULL(e.taxable, e.paid_price) ELSE 0 END AS excel_cn,
  e.unit_price AS excel_unit_price,
  e.seller_discount AS excel_seller_discount,
  e.shipping_fee AS excel_ship_q,
  e.subtotal_after_discount AS excel_subtotal_p,
  e.order_amount_w AS excel_order_amount_w,
  e.taxable AS excel_taxable,
  e.paid_price AS paid_price_legacy,
  a.transactiondate,
  a.price AS api_price,
  a.voucher_seller AS api_voucher_seller,
  a.voucher_platform AS api_voucher_platform,
  a.voucher AS api_voucher,
  a.shipping_fee AS api_shipping_fee,
  a.priceVATincluded AS api_priceVATincluded,
  a.taxable AS api_taxable,
  (
    ISNULL(e.taxable, e.paid_price)
    - ISNULL(
        a.taxable,
        ISNULL(a.priceVATincluded, ISNULL(a.price, 0) - ISNULL(a.voucher_seller, ISNULL(a.voucher, 0)))
      )
  ) AS diff_taxable
FROM dbo.tiktok_prep e
LEFT OUTER JOIN dbo.tiktok_prep_api a
  ON e.order_sn = a.order_id
 AND e.code = a.code;
GO

/* ========== 4) Confirm result ========== */
SELECT name, type_desc FROM sys.objects
WHERE name IN ('tiktok_prep', 'tiktok_prep_api', 'vw_tiktok_prep_check')
ORDER BY name;

SELECT COLUMN_NAME
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'tiktok_prep'
ORDER BY ORDINAL_POSITION;
GO
