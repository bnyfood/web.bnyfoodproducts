USE [bnyfoodproducts]
GO

IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'web_bny_gift')
BEGIN
  CREATE TABLE [dbo].[web_bny_gift](
    [web_bny_gift_id] [bigint] IDENTITY(1,1) NOT NULL,
    [web_bny_gift_pic] [varchar](255) NULL,
    [web_bny_gift_detail] [text] NULL,
    [web_bny_gift_now] [int] NULL CONSTRAINT [DF_web_bny_gift_now] DEFAULT (0),
    CONSTRAINT [PK_web_bny_gift] PRIMARY KEY CLUSTERED ([web_bny_gift_id] ASC)
  );
END
GO

-- หลังสร้างตารางแล้ว รัน web_bny_gift_temporal.sql เพื่อเปิด SYSTEM_VERSIONING (history)
