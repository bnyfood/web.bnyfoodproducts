/*
  Domain-scoped products with full history (materials-style).
  - Live:  web_domain_product          (edit UI only)
  - Hist:  web_domain_product_history  (immutable; never DELETE)
  - View:  web_domain_product_history_lasted  (current tip)
  - Trigger AFTER INSERT, UPDATE on live -> new history row

  ShopID     = tenant (customer renting the software)
  web_domain_id = which website under that shop
  Downstream joins MUST use web_domain_product_history_id, not live id alone.
*/
USE [bnyfoodproducts];
GO

/* ---------- Live table ---------- */
IF OBJECT_ID(N'dbo.web_domain_product', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_product (
    web_domain_product_id INT IDENTITY(1,1) NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    web_domain_category_id INT NULL,
    parent_id INT NOT NULL CONSTRAINT DF_wdp_parent DEFAULT (0),
    Title NVARCHAR(255) NOT NULL,
    Sku NVARCHAR(100) NULL,
    Barcode NVARCHAR(100) NULL,
    Unit NVARCHAR(100) NULL,
    Description NVARCHAR(MAX) NULL,
    thumbnail NVARCHAR(255) NULL,
    Weight FLOAT NULL,
    Dimension NVARCHAR(50) NULL,
    Condition TINYINT NOT NULL CONSTRAINT DF_wdp_condition DEFAULT (1),
    Cost_price FLOAT NULL,
    Price FLOAT NULL,
    OnDiscount TINYINT NULL,
    DiscountType TINYINT NULL,
    DiscountAmountType TINYINT NULL,
    DiscountAmount FLOAT NULL,
    Status TINYINT NOT NULL CONSTRAINT DF_wdp_status DEFAULT (1), /* 1=active, 0=retired (soft) */
    is_visible BIT NOT NULL CONSTRAINT DF_wdp_visible DEFAULT (1),
    is_main_product INT NOT NULL CONSTRAINT DF_wdp_main DEFAULT (1),
    seo_title NVARCHAR(255) NULL,
    seo_description NVARCHAR(MAX) NULL,
    seo_keywords NVARCHAR(500) NULL,
    seo_slug NVARCHAR(255) NULL,
    sort_order INT NOT NULL CONSTRAINT DF_wdp_sort DEFAULT (0),
    cdate DATETIME NOT NULL CONSTRAINT DF_wdp_cdate DEFAULT (GETDATE()),
    updated_at DATETIME NOT NULL CONSTRAINT DF_wdp_updated DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_product PRIMARY KEY CLUSTERED (web_domain_product_id)
  );

  CREATE NONCLUSTERED INDEX IX_wdp_domain
    ON dbo.web_domain_product (web_domain_id, ShopID, parent_id, sort_order);

  CREATE NONCLUSTERED INDEX IX_wdp_category
    ON dbo.web_domain_product (web_domain_category_id);

  CREATE NONCLUSTERED INDEX IX_wdp_sku
    ON dbo.web_domain_product (ShopID, web_domain_id, Sku);
END
GO

/* ---------- History table (forever — never DELETE rows) ---------- */
IF OBJECT_ID(N'dbo.web_domain_product_history', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_product_history (
    web_domain_product_history_id BIGINT IDENTITY(1,1) NOT NULL,
    web_domain_product_id INT NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    web_domain_category_id INT NULL,
    parent_id INT NOT NULL,
    Title NVARCHAR(255) NOT NULL,
    Sku NVARCHAR(100) NULL,
    Barcode NVARCHAR(100) NULL,
    Unit NVARCHAR(100) NULL,
    Description NVARCHAR(MAX) NULL,
    thumbnail NVARCHAR(255) NULL,
    Weight FLOAT NULL,
    Dimension NVARCHAR(50) NULL,
    Condition TINYINT NOT NULL,
    Cost_price FLOAT NULL,
    Price FLOAT NULL,
    OnDiscount TINYINT NULL,
    DiscountType TINYINT NULL,
    DiscountAmountType TINYINT NULL,
    DiscountAmount FLOAT NULL,
    Status TINYINT NOT NULL,
    is_visible BIT NOT NULL,
    is_main_product INT NOT NULL,
    seo_title NVARCHAR(255) NULL,
    seo_description NVARCHAR(MAX) NULL,
    seo_keywords NVARCHAR(500) NULL,
    seo_slug NVARCHAR(255) NULL,
    sort_order INT NOT NULL,
    cdate DATETIME NULL,
    updated_at DATETIME NULL,
    history_cdate DATETIME NOT NULL CONSTRAINT DF_wdph_hcdate DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_product_history PRIMARY KEY CLUSTERED (web_domain_product_history_id)
  );

  CREATE NONCLUSTERED INDEX IX_wdph_product
    ON dbo.web_domain_product_history (web_domain_product_id, web_domain_product_history_id DESC);

  CREATE NONCLUSTERED INDEX IX_wdph_domain
    ON dbo.web_domain_product_history (web_domain_id, ShopID);
END
GO

/* ---------- Current tip view (join here for "live" reads) ---------- */
IF OBJECT_ID(N'dbo.web_domain_product_history_lasted', N'V') IS NOT NULL
  DROP VIEW dbo.web_domain_product_history_lasted;
GO

CREATE VIEW dbo.web_domain_product_history_lasted
AS
SELECT h.*
FROM dbo.web_domain_product_history AS h
WHERE h.web_domain_product_history_id IN (
  SELECT MAX(h2.web_domain_product_history_id)
  FROM dbo.web_domain_product_history AS h2
  GROUP BY h2.web_domain_product_id
);
GO

/* ---------- Trigger: every INSERT / UPDATE -> new history row ---------- */
IF OBJECT_ID(N'dbo.web_domain_product_trigger', N'TR') IS NOT NULL
  DROP TRIGGER dbo.web_domain_product_trigger;
GO

CREATE TRIGGER dbo.web_domain_product_trigger
ON dbo.web_domain_product
AFTER INSERT, UPDATE
AS
BEGIN
  SET NOCOUNT ON;

  /* Soft-retire is an UPDATE (Status=0) — still writes history. Never DELETE history. */
  INSERT INTO dbo.web_domain_product_history (
    web_domain_product_id,
    ShopID,
    web_domain_id,
    web_domain_category_id,
    parent_id,
    Title,
    Sku,
    Barcode,
    Unit,
    Description,
    thumbnail,
    Weight,
    Dimension,
    Condition,
    Cost_price,
    Price,
    OnDiscount,
    DiscountType,
    DiscountAmountType,
    DiscountAmount,
    Status,
    is_visible,
    is_main_product,
    seo_title,
    seo_description,
    seo_keywords,
    seo_slug,
    sort_order,
    cdate,
    updated_at,
    history_cdate
  )
  SELECT
    i.web_domain_product_id,
    i.ShopID,
    i.web_domain_id,
    i.web_domain_category_id,
    i.parent_id,
    i.Title,
    i.Sku,
    i.Barcode,
    i.Unit,
    i.Description,
    i.thumbnail,
    i.Weight,
    i.Dimension,
    i.Condition,
    i.Cost_price,
    i.Price,
    i.OnDiscount,
    i.DiscountType,
    i.DiscountAmountType,
    i.DiscountAmount,
    i.Status,
    i.is_visible,
    i.is_main_product,
    i.seo_title,
    i.seo_description,
    i.seo_keywords,
    i.seo_slug,
    i.sort_order,
    i.cdate,
    i.updated_at,
    GETDATE()
  FROM INSERTED AS i;
END
GO
