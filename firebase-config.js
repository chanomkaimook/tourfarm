// Firebase public config. Fill these values from Firebase Console > Project settings > Web app.
// This config is safe to ship in a public frontend repo when Firestore rules require authentication.
window.FIREBASE_CONFIG = {
  apiKey: "AIzaSyDENjjQxVTh69K956lwvH0D4rnwCcIwKL8",
  authDomain: "booking-farm.firebaseapp.com",
  projectId: "booking-farm",
  storageBucket: "booking-farm.firebasestorage.app",
  messagingSenderId: "86867411119",
  appId: "1:86867411119:web:5585e517622129e4e8145a",
};

  /* const firebaseConfig = {
    apiKey: "AIzaSyDENjjQxVTh69K956lwvH0D4rnwCcIwKL8",
    authDomain: "booking-farm.firebaseapp.com",
    databaseURL: "https://booking-farm-default-rtdb.asia-southeast1.firebasedatabase.app",
    projectId: "booking-farm",
    storageBucket: "booking-farm.firebasestorage.app",
    messagingSenderId: "86867411119",
    appId: "1:86867411119:web:5585e517622129e4e8145a",
    measurementId: "G-M8LBMYL6W4"
  }; */

window.FIREBASE_COLLECTIONS = {
  bookings: "bookings",
};

window.isFirebaseConfigured = function isFirebaseConfigured() {
  const cfg = window.FIREBASE_CONFIG || {};
  return Boolean(
    cfg.apiKey &&
    cfg.projectId &&
    cfg.appId &&
    !String(cfg.apiKey).startsWith("PASTE_") &&
    !String(cfg.projectId).startsWith("PASTE_") &&
    !String(cfg.appId).startsWith("PASTE_")
  );
};

window.initFirebaseServices = function initFirebaseServices() {
  try {
    if (!window.firebase) {
      return { configured: false, error: "โหลด Firebase SDK ไม่สำเร็จ กรุณาตรวจสอบอินเทอร์เน็ตหรือ CDN" };
    }
    if (!window.isFirebaseConfigured()) {
      return { configured: false, error: "ยังไม่ได้ตั้งค่า Firebase ในไฟล์ firebase-config.js" };
    }
    if (!firebase.apps.length) firebase.initializeApp(window.FIREBASE_CONFIG);
    const auth = firebase.auth();
    const db = firebase.firestore();
    return {
      configured: true,
      auth,
      db,
      bookingsRef: db.collection(window.FIREBASE_COLLECTIONS.bookings),
    };
  } catch (err) {
    return { configured: false, error: err.message || "ตั้งค่า Firebase ไม่สำเร็จ" };
  }
};
