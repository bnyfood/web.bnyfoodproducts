# Accounting report cutover (≤ 2026-06-30 = legacy)

## Rule
- Report window **end date ≤ 2026-06-30** → `report_legacy` / legacy decision framework
- End date **after 2026-06-30** → current framework
- UI/UX stays current; data screening + money formulas switch by period

## What changes in legacy mode
| Area | Shopee | TikTok |
|------|--------|--------|
| Sale screening | No PHP `apply_passed_pack_report_filter` | Same |
| Sale money | Use SP `priceVATincluded` (grouper keeps first-row SP) | Remap to pre-fix: `price=total_amount`, `priceVATincluded=total−(platform+seller)` |
| CN source | SP `…_DateStart_DateEnd_CN` + `make_group_cn_legacy` | Still SP CN (unchanged path) |
| CN money | `ValueBeforeVAT` / legacy grouper | `cn_tax_amounts` prefers `ValueBeforeVAT` when legacy |

## Key files
- `application/libraries/util/report_cutover.php`
- Controllers: `saletaxreport.php`, `creditreport.php`
- Models: `shopee_orders_model.php`, `tiktok_orders_model.php`
- BL: `shopee_report_sale.php`, `shopee_report_cn.php` (`make_group_cn_legacy`)
- Views: saletaxreport shopee/tiktok (+ more); CN via `order_util::cn_tax_amounts`

## Out of scope this commit
- Lazada cutover (not required for June PDF focus)
- Changing live download virtual-pack rules (ingest stays current; report period switches)
