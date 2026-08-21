USE [bnyfoodproducts]
GO

/****** Object:  Table [dbo].[web_sku_history] ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[web_sku_history](
	[web_sku_history_id] [bigint] IDENTITY(1,1) NOT NULL,
	[web_sku_id] [bigint] NULL,
	[web_product_id] [bigint] NULL,
	[sku_name] [varchar](255) NOT NULL,
	[sku_value] [int] NULL,
	[temp_key] [varchar](255) NULL,
	[ShopID] [int] NULL,
	[cdate] [datetime] NULL CONSTRAINT [DF_web_sku_history_cdate]  DEFAULT (getdate()),
 CONSTRAINT [PK_web_sku_history] PRIMARY KEY CLUSTERED 
(
	[web_sku_history_id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO

/****** Object:  Trigger [dbo].[web_sku_trigger] ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TRIGGER [dbo].[web_sku_trigger]
   ON  [dbo].[web_sku]
   AFTER INSERT,UPDATE
AS 
BEGIN
	SET NOCOUNT ON;

	INSERT INTO [dbo].[web_sku_history]
		([web_sku_id],[web_product_id],[sku_name],[sku_value],[temp_key],[ShopID],[cdate])
	SELECT
		[web_sku_id],[web_product_id],[sku_name],[sku_value],[temp_key],[ShopID],getdate()
	FROM INSERTED;
END
GO

/****** Object:  View [dbo].[web_sku_history_lasted] ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE VIEW [dbo].[web_sku_history_lasted]
AS
SELECT        web_sku_history_id, web_sku_id, web_product_id, sku_name, sku_value, temp_key, ShopID, cdate
FROM            dbo.web_sku_history
WHERE        (web_sku_history_id IN
                             (SELECT        MAX(web_sku_history_id) AS web_sku_history_id
                               FROM            dbo.web_sku_history AS web_sku_history_1
                               GROUP BY web_sku_id))
GO
