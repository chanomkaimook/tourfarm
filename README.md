# Farm Booking Online Setup

ระบบนี้เป็น static React app ที่ deploy ผ่าน GitHub Pages ได้ และใช้ Firebase Authentication + Firestore เป็น backend

## 1. Firebase

1. สร้าง Firebase project
2. เพิ่ม Web app ใน Project settings
3. เปิด Authentication > Sign-in method > Email/Password
4. สร้าง user ใน Authentication > Users
5. เปิด Firestore Database
6. Publish rules จากไฟล์ `firestore.rules`

## 2. ใส่ Firebase config

แก้ไฟล์ `firebase-config.js`

```js
window.FIREBASE_CONFIG = {
  apiKey: "...",
  authDomain: "...",
  projectId: "...",
  storageBucket: "...",
  messagingSenderId: "...",
  appId: "...",
};
```

Firebase config ไม่ใช่ secret สำหรับเว็บ frontend แต่ต้องใช้ Firestore rules เพื่อกันคนที่ไม่ได้ login

## 3. GitHub Pages

1. สร้าง GitHub repository
2. Upload ไฟล์ทั้งหมดในโฟลเดอร์นี้
3. ไปที่ Settings > Pages
4. เลือก Deploy from a branch
5. เลือก branch `main` และ folder `/root`
6. หลัง deploy ให้ copy URL ของ GitHub Pages
7. ใน Firebase Console ไปที่ Authentication > Settings > Authorized domains
8. เพิ่ม domain ของ GitHub Pages เช่น `yourname.github.io`

## 4. โครงสร้างข้อมูล Firestore

Collection: `bookings`

Document ID ใช้รหัส booking เช่น `B-1234567` หรือ `B-1234567-01`

ฟิลด์หลัก:

- `customerName`
- `address`
- `taxId`
- `contactName`
- `contactPhone`
- `adults`
- `kids`
- `date`
- `round`
- `seller`
- `groupType`
- `schoolSubType`
- `stations`
- `payment`
- `note`
- `createdAt`
- `createdBy`
- `updatedAt`
- `updatedBy`

## 5. หมายเหตุ

- ถ้าเห็นหน้าบอกว่ายังไม่ได้ตั้งค่า Firebase ให้ตรวจไฟล์ `firebase-config.js`
- ถ้า login แล้วอ่าน/เขียนข้อมูลไม่ได้ ให้ตรวจ Firestore rules และ Authorized domains
- ระบบนี้ไม่เปิดสมัครสมาชิกเอง ผู้ดูแลควรสร้าง user ใน Firebase Console
