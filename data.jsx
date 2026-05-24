// ===== Constants =====
const TIME_SLOTS = ['10:00', '11:00', '12:00', '13:00', '14:00', '15:00'];
const MAX_ROUNDS_PER_DAY = 6;

const SELLERS = ['HO', 'Farm'];

const PAYMENT_STATUSES = [
  { key: 'unpaid',    label: 'ยังไม่โอน',     color: 'var(--danger)', bg: 'var(--danger-bg)', dot: 'var(--danger)' },
  { key: 'deposit',   label: 'โอนมัดจำแล้ว',  color: 'var(--success)', bg: 'var(--success-bg)', dot: 'var(--success)' },
  { key: 'cancelled', label: 'ยกเลิกการจอง', color: 'var(--neutral)', bg: 'var(--neutral-bg)', dot: 'var(--neutral)' },
];

const GROUP_TYPES = [
  { key: 'company',     label: 'บริษัท' },
  { key: 'tour',        label: 'บริษัททัวร์' },
  { key: 'school',      label: 'โรงเรียน', sub: ['รัฐ', 'เอกชน', 'นานาชาติ'] },
  { key: 'government',  label: 'หน่วยงานราชการ' },
];

const STATIONS = [
  'ฐานการรีดนมแม่โค',
  'เล่าเรื่องสายพันธุ์โคนม',
  'การฝึกสุนัขขั้นพื้นฐาน',
  'สายพันธุ์สุนัข',
  'การเลี้ยงกระต่ายและแกะ',
  'การเลี้ยงไก่และหมูแคระ',
  'การแต่งกายคาวบอย',
  'การแต่งกายม้าคาวบอย',
];

// ===== Helpers =====
const pad = (n) => String(n).padStart(2, '0');
const toISO = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
const parseISO = (s) => { const [y,m,d] = s.split('-').map(Number); return new Date(y, m-1, d); };
const thMonth = ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
const thMonthShort = ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
const thDay = ['อา.','จ.','อ.','พ.','พฤ.','ศ.','ส.'];
const thDayFull = ['อาทิตย์','จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์'];

function fmtThaiDate(iso) {
  const d = parseISO(iso);
  return `${d.getDate()} ${thMonthShort[d.getMonth()]} ${(d.getFullYear()+543) % 100}`;
}
function fmtThaiDateLong(iso) {
  const d = parseISO(iso);
  return `${thDayFull[d.getDay()]}ที่ ${d.getDate()} ${thMonth[d.getMonth()]} ${d.getFullYear()+543}`;
}

// ===== Sample bookings =====
// Note: today set relative to 2026-05-24 for realistic spread.
const SAMPLE_BOOKINGS = [
  {
    id: 'B-2605-001',
    customerName: 'บริษัท เซ็นทรัล รีเทล จำกัด',
    address: '99 อาคารเซ็นทรัลเวิลด์ ถ.พระราม 1 ปทุมวัน กรุงเทพฯ 10330',
    taxId: '0105543091234',
    contactName: 'คุณนภัส อิทธิพล',
    contactPhone: '081-234-5678',
    adults: 35, kids: 0,
    date: '2026-05-25', round: '10:00',
    seller: 'HO',
    groupType: 'company', schoolSubType: null,
    stations: [0, 1, 6],
    payment: 'deposit',
    note: 'ขอเตรียมน้ำดื่ม + ป้ายต้อนรับ',
  },
  {
    id: 'B-2605-002',
    customerName: 'โรงเรียนสาธิตจุฬาฯ (ฝ่ายประถม)',
    address: '254 ถ.พญาไท แขวงวังใหม่ ปทุมวัน กรุงเทพฯ 10330',
    taxId: '0994000159058',
    contactName: 'อาจารย์ปิยะดา ศรีสุข',
    contactPhone: '089-555-1224',
    adults: 8, kids: 62,
    date: '2026-05-25', round: '11:00',
    seller: 'Farm',
    groupType: 'school', schoolSubType: 'เอกชน',
    stations: [0, 2, 4, 5],
    payment: 'deposit',
    note: 'นักเรียน ป.4 — ขอวิทยากรพูดช้าๆ',
  },
  {
    id: 'B-2605-003',
    customerName: 'Wonder Asia Tour Co., Ltd.',
    address: '88/9 ถ.สุขุมวิท 23 คลองเตยเหนือ วัฒนา กรุงเทพฯ',
    taxId: '0105561012876',
    contactName: 'Mr. Kenji Watanabe',
    contactPhone: '092-118-0099',
    adults: 24, kids: 4,
    date: '2026-05-26', round: '13:00',
    seller: 'HO',
    groupType: 'tour', schoolSubType: null,
    stations: [1, 3, 6, 7],
    payment: 'unpaid',
    note: 'กรุ๊ปทัวร์ญี่ปุ่น — ต้องการล่ามภาษาญี่ปุ่น',
  },
  {
    id: 'B-2605-004',
    customerName: 'โรงเรียนนานาชาติบางกอกพรีพ',
    address: '77 สุขุมวิท 77 พระโขนง วัฒนา กรุงเทพฯ',
    taxId: '0105549088421',
    contactName: 'Ms. Sarah Hill',
    contactPhone: '084-770-2211',
    adults: 6, kids: 28,
    date: '2026-05-26', round: '10:00',
    seller: 'Farm',
    groupType: 'school', schoolSubType: 'นานาชาติ',
    stations: [2, 3, 5, 6, 7],
    payment: 'deposit',
    note: '',
  },
  {
    id: 'B-2605-005',
    customerName: 'กรมส่งเสริมการเกษตร',
    address: '2143/1 ถ.พหลโยธิน แขวงลาดยาว เขตจตุจักร กรุงเทพฯ',
    taxId: '0994000165023',
    contactName: 'คุณวีระศักดิ์ พรชัย',
    contactPhone: '086-321-9988',
    adults: 42, kids: 0,
    date: '2026-05-27', round: '14:00',
    seller: 'HO',
    groupType: 'government', schoolSubType: null,
    stations: [0, 1],
    payment: 'unpaid',
    note: 'ดูงานเรื่องโคนม',
  },
  {
    id: 'B-2605-006',
    customerName: 'โรงเรียนวัดบางนา (รัฐ)',
    address: '15 หมู่ 4 ต.บางนา อ.เมือง จ.สมุทรปราการ',
    taxId: '0994000234521',
    contactName: 'ผอ.ประภาส ใจดี',
    contactPhone: '083-441-7700',
    adults: 5, kids: 48,
    date: '2026-05-28', round: '10:00',
    seller: 'Farm',
    groupType: 'school', schoolSubType: 'รัฐ',
    stations: [0, 2, 4, 5],
    payment: 'deposit',
    note: '',
  },
  {
    id: 'B-2605-007',
    customerName: 'บริษัท แอดวานซ์ อินโฟร์ จำกัด',
    address: '414 ถ.พหลโยธิน สามเสนใน พญาไท กรุงเทพฯ',
    taxId: '0107535000346',
    contactName: 'คุณธนวัฒน์ ศักดิ์ดี',
    contactPhone: '081-919-5544',
    adults: 28, kids: 22,
    date: '2026-05-29', round: '11:00',
    seller: 'HO',
    groupType: 'company', schoolSubType: null,
    stations: [1, 6, 7],
    payment: 'deposit',
    note: 'Family Day พนักงาน',
  },
  {
    id: 'B-2605-008',
    customerName: 'Asia Holiday Tours',
    address: '321 ถ.รัชดาภิเษก ห้วยขวาง กรุงเทพฯ',
    taxId: '0105558901123',
    contactName: 'คุณอรทัย วรรณา',
    contactPhone: '098-661-2233',
    adults: 32, kids: 2,
    date: '2026-05-29', round: '13:00',
    seller: 'HO',
    groupType: 'tour', schoolSubType: null,
    stations: [0, 2, 6],
    payment: 'cancelled',
    note: 'ยกเลิกเนื่องจากกรุ๊ปไม่ครบจำนวน',
  },
  {
    id: 'B-2606-001',
    customerName: 'โรงเรียนสาธิตเกษตรฯ',
    address: '50 ถ.งามวงศ์วาน ลาดยาว จตุจักร กรุงเทพฯ',
    taxId: '0994000178822',
    contactName: 'อ.สุรชัย พันธุ์ดี',
    contactPhone: '081-777-3322',
    adults: 7, kids: 55,
    date: '2026-06-02', round: '10:00',
    seller: 'Farm',
    groupType: 'school', schoolSubType: 'รัฐ',
    stations: [0, 1, 4, 5],
    payment: 'deposit',
    note: '',
  },
  {
    id: 'B-2606-002',
    customerName: 'บริษัท ปตท. จำกัด (มหาชน)',
    address: '555 ถ.วิภาวดีรังสิต จตุจักร กรุงเทพฯ',
    taxId: '0107544000108',
    contactName: 'คุณกานต์ ทองดี',
    contactPhone: '086-554-1188',
    adults: 60, kids: 0,
    date: '2026-06-03', round: '14:00',
    seller: 'HO',
    groupType: 'company', schoolSubType: null,
    stations: [1, 6, 7],
    payment: 'unpaid',
    note: 'CSR กิจกรรมพนักงาน',
  },
  {
    id: 'B-2606-003',
    customerName: 'Sunshine Tour (Singapore)',
    address: '88 Beach Rd, Singapore',
    taxId: '—',
    contactName: 'Ms. Lim Wei Ling',
    contactPhone: '+65 9120 4455',
    adults: 26, kids: 6,
    date: '2026-06-05', round: '12:00',
    seller: 'Farm',
    groupType: 'tour', schoolSubType: null,
    stations: [2, 3, 6, 7],
    payment: 'deposit',
    note: 'อังกฤษ-จีน',
  },
  {
    id: 'B-2606-004',
    customerName: 'องค์การบริหารส่วนตำบลบางพลี',
    address: 'อ.บางพลี จ.สมุทรปราการ',
    taxId: '0994000451102',
    contactName: 'คุณนิตยา รักษ์ดี',
    contactPhone: '089-220-1144',
    adults: 38, kids: 12,
    date: '2026-06-08', round: '11:00',
    seller: 'HO',
    groupType: 'government', schoolSubType: null,
    stations: [0, 4, 5],
    payment: 'deposit',
    note: '',
  },
  {
    id: 'B-2605-009',
    customerName: 'โรงเรียนเซนต์โยเซฟ',
    address: 'ถ.สีลม บางรัก กรุงเทพฯ',
    taxId: '0994000222113',
    contactName: 'อ.มาลี วงศ์ศรี',
    contactPhone: '081-200-8899',
    adults: 6, kids: 44,
    date: '2026-05-25', round: '13:00',
    seller: 'Farm',
    groupType: 'school', schoolSubType: 'เอกชน',
    stations: [0, 2, 4],
    payment: 'unpaid',
    note: '',
  },
  {
    id: 'B-2605-010',
    customerName: 'บริษัท เอสซีจี เคมิคอลส์',
    address: 'นิคมอุตสาหกรรมมาบตาพุด ระยอง',
    taxId: '0107546000159',
    contactName: 'คุณพรชนก เจริญสุข',
    contactPhone: '082-330-7788',
    adults: 45, kids: 5,
    date: '2026-05-25', round: '14:00',
    seller: 'HO',
    groupType: 'company', schoolSubType: null,
    stations: [1, 3, 6, 7],
    payment: 'deposit',
    note: 'ทัศนศึกษาพนักงานใหม่',
  },
];

Object.assign(window, {
  TIME_SLOTS, MAX_ROUNDS_PER_DAY, SELLERS, PAYMENT_STATUSES, GROUP_TYPES, STATIONS,
  pad, toISO, parseISO, thMonth, thMonthShort, thDay, thDayFull,
  fmtThaiDate, fmtThaiDateLong, SAMPLE_BOOKINGS,
});
