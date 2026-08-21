# Windows Task Scheduler on 192.168.1.252 (VM WEB)

Controller jobs = `curl` to `https://www.bnyfoodproducts.com/...`

Disabled for DB work. Re-enable later with **Enable** (do not Delete).

## Already disabled before this stop (do not Enable on restore)

- `get_finance_transaction`
- `get_finance_transaction_more`

## Disable now / Enable later

- `BNY Reward Random`
- `create_textinvoiceid_biggrill`
- `create_textinvoiceid_lazada`
- `create_textinvoiceid_shopee`
- `create_textinvoiceid_tiktok`
- `create_update_textinvoiceid_shopee`
- `lazada_get_order`
- `lazada_get_order_items`
- `lazada_get_order_step2`
- `lazada_get_payout`
- `lazada_get_tracking`
- `lazada_get_tracking_more`
- `refresh_lazada_token`
- `refresh_shopee_token`
- `refresh_tiktok_token`
- `Shopee_getorderlist_recheck`
- `Shopee_getReturnList_return_step1`
- `shopee_get_orderlist`
- `shopee_get_orders_and_orderitems_and_address`
- `shopee_get_order_step2`
- `shopee_get_tracking`
- `Shopee_make_order_return_step2`
- `shopp_get_tracking_more`
- `Tiktok_get_orders`
- `Tiktok_Order_More`

## Leave running

- `SQL_service_restart`
- `DefenderSignatureUpdate_Every2Hours`
- `DefenderStorageQuickScan_0200`
- `User_Feed_Synchronization-...`
