/*
  Unified catalog for BNY factory BOM + SaaS storefront (same schema from day one).
  Naming: web_domain_*
  ShopID = SaaS tenant; web_domain_id = website under tenant.
  History: AFTER INSERT,UPDATE triggers; joins use *_history_id (never live alone).

  Layers:
    A) Atomic / component     -> web_domain_product (+ packing dims / max load)
    B) Bundle / nested BOM    -> web_domain_sku + web_domain_sku_map_product
                               (child = product OR nested sku)
    C) SaaS listing + 2D opts -> web_domain_salable_product + option dims/values
                               + web_domain_product_variant (-> sku for fulfillment)
*/
USE [bnyfoodproducts];
GO

/* =====================================================================
   A) ATOMIC PRODUCT — packing / load / flags
   ===================================================================== */
IF COL_LENGTH(N'dbo.web_domain_product', N'width_cm') IS NULL
  ALTER TABLE dbo.web_domain_product ADD width_cm FLOAT NULL;
IF COL_LENGTH(N'dbo.web_domain_product', N'length_cm') IS NULL
  ALTER TABLE dbo.web_domain_product ADD length_cm FLOAT NULL;
IF COL_LENGTH(N'dbo.web_domain_product', N'height_cm') IS NULL
  ALTER TABLE dbo.web_domain_product ADD height_cm FLOAT NULL;
IF COL_LENGTH(N'dbo.web_domain_product', N'weight_g') IS NULL
  ALTER TABLE dbo.web_domain_product ADD weight_g FLOAT NULL;
IF COL_LENGTH(N'dbo.web_domain_product', N'max_load_axis_x_g') IS NULL
  ALTER TABLE dbo.web_domain_product ADD max_load_axis_x_g FLOAT NULL;
IF COL_LENGTH(N'dbo.web_domain_product', N'max_load_axis_y_g') IS NULL
  ALTER TABLE dbo.web_domain_product ADD max_load_axis_y_g FLOAT NULL;
IF COL_LENGTH(N'dbo.web_domain_product', N'max_load_axis_z_g') IS NULL
  ALTER TABLE dbo.web_domain_product ADD max_load_axis_z_g FLOAT NULL;
IF COL_LENGTH(N'dbo.web_domain_product', N'is_atomic') IS NULL
  ALTER TABLE dbo.web_domain_product ADD is_atomic BIT NOT NULL CONSTRAINT DF_wdp_atomic DEFAULT (1);
IF COL_LENGTH(N'dbo.web_domain_product', N'is_salable') IS NULL
  ALTER TABLE dbo.web_domain_product ADD is_salable BIT NOT NULL CONSTRAINT DF_wdp_salable DEFAULT (1);
IF COL_LENGTH(N'dbo.web_domain_product', N'image_config_json') IS NULL
  ALTER TABLE dbo.web_domain_product ADD image_config_json NVARCHAR(MAX) NULL;
GO

IF COL_LENGTH(N'dbo.web_domain_product_history', N'width_cm') IS NULL
  ALTER TABLE dbo.web_domain_product_history ADD width_cm FLOAT NULL;
IF COL_LENGTH(N'dbo.web_domain_product_history', N'length_cm') IS NULL
  ALTER TABLE dbo.web_domain_product_history ADD length_cm FLOAT NULL;
IF COL_LENGTH(N'dbo.web_domain_product_history', N'height_cm') IS NULL
  ALTER TABLE dbo.web_domain_product_history ADD height_cm FLOAT NULL;
IF COL_LENGTH(N'dbo.web_domain_product_history', N'weight_g') IS NULL
  ALTER TABLE dbo.web_domain_product_history ADD weight_g FLOAT NULL;
IF COL_LENGTH(N'dbo.web_domain_product_history', N'max_load_axis_x_g') IS NULL
  ALTER TABLE dbo.web_domain_product_history ADD max_load_axis_x_g FLOAT NULL;
IF COL_LENGTH(N'dbo.web_domain_product_history', N'max_load_axis_y_g') IS NULL
  ALTER TABLE dbo.web_domain_product_history ADD max_load_axis_y_g FLOAT NULL;
IF COL_LENGTH(N'dbo.web_domain_product_history', N'max_load_axis_z_g') IS NULL
  ALTER TABLE dbo.web_domain_product_history ADD max_load_axis_z_g FLOAT NULL;
IF COL_LENGTH(N'dbo.web_domain_product_history', N'is_atomic') IS NULL
  ALTER TABLE dbo.web_domain_product_history ADD is_atomic BIT NULL;
IF COL_LENGTH(N'dbo.web_domain_product_history', N'is_salable') IS NULL
  ALTER TABLE dbo.web_domain_product_history ADD is_salable BIT NULL;
IF COL_LENGTH(N'dbo.web_domain_product_history', N'image_config_json') IS NULL
  ALTER TABLE dbo.web_domain_product_history ADD image_config_json NVARCHAR(MAX) NULL;
GO

IF OBJECT_ID(N'dbo.web_domain_product_trigger', N'TR') IS NOT NULL
  DROP TRIGGER dbo.web_domain_product_trigger;
GO
CREATE TRIGGER dbo.web_domain_product_trigger
ON dbo.web_domain_product AFTER INSERT, UPDATE AS
BEGIN
  SET NOCOUNT ON;
  INSERT INTO dbo.web_domain_product_history (
    web_domain_product_id, ShopID, web_domain_id, web_domain_category_id, parent_id,
    Title, Sku, Barcode, Unit, Description, thumbnail,
    Weight, Dimension, Condition, Cost_price, Price,
    OnDiscount, DiscountType, DiscountAmountType, DiscountAmount,
    Status, is_visible, is_main_product,
    seo_title, seo_description, seo_keywords, seo_slug, sort_order,
    cdate, updated_at,
    width_cm, length_cm, height_cm, weight_g,
    max_load_axis_x_g, max_load_axis_y_g, max_load_axis_z_g,
    is_atomic, is_salable, image_config_json, history_cdate
  )
  SELECT
    i.web_domain_product_id, i.ShopID, i.web_domain_id, i.web_domain_category_id, i.parent_id,
    i.Title, i.Sku, i.Barcode, i.Unit, i.Description, i.thumbnail,
    i.Weight, i.Dimension, i.Condition, i.Cost_price, i.Price,
    i.OnDiscount, i.DiscountType, i.DiscountAmountType, i.DiscountAmount,
    i.Status, i.is_visible, i.is_main_product,
    i.seo_title, i.seo_description, i.seo_keywords, i.seo_slug, i.sort_order,
    i.cdate, i.updated_at,
    i.width_cm, i.length_cm, i.height_cm, i.weight_g,
    i.max_load_axis_x_g, i.max_load_axis_y_g, i.max_load_axis_z_g,
    i.is_atomic, i.is_salable, i.image_config_json, GETDATE()
  FROM INSERTED i;
END
GO

IF OBJECT_ID(N'dbo.web_domain_product_history_lasted', N'V') IS NOT NULL
  DROP VIEW dbo.web_domain_product_history_lasted;
GO
CREATE VIEW dbo.web_domain_product_history_lasted AS
SELECT h.* FROM dbo.web_domain_product_history h
WHERE h.web_domain_product_history_id IN (
  SELECT MAX(h2.web_domain_product_history_id)
  FROM dbo.web_domain_product_history h2
  GROUP BY h2.web_domain_product_id
);
GO

/* =====================================================================
   B) SKU (sellable / intermediate nested BOM node)
   ===================================================================== */
IF COL_LENGTH(N'dbo.web_domain_sku', N'Title') IS NULL
  ALTER TABLE dbo.web_domain_sku ADD Title NVARCHAR(255) NULL;
IF COL_LENGTH(N'dbo.web_domain_sku', N'Description') IS NULL
  ALTER TABLE dbo.web_domain_sku ADD Description NVARCHAR(MAX) NULL;
IF COL_LENGTH(N'dbo.web_domain_sku', N'thumbnail') IS NULL
  ALTER TABLE dbo.web_domain_sku ADD thumbnail NVARCHAR(255) NULL;
IF COL_LENGTH(N'dbo.web_domain_sku', N'Price') IS NULL
  ALTER TABLE dbo.web_domain_sku ADD Price FLOAT NULL;
IF COL_LENGTH(N'dbo.web_domain_sku', N'Cost_price') IS NULL
  ALTER TABLE dbo.web_domain_sku ADD Cost_price FLOAT NULL;
IF COL_LENGTH(N'dbo.web_domain_sku', N'is_salable') IS NULL
  ALTER TABLE dbo.web_domain_sku ADD is_salable BIT NOT NULL CONSTRAINT DF_wdsku_salable DEFAULT (1);
IF COL_LENGTH(N'dbo.web_domain_sku', N'protocol_string') IS NULL
  ALTER TABLE dbo.web_domain_sku ADD protocol_string NVARCHAR(500) NULL; /* e.g. (S300*1,P15*2)*3 */
IF COL_LENGTH(N'dbo.web_domain_sku', N'created_by') IS NULL
  ALTER TABLE dbo.web_domain_sku ADD created_by NVARCHAR(20) NULL; /* ADMIN | AI */
IF COL_LENGTH(N'dbo.web_domain_sku', N'seo_title') IS NULL
  ALTER TABLE dbo.web_domain_sku ADD seo_title NVARCHAR(255) NULL;
IF COL_LENGTH(N'dbo.web_domain_sku', N'seo_description') IS NULL
  ALTER TABLE dbo.web_domain_sku ADD seo_description NVARCHAR(MAX) NULL;
IF COL_LENGTH(N'dbo.web_domain_sku', N'seo_keywords') IS NULL
  ALTER TABLE dbo.web_domain_sku ADD seo_keywords NVARCHAR(500) NULL;
IF COL_LENGTH(N'dbo.web_domain_sku', N'seo_slug') IS NULL
  ALTER TABLE dbo.web_domain_sku ADD seo_slug NVARCHAR(255) NULL;
IF COL_LENGTH(N'dbo.web_domain_sku', N'image_config_json') IS NULL
  ALTER TABLE dbo.web_domain_sku ADD image_config_json NVARCHAR(MAX) NULL;
IF COL_LENGTH(N'dbo.web_domain_sku', N'web_domain_category_id') IS NULL
  ALTER TABLE dbo.web_domain_sku ADD web_domain_category_id INT NULL;
GO

IF COL_LENGTH(N'dbo.web_domain_sku_history', N'Title') IS NULL
  ALTER TABLE dbo.web_domain_sku_history ADD Title NVARCHAR(255) NULL;
IF COL_LENGTH(N'dbo.web_domain_sku_history', N'Description') IS NULL
  ALTER TABLE dbo.web_domain_sku_history ADD Description NVARCHAR(MAX) NULL;
IF COL_LENGTH(N'dbo.web_domain_sku_history', N'thumbnail') IS NULL
  ALTER TABLE dbo.web_domain_sku_history ADD thumbnail NVARCHAR(255) NULL;
IF COL_LENGTH(N'dbo.web_domain_sku_history', N'Price') IS NULL
  ALTER TABLE dbo.web_domain_sku_history ADD Price FLOAT NULL;
IF COL_LENGTH(N'dbo.web_domain_sku_history', N'Cost_price') IS NULL
  ALTER TABLE dbo.web_domain_sku_history ADD Cost_price FLOAT NULL;
IF COL_LENGTH(N'dbo.web_domain_sku_history', N'is_salable') IS NULL
  ALTER TABLE dbo.web_domain_sku_history ADD is_salable BIT NULL;
IF COL_LENGTH(N'dbo.web_domain_sku_history', N'protocol_string') IS NULL
  ALTER TABLE dbo.web_domain_sku_history ADD protocol_string NVARCHAR(500) NULL;
IF COL_LENGTH(N'dbo.web_domain_sku_history', N'created_by') IS NULL
  ALTER TABLE dbo.web_domain_sku_history ADD created_by NVARCHAR(20) NULL;
IF COL_LENGTH(N'dbo.web_domain_sku_history', N'seo_title') IS NULL
  ALTER TABLE dbo.web_domain_sku_history ADD seo_title NVARCHAR(255) NULL;
IF COL_LENGTH(N'dbo.web_domain_sku_history', N'seo_description') IS NULL
  ALTER TABLE dbo.web_domain_sku_history ADD seo_description NVARCHAR(MAX) NULL;
IF COL_LENGTH(N'dbo.web_domain_sku_history', N'seo_keywords') IS NULL
  ALTER TABLE dbo.web_domain_sku_history ADD seo_keywords NVARCHAR(500) NULL;
IF COL_LENGTH(N'dbo.web_domain_sku_history', N'seo_slug') IS NULL
  ALTER TABLE dbo.web_domain_sku_history ADD seo_slug NVARCHAR(255) NULL;
IF COL_LENGTH(N'dbo.web_domain_sku_history', N'image_config_json') IS NULL
  ALTER TABLE dbo.web_domain_sku_history ADD image_config_json NVARCHAR(MAX) NULL;
IF COL_LENGTH(N'dbo.web_domain_sku_history', N'web_domain_category_id') IS NULL
  ALTER TABLE dbo.web_domain_sku_history ADD web_domain_category_id INT NULL;
GO

IF OBJECT_ID(N'dbo.web_domain_sku_trigger', N'TR') IS NOT NULL
  DROP TRIGGER dbo.web_domain_sku_trigger;
GO
CREATE TRIGGER dbo.web_domain_sku_trigger
ON dbo.web_domain_sku AFTER INSERT, UPDATE AS
BEGIN
  SET NOCOUNT ON;
  INSERT INTO dbo.web_domain_sku_history (
    web_domain_sku_id, ShopID, web_domain_id, web_domain_product_id, web_domain_product_history_id,
    sku_name, sku_value, temp_key, Status, cdate, updated_at,
    Title, Description, thumbnail, Price, Cost_price, is_salable, protocol_string, created_by,
    seo_title, seo_description, seo_keywords, seo_slug, image_config_json, web_domain_category_id,
    history_cdate
  )
  SELECT
    i.web_domain_sku_id, i.ShopID, i.web_domain_id, i.web_domain_product_id,
    CASE WHEN i.web_domain_product_id IS NULL THEN NULL ELSE (
      SELECT TOP 1 l.web_domain_product_history_id FROM dbo.web_domain_product_history_lasted l
      WHERE l.web_domain_product_id = i.web_domain_product_id
    ) END,
    i.sku_name, i.sku_value, i.temp_key, i.Status, i.cdate, i.updated_at,
    i.Title, i.Description, i.thumbnail, i.Price, i.Cost_price, i.is_salable, i.protocol_string, i.created_by,
    i.seo_title, i.seo_description, i.seo_keywords, i.seo_slug, i.image_config_json, i.web_domain_category_id,
    GETDATE()
  FROM INSERTED i;
END
GO

IF OBJECT_ID(N'dbo.web_domain_sku_history_lasted', N'V') IS NOT NULL
  DROP VIEW dbo.web_domain_sku_history_lasted;
GO
CREATE VIEW dbo.web_domain_sku_history_lasted AS
SELECT h.* FROM dbo.web_domain_sku_history h
WHERE h.web_domain_sku_history_id IN (
  SELECT MAX(h2.web_domain_sku_history_id)
  FROM dbo.web_domain_sku_history h2
  GROUP BY h2.web_domain_sku_id
);
GO

/* Nested BOM: child may be atomic product OR another sku */
IF COL_LENGTH(N'dbo.web_domain_sku_map_product', N'child_sku_id') IS NULL
  ALTER TABLE dbo.web_domain_sku_map_product ADD child_sku_id INT NULL;
IF COL_LENGTH(N'dbo.web_domain_sku_map_product', N'child_sku_history_id') IS NULL
  ALTER TABLE dbo.web_domain_sku_map_product ADD child_sku_history_id BIGINT NULL;
GO
/* Allow atomic-only OR sku-only child rows */
IF EXISTS (
  SELECT 1 FROM sys.columns
  WHERE object_id = OBJECT_ID(N'dbo.web_domain_sku_map_product') AND name = N'web_domain_product_id' AND is_nullable = 0
)
  ALTER TABLE dbo.web_domain_sku_map_product ALTER COLUMN web_domain_product_id INT NULL;
GO

IF COL_LENGTH(N'dbo.web_domain_sku_map_product_history', N'child_sku_id') IS NULL
  ALTER TABLE dbo.web_domain_sku_map_product_history ADD child_sku_id INT NULL;
IF COL_LENGTH(N'dbo.web_domain_sku_map_product_history', N'child_sku_history_id') IS NULL
  ALTER TABLE dbo.web_domain_sku_map_product_history ADD child_sku_history_id BIGINT NULL;
GO
IF EXISTS (
  SELECT 1 FROM sys.columns
  WHERE object_id = OBJECT_ID(N'dbo.web_domain_sku_map_product_history') AND name = N'web_domain_product_id' AND is_nullable = 0
)
  ALTER TABLE dbo.web_domain_sku_map_product_history ALTER COLUMN web_domain_product_id INT NULL;
GO

IF OBJECT_ID(N'dbo.web_domain_sku_map_product_trigger', N'TR') IS NOT NULL
  DROP TRIGGER dbo.web_domain_sku_map_product_trigger;
GO
CREATE TRIGGER dbo.web_domain_sku_map_product_trigger
ON dbo.web_domain_sku_map_product AFTER INSERT, UPDATE AS
BEGIN
  SET NOCOUNT ON;
  INSERT INTO dbo.web_domain_sku_map_product_history (
    web_domain_sku_map_product_id, ShopID, web_domain_id,
    web_domain_sku_id, web_domain_sku_history_id,
    web_domain_product_id, web_domain_product_history_id,
    child_sku_id, child_sku_history_id,
    quantity, cdate, updated_at, history_cdate
  )
  SELECT
    i.web_domain_sku_map_product_id, i.ShopID, i.web_domain_id,
    i.web_domain_sku_id,
    (SELECT TOP 1 s.web_domain_sku_history_id FROM dbo.web_domain_sku_history_lasted s WHERE s.web_domain_sku_id = i.web_domain_sku_id),
    i.web_domain_product_id,
    CASE WHEN i.web_domain_product_id IS NULL THEN NULL ELSE ISNULL(i.web_domain_product_history_id, (
      SELECT TOP 1 l.web_domain_product_history_id FROM dbo.web_domain_product_history_lasted l
      WHERE l.web_domain_product_id = i.web_domain_product_id
    )) END,
    i.child_sku_id,
    CASE WHEN i.child_sku_id IS NULL THEN NULL ELSE ISNULL(i.child_sku_history_id, (
      SELECT TOP 1 s2.web_domain_sku_history_id FROM dbo.web_domain_sku_history_lasted s2
      WHERE s2.web_domain_sku_id = i.child_sku_id
    )) END,
    i.quantity, i.cdate, i.updated_at, GETDATE()
  FROM INSERTED i;
END
GO

IF OBJECT_ID(N'dbo.web_domain_sku_map_product_history_lasted', N'V') IS NOT NULL
  DROP VIEW dbo.web_domain_sku_map_product_history_lasted;
GO
CREATE VIEW dbo.web_domain_sku_map_product_history_lasted AS
SELECT h.* FROM dbo.web_domain_sku_map_product_history h
WHERE h.web_domain_sku_map_product_history_id IN (
  SELECT MAX(h2.web_domain_sku_map_product_history_id)
  FROM dbo.web_domain_sku_map_product_history h2
  GROUP BY h2.web_domain_sku_map_product_id
);
GO

/* =====================================================================
   C) SAAS STOREFRONT — listing + 2D variants (links to SKU/BOM)
   ===================================================================== */
IF OBJECT_ID(N'dbo.web_domain_salable_product', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_salable_product (
    web_domain_salable_product_id INT IDENTITY(1,1) NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    web_domain_category_id INT NULL,
    product_code NVARCHAR(50) NOT NULL,
    Title NVARCHAR(255) NOT NULL,
    Description NVARCHAR(MAX) NULL,
    thumbnail NVARCHAR(255) NULL,
    has_variants BIT NOT NULL CONSTRAINT DF_wdsp_hasvar DEFAULT (0),
    is_salable BIT NOT NULL CONSTRAINT DF_wdsp_salable DEFAULT (1),
    is_visible BIT NOT NULL CONSTRAINT DF_wdsp_vis DEFAULT (1),
    Status TINYINT NOT NULL CONSTRAINT DF_wdsp_status DEFAULT (1),
    seo_title NVARCHAR(255) NULL,
    seo_description NVARCHAR(MAX) NULL,
    seo_keywords NVARCHAR(500) NULL,
    seo_slug NVARCHAR(255) NULL,
    image_config_json NVARCHAR(MAX) NULL,
    sort_order INT NOT NULL CONSTRAINT DF_wdsp_sort DEFAULT (0),
    cdate DATETIME NOT NULL CONSTRAINT DF_wdsp_cdate DEFAULT (GETDATE()),
    updated_at DATETIME NOT NULL CONSTRAINT DF_wdsp_upd DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_salable_product PRIMARY KEY CLUSTERED (web_domain_salable_product_id)
  );
  CREATE UNIQUE NONCLUSTERED INDEX UX_wdsp_code
    ON dbo.web_domain_salable_product (ShopID, web_domain_id, product_code);
END
GO

IF OBJECT_ID(N'dbo.web_domain_salable_product_history', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_salable_product_history (
    web_domain_salable_product_history_id BIGINT IDENTITY(1,1) NOT NULL,
    web_domain_salable_product_id INT NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    web_domain_category_id INT NULL,
    product_code NVARCHAR(50) NOT NULL,
    Title NVARCHAR(255) NOT NULL,
    Description NVARCHAR(MAX) NULL,
    thumbnail NVARCHAR(255) NULL,
    has_variants BIT NOT NULL,
    is_salable BIT NOT NULL,
    is_visible BIT NOT NULL,
    Status TINYINT NOT NULL,
    seo_title NVARCHAR(255) NULL,
    seo_description NVARCHAR(MAX) NULL,
    seo_keywords NVARCHAR(500) NULL,
    seo_slug NVARCHAR(255) NULL,
    image_config_json NVARCHAR(MAX) NULL,
    sort_order INT NOT NULL,
    cdate DATETIME NULL,
    updated_at DATETIME NULL,
    history_cdate DATETIME NOT NULL CONSTRAINT DF_wdsph_hc DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_salable_product_history PRIMARY KEY CLUSTERED (web_domain_salable_product_history_id)
  );
  CREATE NONCLUSTERED INDEX IX_wdsph_p
    ON dbo.web_domain_salable_product_history (web_domain_salable_product_id, web_domain_salable_product_history_id DESC);
END
GO

IF OBJECT_ID(N'dbo.web_domain_salable_product_history_lasted', N'V') IS NOT NULL
  DROP VIEW dbo.web_domain_salable_product_history_lasted;
GO
CREATE VIEW dbo.web_domain_salable_product_history_lasted AS
SELECT h.* FROM dbo.web_domain_salable_product_history h
WHERE h.web_domain_salable_product_history_id IN (
  SELECT MAX(h2.web_domain_salable_product_history_id)
  FROM dbo.web_domain_salable_product_history h2
  GROUP BY h2.web_domain_salable_product_id
);
GO

IF OBJECT_ID(N'dbo.web_domain_salable_product_trigger', N'TR') IS NOT NULL
  DROP TRIGGER dbo.web_domain_salable_product_trigger;
GO
CREATE TRIGGER dbo.web_domain_salable_product_trigger
ON dbo.web_domain_salable_product AFTER INSERT, UPDATE AS
BEGIN
  SET NOCOUNT ON;
  INSERT INTO dbo.web_domain_salable_product_history (
    web_domain_salable_product_id, ShopID, web_domain_id, web_domain_category_id, product_code,
    Title, Description, thumbnail, has_variants, is_salable, is_visible, Status,
    seo_title, seo_description, seo_keywords, seo_slug, image_config_json, sort_order,
    cdate, updated_at, history_cdate
  )
  SELECT
    i.web_domain_salable_product_id, i.ShopID, i.web_domain_id, i.web_domain_category_id, i.product_code,
    i.Title, i.Description, i.thumbnail, i.has_variants, i.is_salable, i.is_visible, i.Status,
    i.seo_title, i.seo_description, i.seo_keywords, i.seo_slug, i.image_config_json, i.sort_order,
    i.cdate, i.updated_at, GETDATE()
  FROM INSERTED i;
END
GO

/* Option dimensions (1 = thumbnails, 2 = text/size typically) */
IF OBJECT_ID(N'dbo.web_domain_product_option_dimension', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_product_option_dimension (
    web_domain_product_option_dimension_id INT IDENTITY(1,1) NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    web_domain_salable_product_id INT NOT NULL,
    dimension_name NVARCHAR(100) NOT NULL,
    dimension_position INT NOT NULL CONSTRAINT DF_wdpod_pos DEFAULT (1),
    cdate DATETIME NOT NULL CONSTRAINT DF_wdpod_cdate DEFAULT (GETDATE()),
    updated_at DATETIME NOT NULL CONSTRAINT DF_wdpod_upd DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_product_option_dimension PRIMARY KEY CLUSTERED (web_domain_product_option_dimension_id)
  );
  CREATE NONCLUSTERED INDEX IX_wdpod_sp
    ON dbo.web_domain_product_option_dimension (web_domain_salable_product_id, dimension_position);
END
GO

IF OBJECT_ID(N'dbo.web_domain_product_option_dimension_history', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_product_option_dimension_history (
    web_domain_product_option_dimension_history_id BIGINT IDENTITY(1,1) NOT NULL,
    web_domain_product_option_dimension_id INT NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    web_domain_salable_product_id INT NOT NULL,
    dimension_name NVARCHAR(100) NOT NULL,
    dimension_position INT NOT NULL,
    cdate DATETIME NULL,
    updated_at DATETIME NULL,
    history_cdate DATETIME NOT NULL CONSTRAINT DF_wdpodh_hc DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_product_option_dimension_history PRIMARY KEY CLUSTERED (web_domain_product_option_dimension_history_id)
  );
END
GO

IF OBJECT_ID(N'dbo.web_domain_product_option_dimension_history_lasted', N'V') IS NOT NULL
  DROP VIEW dbo.web_domain_product_option_dimension_history_lasted;
GO
CREATE VIEW dbo.web_domain_product_option_dimension_history_lasted AS
SELECT h.* FROM dbo.web_domain_product_option_dimension_history h
WHERE h.web_domain_product_option_dimension_history_id IN (
  SELECT MAX(h2.web_domain_product_option_dimension_history_id)
  FROM dbo.web_domain_product_option_dimension_history h2
  GROUP BY h2.web_domain_product_option_dimension_id
);
GO

IF OBJECT_ID(N'dbo.web_domain_product_option_dimension_trigger', N'TR') IS NOT NULL
  DROP TRIGGER dbo.web_domain_product_option_dimension_trigger;
GO
CREATE TRIGGER dbo.web_domain_product_option_dimension_trigger
ON dbo.web_domain_product_option_dimension AFTER INSERT, UPDATE AS
BEGIN
  SET NOCOUNT ON;
  INSERT INTO dbo.web_domain_product_option_dimension_history (
    web_domain_product_option_dimension_id, ShopID, web_domain_id, web_domain_salable_product_id,
    dimension_name, dimension_position, cdate, updated_at, history_cdate
  )
  SELECT i.web_domain_product_option_dimension_id, i.ShopID, i.web_domain_id, i.web_domain_salable_product_id,
         i.dimension_name, i.dimension_position, i.cdate, i.updated_at, GETDATE()
  FROM INSERTED i;
END
GO

IF OBJECT_ID(N'dbo.web_domain_product_option_value', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_product_option_value (
    web_domain_product_option_value_id INT IDENTITY(1,1) NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    web_domain_product_option_dimension_id INT NOT NULL,
    value_name NVARCHAR(100) NOT NULL,
    thumbnail NVARCHAR(255) NULL,
    display_order INT NOT NULL CONSTRAINT DF_wdpov_ord DEFAULT (0),
    Status TINYINT NOT NULL CONSTRAINT DF_wdpov_status DEFAULT (1),
    cdate DATETIME NOT NULL CONSTRAINT DF_wdpov_cdate DEFAULT (GETDATE()),
    updated_at DATETIME NOT NULL CONSTRAINT DF_wdpov_upd DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_product_option_value PRIMARY KEY CLUSTERED (web_domain_product_option_value_id)
  );
END
GO

IF OBJECT_ID(N'dbo.web_domain_product_option_value_history', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_product_option_value_history (
    web_domain_product_option_value_history_id BIGINT IDENTITY(1,1) NOT NULL,
    web_domain_product_option_value_id INT NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    web_domain_product_option_dimension_id INT NOT NULL,
    value_name NVARCHAR(100) NOT NULL,
    thumbnail NVARCHAR(255) NULL,
    display_order INT NOT NULL,
    Status TINYINT NOT NULL,
    cdate DATETIME NULL,
    updated_at DATETIME NULL,
    history_cdate DATETIME NOT NULL CONSTRAINT DF_wdpovh_hc DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_product_option_value_history PRIMARY KEY CLUSTERED (web_domain_product_option_value_history_id)
  );
END
GO

IF OBJECT_ID(N'dbo.web_domain_product_option_value_history_lasted', N'V') IS NOT NULL
  DROP VIEW dbo.web_domain_product_option_value_history_lasted;
GO
CREATE VIEW dbo.web_domain_product_option_value_history_lasted AS
SELECT h.* FROM dbo.web_domain_product_option_value_history h
WHERE h.web_domain_product_option_value_history_id IN (
  SELECT MAX(h2.web_domain_product_option_value_history_id)
  FROM dbo.web_domain_product_option_value_history h2
  GROUP BY h2.web_domain_product_option_value_id
);
GO

IF OBJECT_ID(N'dbo.web_domain_product_option_value_trigger', N'TR') IS NOT NULL
  DROP TRIGGER dbo.web_domain_product_option_value_trigger;
GO
CREATE TRIGGER dbo.web_domain_product_option_value_trigger
ON dbo.web_domain_product_option_value AFTER INSERT, UPDATE AS
BEGIN
  SET NOCOUNT ON;
  INSERT INTO dbo.web_domain_product_option_value_history (
    web_domain_product_option_value_id, ShopID, web_domain_id, web_domain_product_option_dimension_id,
    value_name, thumbnail, display_order, Status, cdate, updated_at, history_cdate
  )
  SELECT i.web_domain_product_option_value_id, i.ShopID, i.web_domain_id, i.web_domain_product_option_dimension_id,
         i.value_name, i.thumbnail, i.display_order, i.Status, i.cdate, i.updated_at, GETDATE()
  FROM INSERTED i;
END
GO

/* Sellable variant = storefront choice; fulfillment via web_domain_sku (BOM) */
IF OBJECT_ID(N'dbo.web_domain_product_variant', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_product_variant (
    web_domain_product_variant_id INT IDENTITY(1,1) NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    web_domain_salable_product_id INT NOT NULL,
    variant_sku NVARCHAR(100) NOT NULL,
    option_value_id_1 INT NULL,
    option_value_id_2 INT NULL,
    web_domain_sku_id INT NULL, /* BOM / fulfillment SKU */
    Price FLOAT NULL,
    Cost_price FLOAT NULL,
    barcode NVARCHAR(100) NULL,
    is_salable BIT NOT NULL CONSTRAINT DF_wdpv_salable DEFAULT (1),
    Status TINYINT NOT NULL CONSTRAINT DF_wdpv_status DEFAULT (1),
    cdate DATETIME NOT NULL CONSTRAINT DF_wdpv_cdate DEFAULT (GETDATE()),
    updated_at DATETIME NOT NULL CONSTRAINT DF_wdpv_upd DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_product_variant PRIMARY KEY CLUSTERED (web_domain_product_variant_id)
  );
  CREATE UNIQUE NONCLUSTERED INDEX UX_wdpv_sku
    ON dbo.web_domain_product_variant (ShopID, web_domain_id, variant_sku);
  CREATE NONCLUSTERED INDEX IX_wdpv_sp
    ON dbo.web_domain_product_variant (web_domain_salable_product_id);
END
GO

IF OBJECT_ID(N'dbo.web_domain_product_variant_history', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_product_variant_history (
    web_domain_product_variant_history_id BIGINT IDENTITY(1,1) NOT NULL,
    web_domain_product_variant_id INT NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    web_domain_salable_product_id INT NOT NULL,
    web_domain_salable_product_history_id BIGINT NULL,
    variant_sku NVARCHAR(100) NOT NULL,
    option_value_id_1 INT NULL,
    option_value_id_2 INT NULL,
    option_1_name_snapshot NVARCHAR(100) NULL,
    option_1_thumbnail_snapshot NVARCHAR(255) NULL,
    option_2_name_snapshot NVARCHAR(100) NULL,
    web_domain_sku_id INT NULL,
    web_domain_sku_history_id BIGINT NULL,
    Price FLOAT NULL,
    Cost_price FLOAT NULL,
    true_base_cost_snapshot FLOAT NULL,
    barcode NVARCHAR(100) NULL,
    is_salable BIT NOT NULL,
    Status TINYINT NOT NULL,
    cdate DATETIME NULL,
    updated_at DATETIME NULL,
    history_cdate DATETIME NOT NULL CONSTRAINT DF_wdpvh_hc DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_product_variant_history PRIMARY KEY CLUSTERED (web_domain_product_variant_history_id)
  );
  CREATE NONCLUSTERED INDEX IX_wdpvh_v
    ON dbo.web_domain_product_variant_history (web_domain_product_variant_id, web_domain_product_variant_history_id DESC);
END
GO

IF OBJECT_ID(N'dbo.web_domain_product_variant_history_lasted', N'V') IS NOT NULL
  DROP VIEW dbo.web_domain_product_variant_history_lasted;
GO
CREATE VIEW dbo.web_domain_product_variant_history_lasted AS
SELECT h.* FROM dbo.web_domain_product_variant_history h
WHERE h.web_domain_product_variant_history_id IN (
  SELECT MAX(h2.web_domain_product_variant_history_id)
  FROM dbo.web_domain_product_variant_history h2
  GROUP BY h2.web_domain_product_variant_id
);
GO

IF OBJECT_ID(N'dbo.web_domain_product_variant_trigger', N'TR') IS NOT NULL
  DROP TRIGGER dbo.web_domain_product_variant_trigger;
GO
CREATE TRIGGER dbo.web_domain_product_variant_trigger
ON dbo.web_domain_product_variant AFTER INSERT, UPDATE AS
BEGIN
  SET NOCOUNT ON;
  INSERT INTO dbo.web_domain_product_variant_history (
    web_domain_product_variant_id, ShopID, web_domain_id, web_domain_salable_product_id,
    web_domain_salable_product_history_id, variant_sku,
    option_value_id_1, option_value_id_2,
    option_1_name_snapshot, option_1_thumbnail_snapshot, option_2_name_snapshot,
    web_domain_sku_id, web_domain_sku_history_id,
    Price, Cost_price, true_base_cost_snapshot, barcode, is_salable, Status,
    cdate, updated_at, history_cdate
  )
  SELECT
    i.web_domain_product_variant_id, i.ShopID, i.web_domain_id, i.web_domain_salable_product_id,
    (SELECT TOP 1 sp.web_domain_salable_product_history_id FROM dbo.web_domain_salable_product_history_lasted sp
     WHERE sp.web_domain_salable_product_id = i.web_domain_salable_product_id),
    i.variant_sku,
    i.option_value_id_1, i.option_value_id_2,
    (SELECT TOP 1 v1.value_name FROM dbo.web_domain_product_option_value v1 WHERE v1.web_domain_product_option_value_id = i.option_value_id_1),
    (SELECT TOP 1 v1.thumbnail FROM dbo.web_domain_product_option_value v1 WHERE v1.web_domain_product_option_value_id = i.option_value_id_1),
    (SELECT TOP 1 v2.value_name FROM dbo.web_domain_product_option_value v2 WHERE v2.web_domain_product_option_value_id = i.option_value_id_2),
    i.web_domain_sku_id,
    CASE WHEN i.web_domain_sku_id IS NULL THEN NULL ELSE (
      SELECT TOP 1 s.web_domain_sku_history_id FROM dbo.web_domain_sku_history_lasted s
      WHERE s.web_domain_sku_id = i.web_domain_sku_id
    ) END,
    i.Price, i.Cost_price, i.Cost_price, i.barcode, i.is_salable, i.Status,
    i.cdate, i.updated_at, GETDATE()
  FROM INSERTED i;
END
GO

/* Light SaaS tenant API stubs (domain-scoped) — expand later for checkout/e-tax */
IF OBJECT_ID(N'dbo.web_domain_api_key', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_api_key (
    web_domain_api_key_id INT IDENTITY(1,1) NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    client_name NVARCHAR(100) NOT NULL,
    api_key_hash NVARCHAR(255) NOT NULL,
    permissions_json NVARCHAR(MAX) NULL,
    is_active BIT NOT NULL CONSTRAINT DF_wdak_active DEFAULT (1),
    cdate DATETIME NOT NULL CONSTRAINT DF_wdak_cdate DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_api_key PRIMARY KEY CLUSTERED (web_domain_api_key_id)
  );
  CREATE UNIQUE NONCLUSTERED INDEX UX_wdak_hash ON dbo.web_domain_api_key (api_key_hash);
END
GO

IF OBJECT_ID(N'dbo.web_domain_webhook', N'U') IS NULL
BEGIN
  CREATE TABLE dbo.web_domain_webhook (
    web_domain_webhook_id INT IDENTITY(1,1) NOT NULL,
    ShopID INT NOT NULL,
    web_domain_id INT NOT NULL,
    target_url NVARCHAR(500) NOT NULL,
    event_type NVARCHAR(100) NOT NULL,
    secret_token NVARCHAR(255) NULL,
    is_active BIT NOT NULL CONSTRAINT DF_wdwh_active DEFAULT (1),
    cdate DATETIME NOT NULL CONSTRAINT DF_wdwh_cdate DEFAULT (GETDATE()),
    CONSTRAINT PK_web_domain_webhook PRIMARY KEY CLUSTERED (web_domain_webhook_id)
  );
END
GO
