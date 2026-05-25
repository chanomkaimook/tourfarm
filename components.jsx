// Shared UI primitives
const { useState, useEffect, useMemo, useRef } = React;

// ===== Icons (simple inline SVG) =====
const Icon = ({ name, size = 18, stroke = 1.6 }) => {
  const paths = {
    dashboard: <><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></>,
    calendar: <><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/></>,
    list: <><path d="M8 6h13M8 12h13M8 18h13"/><circle cx="4" cy="6" r="1"/><circle cx="4" cy="12" r="1"/><circle cx="4" cy="18" r="1"/></>,
    plus: <path d="M12 5v14M5 12h14"/>,
    download: <><path d="M12 4v12m0 0l-4-4m4 4l4-4"/><path d="M4 20h16"/></>,
    filter: <path d="M3 5h18l-7 9v6l-4-2v-4L3 5z"/>,
    search: <><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></>,
    x: <path d="M6 6l12 12M18 6L6 18"/>,
    edit: <><path d="M4 20h4l10-10-4-4L4 16v4z"/><path d="M14 6l4 4"/></>,
    eye: <><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></>,
    pencil: <><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></>,
    chev_l: <path d="M15 6l-6 6 6 6"/>,
    chev_r: <path d="M9 6l6 6-6 6"/>,
    chev_d: <path d="M6 9l6 6 6-6"/>,
    user: <><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-7 8-7s8 3 8 7"/></>,
    users: <><circle cx="9" cy="8" r="4"/><circle cx="17" cy="9" r="3"/><path d="M2 21c0-4 3.5-6.5 7-6.5s7 2.5 7 6.5"/><path d="M15 21c0-2.5 2-4 4-4s3 1 3 3"/></>,
    money: <><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="3"/></>,
    check: <path d="M5 12l4 4 10-10"/>,
    trash: <><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13"/></>,
    excel: <><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 8l8 8M16 8l-8 8"/></>,
    clock: <><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></>,
    pin: <><path d="M12 22s7-7.5 7-13a7 7 0 10-14 0c0 5.5 7 13 7 13z"/><circle cx="12" cy="9" r="2.5"/></>,
    phone: <path d="M5 4h3l2 5-2.5 1.5a11 11 0 005 5L14 13l5 2v3a2 2 0 01-2 2A15 15 0 013 6a2 2 0 012-2z"/>,
    farm: <><path d="M3 11l9-7 9 7"/><path d="M5 10v10h14V10"/><path d="M10 20v-5h4v5"/></>,
    sun: <><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></>,
    moon: <path d="M21 13.5A8.5 8.5 0 1110.5 3a6.5 6.5 0 0010.5 10.5z"/>,
    logout: <><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/><path d="M21 19V5a2 2 0 00-2-2h-5"/></>,
    lock: <><rect x="4" y="11" width="16" height="10" rx="2"/><path d="M8 11V8a4 4 0 018 0v3"/></>,
  };
  return (
    <svg width={size} height={size} viewBox="0 0 24 24" fill="none"
         stroke="currentColor" strokeWidth={stroke} strokeLinecap="round" strokeLinejoin="round">
      {paths[name] || null}
    </svg>
  );
};

// ===== Sidebar =====
function Sidebar({ page, onNavigate, stats, theme, onToggleTheme, user, onSignOut }) {
  const items = [
    { key: 'dashboard', icon: 'dashboard', label: 'แดชบอร์ด' },
    { key: 'calendar',  icon: 'calendar',  label: 'ปฏิทินการจอง' },
    { key: 'list',      icon: 'list',      label: 'รายการจอง' },
  ];
  return (
    <aside className="sidebar">
      <div className="brand">
        <div className="brand-mark"><Icon name="farm" size={20} stroke={1.8} /></div>
        <div>
          <div className="brand-name">Farm Booking</div>
          <div className="brand-sub">ระบบจองท่องเที่ยวฟาร์ม · กรุ๊ป</div>
        </div>
      </div>
      <nav className="nav">
        {items.map(it => (
          <button key={it.key}
                  className={`nav-item ${page === it.key ? 'is-active' : ''}`}
                  onClick={() => onNavigate(it.key)}>
            <Icon name={it.icon} size={18} />
            <span>{it.label}</span>
          </button>
        ))}
      </nav>
      <button className="theme-toggle" onClick={onToggleTheme} aria-label="สลับธีม">
        <span className={`theme-toggle-option ${theme === 'light' ? 'is-on' : ''}`}>
          <Icon name="sun" size={15}/>
          <span>Sun</span>
        </span>
        <span className={`theme-toggle-option ${theme === 'dark' ? 'is-on' : ''}`}>
          <Icon name="moon" size={15}/>
          <span>Dark</span>
        </span>
      </button>
      <div className="side-card">
        <div className="side-card-label">รอบจองวันนี้</div>
        <div className="side-card-value">
          <span className="num">{stats.todayRounds}</span>
          <span className="den">/ {MAX_ROUNDS_PER_DAY * 1}</span>
        </div>
        <div className="side-card-bar">
          <div style={{ width: `${(stats.todayRounds / MAX_ROUNDS_PER_DAY) * 100}%` }}></div>
        </div>
        <div className="side-card-foot">{stats.todayVisitors} คน · {stats.todayDeposit} มัดจำ</div>
      </div>
      <div className="side-user">
        <div className="avatar">นภ</div>
        <div>
          <div className="user-name">{user?.email || 'Farm Operations'}</div>
          <div className="user-role">เข้าสู่ระบบด้วย Firebase Auth</div>
        </div>
        <button className="icon-btn-sm signout-btn" onClick={onSignOut} title="ออกจากระบบ">
          <Icon name="logout" size={15}/>
        </button>
      </div>
    </aside>
  );
}

function AuthScreen({ configured, setupError, loading, error, onLogin, onGoogleLogin, theme, onToggleTheme }) {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const isBusy = Boolean(loading);

  const submit = (e) => {
    e.preventDefault();
    if (!configured || isBusy) return;
    onLogin(email.trim(), password);
  };

  return (
    <div className="auth-page">
      <button className="theme-toggle auth-theme-toggle" onClick={onToggleTheme} aria-label="สลับธีม">
        <span className={`theme-toggle-option ${theme === 'light' ? 'is-on' : ''}`}>
          <Icon name="sun" size={15}/><span>Sun</span>
        </span>
        <span className={`theme-toggle-option ${theme === 'dark' ? 'is-on' : ''}`}>
          <Icon name="moon" size={15}/><span>Dark</span>
        </span>
      </button>

      <form className="auth-card" onSubmit={submit}>
        <div className="brand auth-brand">
          <div className="brand-mark"><Icon name="farm" size={21} stroke={1.8} /></div>
          <div>
            <div className="brand-name">Farm Booking</div>
            <div className="brand-sub">ระบบจองออนไลน์ผ่าน Firebase</div>
          </div>
        </div>

        <div className="auth-lock"><Icon name="lock" size={22}/></div>
        <h1 className="auth-title">เข้าสู่ระบบ</h1>
        <p className="auth-subtitle">ใช้บัญชีที่สร้างไว้ใน Firebase Authentication หรือ Gmail</p>

        {!configured && (
          <div className="auth-alert">
            {setupError || 'กรุณากำหนดค่า Firebase ก่อนใช้งานออนไลน์'}
            <div className="auth-alert-sub">แก้ไฟล์ firebase-config.js แล้วเปิดเว็บใหม่อีกครั้ง</div>
          </div>
        )}

        {error && <div className="auth-alert auth-alert-danger">{error}</div>}

        <label className="field auth-field">
          <span className="field-label">Email</span>
          <input type="email" value={email} onChange={e => setEmail(e.target.value)}
                 placeholder="name@example.com" disabled={!configured || isBusy} required/>
        </label>
        <label className="field auth-field">
          <span className="field-label">Password</span>
          <input type="password" value={password} onChange={e => setPassword(e.target.value)}
                 placeholder="••••••••" disabled={!configured || isBusy} required/>
        </label>

        <button className="btn btn-primary auth-submit" disabled={!configured || isBusy}>
          {loading === 'email' ? 'กำลังเข้าสู่ระบบ...' : 'เข้าสู่ระบบ'}
        </button>

        <div className="auth-divider"><span>หรือ</span></div>

        <button type="button" className="btn auth-google" onClick={onGoogleLogin} disabled={!configured || isBusy}>
          <span className="google-mark" aria-hidden="true">G</span>
          {loading === 'google' ? 'กำลังเชื่อมต่อ Gmail...' : 'เข้าสู่ระบบด้วย Gmail'}
        </button>
      </form>
    </div>
  );
}

// ===== TopBar =====
function TopBar({ title, subtitle, right }) {
  return (
    <div className="topbar">
      <div>
        <h1 className="page-title">{title}</h1>
        {subtitle && <div className="page-subtitle">{subtitle}</div>}
      </div>
      <div className="topbar-right">{right}</div>
    </div>
  );
}

// ===== Stat Card =====
function StatCard({ label, value, sub, accent, icon }) {
  return (
    <div className="stat-card">
      <div className="stat-head">
        <span className="stat-label">{label}</span>
        {icon && <span className="stat-icon" style={{ color: accent }}><Icon name={icon} size={16} /></span>}
      </div>
      <div className="stat-value" style={accent ? { color: accent } : null}>{value}</div>
      {sub && <div className="stat-sub">{sub}</div>}
    </div>
  );
}

// ===== Payment badge =====
function PaymentBadge({ status, size = 'md' }) {
  const s = PAYMENT_STATUSES.find(p => p.key === status);
  if (!s) return null;
  return (
    <span className={`pay-badge size-${size}`} style={{ background: s.bg, color: s.color }}>
      <span className="pay-dot" style={{ background: s.dot }}></span>
      {s.label}
    </span>
  );
}

// ===== Group type chip =====
function GroupChip({ type, sub }) {
  const g = GROUP_TYPES.find(g => g.key === type);
  if (!g) return null;
  return (
    <span className="group-chip" data-type={type}>
      {g.label}{sub ? ` · ${sub}` : ''}
    </span>
  );
}

// ===== Modal shell =====
function Modal({ open, onClose, children, width = 880 }) {
  useEffect(() => {
    if (!open) return;
    const onKey = (e) => { if (e.key === 'Escape') onClose(); };
    window.addEventListener('keydown', onKey);
    return () => window.removeEventListener('keydown', onKey);
  }, [open, onClose]);
  if (!open) return null;
  return (
    <div className="modal-backdrop" onClick={onClose}>
      <div className="modal" style={{ maxWidth: width }} onClick={(e) => e.stopPropagation()}>
        {children}
      </div>
    </div>
  );
}

// ===== Toast =====
function Toast({ msg }) {
  if (!msg) return null;
  return <div className="toast"><Icon name="check" size={16} /> {msg}</div>;
}

// ===== Empty state =====
function Empty({ children, icon = 'list' }) {
  return (
    <div className="empty">
      <Icon name={icon} size={28} stroke={1.2} />
      <div>{children}</div>
    </div>
  );
}

Object.assign(window, {
  Icon, Sidebar, AuthScreen, TopBar, StatCard, PaymentBadge, GroupChip, Modal, Toast, Empty
});
