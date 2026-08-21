# BNY Food — Sandbox

พื้นที่ทดลองสำหรับงานใหม่ (Data Ingestion & Legal Reconciliation)  
**ไม่ใช่ production** — โค้ด/SQL ที่นี่ยังไม่มีผลกับระบบจริงจนกว่าจะโปรโมท

## โครงสร้าง

```
sandbox/
  docs/           แผน สถาปัตยกรรม มติที่ตกลง
  sql/            schema / query ทดลอง (ยังไม่รันบน DB จริง)
  php/            webhook, worker, parser ร่าง
  samples/        ไฟล์ Excel/CSV ตัวอย่าง (ไม่มีข้อมูลจริงถ้าเป็นไปได้)
```

## กติกา

1. งานใหม่ทั้งหมดเริ่มที่นี่
2. ห้ามรัน SQL ทำลายข้อมูลบน DB `bnyfoodproducts` โดยไม่ได้รับอนุญาต
3. โปรโมทเข้า `application/` ต้องมีคำสั่งชัดจากผู้ดูแล

## สถานะปัจจุบัน

- [ ] ตกลง architecture decisions
- [ ] Phase 1 schema ใน `sql/`
- [ ] Phase 2 webhook + worker ใน `php/`
- [ ] Phase 3 excel reconcile
- [ ] โปรโมทเข้า production (รอสั่ง)
