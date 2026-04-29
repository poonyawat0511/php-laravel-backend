# ระบบยื่นคำขอจัดตั้งสหกรณ์ (Cooperative Request Management API)

โปรเจกต์ระบบ Backend สำหรับจัดการคำขอจัดตั้งสหกรณ์ พัฒนาด้วย **Laravel 11** และใช้ **PostgreSQL** เป็นฐานข้อมูลหลักระบบมีการแบ่งสิทธิ์การใช้งานชัดเจนระหว่างผู้ใช้งานทั่วไป (Public) และเจ้าหน้าที่ (Staff)

---

## 1. ข้อมูลการทดสอบผ่านระบบ Cloud (Railway)

คุณสามารถทดสอบ API ได้ทันทีผ่าน URL สาธารณะโดยไม่ต้องติดตั้งโปรเจกต์ในเครื่อง

- **Base URL:** `https://php-laravel-backend-production.up.railway.app`
- **Setup Database:** `https://php-laravel-backend-production.up.railway.app/api/setup-db`  
  *(ใช้สำหรับรีเซ็ตและ Seed ข้อมูลเริ่มต้นเข้าฐานข้อมูลใหม่ทั้งหมด)*

---

## 2. ข้อมูลบัญชีสำหรับทดสอบ (Seed Users)

ข้อมูลชุดนี้ถูกติดตั้งไว้ในระบบเรียบร้อยแล้วผ่าน Database Seeder:

| บทบาท (Role) | อีเมล (Email) | รหัสผ่าน (Password) | สิทธิ์การใช้งาน |
| :--- | :--- | :--- | :--- |
| **Public User** | `public@test.com` | `user1234` | ยื่นคำขอจัดตั้ง, ดูรายการคำขอของตนเอง |
| **Staff User** | `staff@test.com` | `staff1234` | ดูคำขอทั้งหมดในระบบ, อนุมัติ/ปฏิเสธคำขอ |

---

## 3. การติดตั้งและรันโปรเจกต์ในเครื่อง (Localhost)

### ความต้องการของระบบ
- PHP 8.3 หรือสูงกว่า
- Composer
- ฐานข้อมูล PostgreSQL

### ขั้นตอนการรัน
1. **ติดตั้ง Dependencies:**
   ```bash
   composer install
2. **ตั้งค่าไฟล์ Environment:**
คัดลอกไฟล์ .env.example เป็น .env และตั้งค่าฐานข้อมูลให้ถูกต้อง
    ```bash
   cp .env.example .env
3. **สร้าง Application Key:**
    ```bash
    php artisan key:generate
4. **เตรียมฐานข้อมูล (Migrate & Seed):**
    ```bash
    php artisan migrate:fresh --seed
5. **เริ่มรันเซิร์ฟเวอร์:**
    ```bash
    php artisan serve
- ระบบจะรันอยู่ที่: http://127.0.0.1:8000

## 4. การทดสอบด้วย Postman
**ภายในโปรเจกต์ได้แนบไฟล์ Postman Collection ไว้เพื่อความสะดวกในการทดสอบ API:**
ไฟล์ที่เกี่ยวข้อง: php-laravel-backend.postman_collection (อยู่ในโฟลเดอร์หลัก)
### วิธีการใช้:
1. **Import ไฟล์เข้าโปรแกรม Postman**
2. **เริ่มต้นด้วยการเรียก Request register สำหรับลงทะเบียนผู้ใช้ใหม่**
3. **หลังจากลงทะเบียนผู้ใช้สามารถใช Email และ Password ในการ login ด้วยการเรียก Request login**
4. **คัดลอก Token ไปใส่ใน Header Authorization: Bearer {token} สำหรับ Request ที่ต้องใช้สิทธิ์ในการเข้าถึง**


# รายการ API Routes ที่สำคัญ
## Public Endpoints
- POST /api/register : ลงทะเบียนผู้ใช้ใหม่
- POST /api/login : เข้าสู่ระบบเพื่อรับ Token
- POST /api/cooperatives : ยื่นคำขอจัดตั้งสหกรณ์ (ต้องมี Token)
- GET /api/cooperatives/me : ดูประวัติคำขอของตนเอง (ต้องมี Token)

## Staff Endpoints (ต้องมี Staff Middleware)
- GET /api/staff/cooperatives : รายการคำขอทั้งหมด (รองรับ Query String ?status=pending)
- PATCH /api/staff/cooperatives/{id}/review : พิจารณาคำขอ (approved/rejected)

# โครงสร้างโปรเจกต์ที่สำคัญ
- app/Http/Controllers : การประมวลผล Logic ของระบบ
- app/Models : โครงสร้างตารางและสัมพันธ์ของข้อมูล
- database/migrations : โครงสร้าง Database Schema
- database/seeders : ข้อมูลตั้งต้นสำหรับทดสอบ (Public & Staff Users)
- routes/api.php : การกำหนดเส้นทาง API ทั้งหมด