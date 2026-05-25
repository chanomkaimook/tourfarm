// Pages: Dashboard, Calendar, List
const { useState: useStateP, useEffect: useEffectP, useMemo: useMemoP } = React;

// =============================================================
// DASHBOARD
// =============================================================
function DashboardPage({ bookings, onNavigate, onEdit }) {
  const today = getTodayDate();
  const todayISO = toISO(today);

  const stats = useMemoP(() => {
    const active = bookings.filter(b => b.payment !== 'cancelled');
    const totalVisitors = active.reduce((s, b) => s + b.adults + b.kids, 0);
    const totalAdults = active.reduce((s, b) => s + b.adults, 0);
    const totalKids = active.reduce((s, b) => s + b.kids, 0);
    const byStatus = PAYMENT_STATUSES.map(p => ({
      ...p,
      count: bookings.filter(b => b.payment === p.key).length
    }));
    const byGroup = GROUP_TYPES.map(g => ({
      ...g,
      count: active.filter(b => b.groupType === g.key).length
    }));
    const upcoming = bookings
      .filter(b => b.payment !== 'cancelled' && b.date >= todayISO)
      .sort((a,b) => a.date.localeCompare(b.date) || a.round.localeCompare(b.round))
      .slice(0, 6);
    // Visitors per day (next 14 days)
    const days = [];
    for (let i = 0; i < 14; i++) {
      const d = new Date(today); d.setDate(today.getDate() + i);
      const iso = toISO(d);
      const dayB = active.filter(b => b.date === iso);
      days.push({
        iso, label: `${d.getDate()} ${thMonthShort[d.getMonth()]}`,
        weekday: thDay[d.getDay()],
        rounds: dayB.length,
        visitors: dayB.reduce((s,b)=>s+b.adults+b.kids,0),
        isToday: iso === todayISO,
      });
    }
    return { active, totalVisitors, totalAdults, totalKids, byStatus, byGroup, upcoming, days };
  }, [bookings, todayISO]);

  const maxVisitors = Math.max(...stats.days.map(d => d.visitors), 1);

  return (
    <div className="page-pad">
      <TopBar title="แดชบอร์ดการจอง"
              subtitle={`สรุปข้อมูล ณ ${fmtThaiDateLong(todayISO)}`}
              right={<button className="btn btn-primary" onClick={() => onNavigate('calendar')}><Icon name="plus" size={16}/> จองใหม่</button>}/>

      <div className="stat-grid">
        <StatCard label="การจองทั้งหมด" value={bookings.length}
                  sub={`${stats.active.length} active · ${bookings.length - stats.active.length} ยกเลิก`}
                  icon="list" accent="var(--success)"/>
        <StatCard label="ผู้เข้าชมรวม" value={stats.totalVisitors.toLocaleString()}
                  sub={`ผู้ใหญ่ ${stats.totalAdults} · เด็ก ${stats.totalKids}`}
                  icon="users" accent="var(--accent)"/>
        <StatCard label="รอโอนเงิน"
                  value={stats.byStatus.find(s=>s.key==='unpaid').count}
                  sub="ติดตามการชำระมัดจำ"
                  icon="money" accent="var(--danger)"/>
        <StatCard label="โอนมัดจำแล้ว"
                  value={stats.byStatus.find(s=>s.key==='deposit').count}
                  sub="พร้อมต้อนรับ"
                  icon="check" accent="var(--success)"/>
      </div>

      <div className="dash-grid">
        {/* Chart */}
        <div className="card">
          <div className="card-head">
            <div>
              <h3 className="card-title">ผู้เข้าชม 14 วันถัดไป</h3>
              <div className="card-sub">นับเฉพาะการจองที่ยังไม่ถูกยกเลิก</div>
            </div>
          </div>
          <div className="bar-chart">
            {stats.days.map(d => (
              <div key={d.iso} className={`bar-col ${d.isToday ? 'is-today' : ''}`}>
                <div className="bar-wrap">
                  <div className="bar" style={{ height: `${(d.visitors / maxVisitors) * 100}%` }}>
                    {d.visitors > 0 && <span className="bar-val">{d.visitors}</span>}
                  </div>
                </div>
                <div className="bar-x">
                  <div className="bar-day">{d.weekday}</div>
                  <div className="bar-date">{d.label}</div>
                  <div className="bar-rounds">{d.rounds} รอบ</div>
                </div>
              </div>
            ))}
          </div>
        </div>

        {/* Group breakdown */}
        <div className="card">
          <div className="card-head">
            <h3 className="card-title">ประเภทกรุ๊ป</h3>
          </div>
          <ul className="bar-list">
            {stats.byGroup.map(g => {
              const max = Math.max(...stats.byGroup.map(x=>x.count), 1);
              return (
                <li key={g.key}>
                  <div className="bar-list-row">
                    <span className="bar-list-label">{g.label}</span>
                    <span className="bar-list-val">{g.count}</span>
                  </div>
                  <div className="bar-list-track">
                    <div className="bar-list-fill" data-type={g.key} style={{ width: `${(g.count/max)*100}%` }}></div>
                  </div>
                </li>
              );
            })}
          </ul>
          <div className="card-divider"></div>
          <div className="card-head"><h3 className="card-title">สถานะการโอน</h3></div>
          <ul className="status-list">
            {stats.byStatus.map(s => (
              <li key={s.key}>
                <span className="pay-dot" style={{ background: s.dot }}></span>
                <span className="status-label">{s.label}</span>
                <span className="status-count">{s.count}</span>
              </li>
            ))}
          </ul>
        </div>

        {/* Upcoming */}
        <div className="card col-span-2">
          <div className="card-head">
            <div>
              <h3 className="card-title">รายการจองที่กำลังจะมาถึง</h3>
              <div className="card-sub">เรียงตามวันที่และรอบ</div>
            </div>
            <button className="btn btn-ghost" onClick={() => onNavigate('list')}>
              ดูทั้งหมด <Icon name="chev_r" size={14}/>
            </button>
          </div>
          {stats.upcoming.length === 0 ? (
            <Empty>ยังไม่มีรายการที่กำลังจะมาถึง</Empty>
          ) : (
            <table className="dash-table">
              <thead>
                <tr>
                  <th>วันที่</th>
                  <th>รอบ</th>
                  <th>ลูกค้า</th>
                  <th>ประเภท</th>
                  <th>ผู้เข้าชม</th>
                  <th>ผู้ขาย</th>
                  <th>สถานะ</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                {stats.upcoming.map(b => (
                  <tr key={b.id} onClick={() => onEdit(b)}>
                    <td>
                      <div className="td-date">{fmtThaiDate(b.date)}</div>
                      <div className="td-day">{thDayFull[parseISO(b.date).getDay()]}</div>
                    </td>
                    <td><span className="round-tag">{b.round}</span></td>
                    <td><div className="td-cust">{b.customerName}</div><div className="td-sub">{b.id}</div></td>
                    <td><GroupChip type={b.groupType} sub={b.schoolSubType}/></td>
                    <td>
                      <div className="td-cust">{b.adults + b.kids} คน</div>
                      <div className="td-sub">ผู้ใหญ่ {b.adults} · เด็ก {b.kids}</div>
                    </td>
                    <td><span className="seller-tag">{b.seller}</span></td>
                    <td><PaymentBadge status={b.payment} size="sm"/></td>
                    <td><button className="icon-btn-sm" title="ดูข้อมูล"><Icon name="eye" size={14}/></button></td>
                  </tr>
                ))}
              </tbody>
            </table>
          )}
        </div>
      </div>
    </div>
  );
}

// =============================================================
// CALENDAR
// =============================================================
function CalendarPage({ bookings, onCreate, onEdit }) {
  const [cursor, setCursor] = useStateP(() => getMonthStartDateInAppTimeZone());
  const [selected, setSelected] = useStateP(null);
  const [selectedNewRounds, setSelectedNewRounds] = useStateP([]);

  const monthLabel = `${thMonth[cursor.getMonth()]} ${cursor.getFullYear() + 543}`;
  const todayISO = getTodayISO();

  // Build month grid
  const grid = useMemoP(() => {
    const first = new Date(cursor.getFullYear(), cursor.getMonth(), 1);
    const startDow = first.getDay();
    const daysInMonth = new Date(cursor.getFullYear(), cursor.getMonth() + 1, 0).getDate();
    const cells = [];
    for (let i = 0; i < startDow; i++) cells.push(null);
    for (let d = 1; d <= daysInMonth; d++) {
      const dt = new Date(cursor.getFullYear(), cursor.getMonth(), d);
      cells.push(dt);
    }
    while (cells.length % 7 !== 0) cells.push(null);
    return cells;
  }, [cursor]);

  const byDate = useMemoP(() => {
    const m = {};
    bookings.forEach(b => {
      if (b.payment === 'cancelled') return;
      (m[b.date] = m[b.date] || []).push(b);
    });
    Object.values(m).forEach(arr => arr.sort((a,b)=>a.round.localeCompare(b.round)));
    return m;
  }, [bookings]);

  // Month summary
  const monthSummary = useMemoP(() => {
    const ym = `${cursor.getFullYear()}-${pad(cursor.getMonth()+1)}`;
    const inMonth = bookings.filter(b => b.date.startsWith(ym) && b.payment !== 'cancelled');
    return {
      bookings: inMonth.length,
      rounds: inMonth.length, // each booking = 1 round
      visitors: inMonth.reduce((s,b)=>s+b.adults+b.kids,0),
      adults: inMonth.reduce((s,b)=>s+b.adults,0),
      kids: inMonth.reduce((s,b)=>s+b.kids,0),
      deposit: inMonth.filter(b=>b.payment==='deposit').length,
      unpaid: inMonth.filter(b=>b.payment==='unpaid').length,
    };
  }, [bookings, cursor]);

  const selectedBookings = selected ? (byDate[selected] || []) : [];
  const takenRounds = selectedBookings.map(b => b.round);
  const availableRounds = TIME_SLOTS.filter(t => !takenRounds.includes(t));

  const move = (delta) => {
    const c = new Date(cursor); c.setMonth(c.getMonth() + delta); setCursor(c);
  };

  const openDayModal = (date) => {
    setSelected(date);
    setSelectedNewRounds([]);
  };

  const closeDayModal = () => {
    setSelected(null);
    setSelectedNewRounds([]);
  };

  const toggleNewRound = (round) => {
    if (takenRounds.includes(round)) return;
    setSelectedNewRounds(prev => (
      prev.includes(round)
        ? prev.filter(x => x !== round)
        : [...prev, round].sort()
    ));
  };

  const createFromDayModal = () => {
    if (selectedNewRounds.length === 0) return;
    const date = selected;
    const rounds = selectedNewRounds;
    closeDayModal();
    onCreate(date, rounds, takenRounds);
  };

  const editFromDayModal = (booking) => {
    setSelected(null);
    onEdit(booking);
  };

  return (
    <div className="page-pad">
      <TopBar title="ปฏิทินการจอง"
              subtitle="คลิกที่วันที่ในตารางเพื่อเปิดดูรอบและจองใหม่"
              right={
                <div className="topbar-controls">
                  <button className="icon-btn" onClick={() => move(-1)}><Icon name="chev_l" size={18}/></button>
                  <div className="month-label">{monthLabel}</div>
                  <button className="icon-btn" onClick={() => move(1)}><Icon name="chev_r" size={18}/></button>
                  <button className="btn btn-ghost" onClick={() => setCursor(getMonthStartDateInAppTimeZone())}>วันนี้</button>
                </div>
              }/>

      {/* Month summary strip */}
      <div className="cal-summary">
        <div className="cal-sum-item">
          <div className="cal-sum-label">รายการจอง</div>
          <div className="cal-sum-value">{monthSummary.bookings}</div>
        </div>
        <div className="cal-sum-item">
          <div className="cal-sum-label">ผู้เข้าชม</div>
          <div className="cal-sum-value">{monthSummary.visitors.toLocaleString()}</div>
          <div className="cal-sum-sub">ผู้ใหญ่ {monthSummary.adults} · เด็ก {monthSummary.kids}</div>
        </div>
        <div className="cal-sum-item">
          <div className="cal-sum-label">โอนมัดจำแล้ว</div>
          <div className="cal-sum-value" style={{ color: 'var(--success)' }}>{monthSummary.deposit}</div>
        </div>
        <div className="cal-sum-item">
          <div className="cal-sum-label">รอโอนเงิน</div>
          <div className="cal-sum-value" style={{ color: 'var(--danger)' }}>{monthSummary.unpaid}</div>
        </div>
        <div className="cal-sum-item">
          <div className="cal-sum-label">ความหนาแน่นเฉลี่ย</div>
          <div className="cal-sum-value">
            {Math.round((monthSummary.rounds / (new Date(cursor.getFullYear(), cursor.getMonth()+1, 0).getDate() * MAX_ROUNDS_PER_DAY)) * 100)}%
          </div>
          <div className="cal-sum-sub">ของ {MAX_ROUNDS_PER_DAY} รอบ/วัน</div>
        </div>
      </div>

      <div className="cal-layout">
        <div className="cal-card">
          <div className="cal-dow">
            {thDay.map((d, i) => (
              <div key={i} className={`cal-dow-cell ${i === 0 ? 'is-sun' : ''} ${i === 6 ? 'is-sat' : ''}`}>{d}</div>
            ))}
          </div>
          <div className="cal-grid">
            {grid.map((dt, i) => {
              if (!dt) return <div key={i} className="cal-cell is-empty"></div>;
              const iso = toISO(dt);
              const list = byDate[iso] || [];
              const isToday = iso === todayISO;
              const isSelected = iso === selected;
              const isFull = list.length >= MAX_ROUNDS_PER_DAY;
              const dow = dt.getDay();
              return (
                <div key={i}
                     className={`cal-cell ${isToday ? 'is-today' : ''} ${isSelected ? 'is-selected' : ''} ${isFull ? 'is-full' : ''} ${dow === 0 ? 'is-sun' : ''} ${dow === 6 ? 'is-sat' : ''}`}
                     onClick={() => openDayModal(iso)}>
                  <div className="cal-cell-head">
                    <span className="cal-cell-date">{dt.getDate()}</span>
                    <span className="cal-cell-count">{list.length}/{MAX_ROUNDS_PER_DAY}</span>
                  </div>
                  <div className="cal-cell-body">
                    {list.slice(0, 4).map(b => (
                      <div key={b.id} className="cal-pill" data-pay={b.payment}
                           onClick={(e) => { e.stopPropagation(); onEdit(b); }}>
                        <span className="cal-pill-time">{b.round}</span>
                        <span className="cal-pill-name">{b.customerName}</span>
                      </div>
                    ))}
                    {list.length > 4 && (
                      <div className="cal-more">+{list.length - 4} รอบ</div>
                    )}
                    {list.length === 0 && !isFull && (
                      <div className="cal-empty">ว่าง</div>
                    )}
                    {isFull && <div className="cal-full-tag">เต็ม</div>}
                  </div>
                </div>
              );
            })}
          </div>
          <div className="cal-legend">
            <span className="legend-item"><span className="dot" style={{background:'var(--success)'}}></span> โอนมัดจำแล้ว</span>
            <span className="legend-item"><span className="dot" style={{background:'var(--danger)'}}></span> ยังไม่โอน</span>
            <span className="legend-item"><span className="dot" style={{background:'var(--line)'}}></span> รอบว่าง</span>
            <span className="legend-spacer"></span>
            <span className="legend-note">1 วันรับสูงสุด {MAX_ROUNDS_PER_DAY} รอบ · 6 ช่วงเวลา 10:00–15:00</span>
          </div>
        </div>
      </div>

      <Modal open={!!selected} onClose={closeDayModal} width={760}>
        {selected && (
          <div className="day-panel day-panel-modal">
            <div className="day-head">
              <div>
                <div className="day-head-eyebrow">วันที่เลือก</div>
                <div className="day-head-date">{fmtThaiDateLong(selected)}</div>
              </div>
              <button className="icon-btn" onClick={closeDayModal}><Icon name="x" size={18}/></button>
            </div>
            <div className="day-stats">
              <div><b>{selectedBookings.length}</b>/{MAX_ROUNDS_PER_DAY} <span className="muted">รอบ</span></div>
              <div><b>{selectedBookings.reduce((s,b)=>s+b.adults+b.kids,0)}</b> <span className="muted">ผู้เข้าชม</span></div>
              <div><b>{availableRounds.length}</b> <span className="muted">รอบว่าง</span></div>
            </div>

            <div className="day-section-title">รอบที่จองแล้ว</div>
            <div className="day-list">
              {selectedBookings.length === 0 && <div className="muted">ยังไม่มีการจองในวันนี้</div>}
              {selectedBookings.map(b => (
                <div key={b.id} className="day-card" onClick={() => editFromDayModal(b)}>
                  <div className="day-card-time">{b.round}</div>
                  <div className="day-card-main">
                    <div className="day-card-name">{b.customerName}</div>
                    <div className="day-card-sub">
                      <GroupChip type={b.groupType} sub={b.schoolSubType}/>
                      <span>{b.adults + b.kids} คน</span>
                      <span>{b.seller}</span>
                    </div>
                  </div>
                  <PaymentBadge status={b.payment} size="sm"/>
                </div>
              ))}
            </div>

            {availableRounds.length > 0 && (
              <>
                <div className="day-section-title">จองรอบใหม่</div>
                <div className="day-round-helper">เลือกได้หลายรอบ แล้วกด Next เพื่อกรอกข้อมูลการจองครั้งเดียว</div>
                <div className="day-rounds">
                  {TIME_SLOTS.map(t => {
                    const taken = takenRounds.includes(t);
                    const picked = selectedNewRounds.includes(t);
                    return (
                      <button key={t}
                              className={`day-round ${taken ? 'is-taken' : ''} ${picked ? 'is-selected' : ''}`}
                              disabled={taken}
                              onClick={() => toggleNewRound(t)}>
                        <span className="day-round-time">{t}</span>
                        <span className="day-round-tag">{taken ? 'จองแล้ว' : picked ? 'เลือกแล้ว' : 'เลือกรอบนี้'}</span>
                      </button>
                    );
                  })}
                </div>
                <div className="day-modal-actions">
                  <div className="day-picked-count">
                    เลือกแล้ว <b>{selectedNewRounds.length}</b> รอบ
                  </div>
                  <button className="btn btn-primary"
                          disabled={selectedNewRounds.length === 0}
                          onClick={createFromDayModal}>
                    Next <Icon name="chev_r" size={16}/>
                  </button>
                </div>
              </>
            )}
            {availableRounds.length === 0 && (
              <div className="full-banner">วันนี้รับเต็มทั้ง {MAX_ROUNDS_PER_DAY} รอบแล้ว</div>
            )}
          </div>
        )}
      </Modal>
    </div>
  );
}

// =============================================================
// LIST
// =============================================================
function ListPage({ bookings, onEdit, onCreate, onExport }) {
  const [from, setFrom] = useStateP('');
  const [to, setTo] = useStateP('');
  const [groupFilter, setGroupFilter] = useStateP([]); // array of keys
  const [payFilter, setPayFilter] = useStateP([]);
  const [search, setSearch] = useStateP('');

  const toggleArr = (arr, setArr, v) =>
    setArr(arr.includes(v) ? arr.filter(x => x !== v) : [...arr, v]);

  const filtered = useMemoP(() => {
    return bookings.filter(b => {
      if (from && b.date < from) return false;
      if (to && b.date > to) return false;
      if (groupFilter.length && !groupFilter.includes(b.groupType)) return false;
      if (payFilter.length && !payFilter.includes(b.payment)) return false;
      if (search) {
        const q = search.toLowerCase();
        if (!(b.customerName.toLowerCase().includes(q)
              || b.id.toLowerCase().includes(q)
              || b.contactName.toLowerCase().includes(q))) return false;
      }
      return true;
    }).sort((a,b) => b.date.localeCompare(a.date) || b.round.localeCompare(a.round));
  }, [bookings, from, to, groupFilter, payFilter, search]);

  const clearFilters = () => { setFrom(''); setTo(''); setGroupFilter([]); setPayFilter([]); setSearch(''); };
  const totalVisitors = filtered.filter(b=>b.payment!=='cancelled').reduce((s,b)=>s+b.adults+b.kids,0);

  return (
    <div className="page-pad">
      <TopBar title="รายการจองทั้งหมด"
              subtitle={`พบ ${filtered.length} รายการ · ผู้เข้าชม ${totalVisitors.toLocaleString()} คน`}
              right={
                <div className="topbar-controls">
                  <button className="btn btn-ghost" onClick={() => onExport(filtered)}>
                    <Icon name="excel" size={16}/> ออกรายงาน Excel
                  </button>
                  <button className="btn btn-primary" onClick={onCreate}>
                    <Icon name="plus" size={16}/> จองใหม่
                  </button>
                </div>
              }/>

      <div className="filter-bar">
        <div className="filter-group">
          <div className="filter-label">ช่วงวันที่</div>
          <div className="date-range">
            <input type="date" value={from} onChange={e => setFrom(e.target.value)} placeholder="จาก"/>
            <span className="dash">–</span>
            <input type="date" value={to} onChange={e => setTo(e.target.value)} placeholder="ถึง"/>
          </div>
        </div>
        <div className="filter-group">
          <div className="filter-label">ประเภทกรุ๊ป</div>
          <div className="chip-row">
            {GROUP_TYPES.map(g => (
              <label key={g.key} className={`check-chip ${groupFilter.includes(g.key) ? 'is-on' : ''}`}>
                <input type="checkbox" checked={groupFilter.includes(g.key)}
                       onChange={() => toggleArr(groupFilter, setGroupFilter, g.key)}/>
                <span>{g.label}</span>
              </label>
            ))}
          </div>
        </div>
        <div className="filter-group">
          <div className="filter-label">สถานะการโอน</div>
          <div className="chip-row">
            {PAYMENT_STATUSES.map(p => (
              <label key={p.key} className={`check-chip pay ${payFilter.includes(p.key) ? 'is-on' : ''}`}
                     style={ payFilter.includes(p.key) ? { borderColor: p.dot, background: p.bg, color: p.color } : null }>
                <input type="checkbox" checked={payFilter.includes(p.key)}
                       onChange={() => toggleArr(payFilter, setPayFilter, p.key)}/>
                <span className="pay-dot" style={{ background: p.dot }}></span>
                <span>{p.label}</span>
              </label>
            ))}
          </div>
        </div>
        <div className="filter-group flex-grow">
          <div className="filter-label">ค้นหา</div>
          <div className="search-wrap">
            <Icon name="search" size={14}/>
            <input value={search} onChange={e => setSearch(e.target.value)}
                   placeholder="ชื่อลูกค้า, ผู้ประสานงาน, รหัสรายการ"/>
          </div>
        </div>
        {(from || to || groupFilter.length || payFilter.length || search) ? (
          <button className="btn btn-ghost btn-sm" onClick={clearFilters}>
            <Icon name="x" size={14}/> ล้างตัวกรอง
          </button>
        ) : null}
      </div>

      {filtered.length === 0 ? (
        <Empty>ไม่พบรายการที่ตรงกับตัวกรอง</Empty>
      ) : (
        <div className="card-list">
          {filtered.map(b => (
            <article key={b.id} className="book-card" data-pay={b.payment} onClick={() => onEdit(b)}>
              <div className="book-card-left">
                <div className="book-card-day">{fmtThaiDate(b.date)}</div>
                <div className="book-card-weekday">{thDayFull[parseISO(b.date).getDay()]}</div>
                <div className="book-card-time">{b.round}</div>
              </div>
              <div className="book-card-main">
                <div className="book-card-id">{b.id}</div>
                <div className="book-card-name">{b.customerName}</div>
                <div className="book-card-meta">
                  <GroupChip type={b.groupType} sub={b.schoolSubType}/>
                  <span className="meta-item"><Icon name="user" size={13}/> {b.contactName}</span>
                  <span className="meta-item"><Icon name="phone" size={13}/> {b.contactPhone}</span>
                  <span className="meta-item"><Icon name="users" size={13}/> {b.adults + b.kids} คน <span className="muted">(ผู้ใหญ่ {b.adults}/เด็ก {b.kids})</span></span>
                  <span className="meta-item">ผู้ขาย <b>{b.seller}</b></span>
                </div>
                <div className="book-card-stations">
                  <span className="stations-label">ฐาน {b.stations.length} ฐาน:</span>
                  {b.stations.map(i => (
                    <span key={i} className="station-tag">{i+1}. {STATIONS[i]}</span>
                  ))}
                </div>
                {b.note && <div className="book-card-note">หมายเหตุ: {b.note}</div>}
              </div>
              <div className="book-card-right">
                <PaymentBadge status={b.payment} size="md"/>
                <button className="btn btn-ghost btn-sm" onClick={(e) => { e.stopPropagation(); onEdit(b); }}>
                  <Icon name="eye" size={14}/> ดูข้อมูล
                </button>
              </div>
            </article>
          ))}
        </div>
      )}
    </div>
  );
}

Object.assign(window, { DashboardPage, CalendarPage, ListPage });
