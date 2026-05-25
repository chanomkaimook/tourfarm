// Booking form modal — create or edit a booking
const { useState: useStateBF, useEffect: useEffectBF, useMemo: useMemoBF } = React;

function BookingForm({ open, initial, lockedDate, lockedRound, lockedRounds = [], takenRounds = [], bookings = [], onClose, onSave, onDelete }) {
  const initialLockedRounds = lockedRounds.length ? lockedRounds : (lockedRound ? [lockedRound] : []);
  const empty = {
    id: null,
    customerName: '', address: '', taxId: '',
    contactName: '', contactPhone: '',
    adults: 0, kids: 0,
    date: lockedDate || '', round: initialLockedRounds[0] || '', rounds: initialLockedRounds,
    seller: 'HO',
    groupType: 'company', schoolSubType: null,
    stations: [],
    payment: 'unpaid',
    note: '',
  };
  const [form, setForm] = useStateBF(empty);
  const [errors, setErrors] = useStateBF({});
  const [activeTab, setActiveTab] = useStateBF('edit');

  useEffectBF(() => {
    if (!open) return;
    const nextLockedRounds = lockedRounds.length ? lockedRounds : (lockedRound ? [lockedRound] : []);
    if (initial) setForm({ ...empty, ...initial, rounds: [initial.round] });
    else setForm({ ...empty, date: lockedDate || '', round: nextLockedRounds[0] || '', rounds: nextLockedRounds });
    setActiveTab(initial ? 'view' : 'edit');
    setErrors({});
  }, [open, initial, lockedDate, lockedRound, lockedRounds.join('|')]);

  const dateTakenRounds = useMemoBF(() => {
    if (!form.date) return takenRounds;
    const takenForDate = bookings
      .filter(b => b.date === form.date && b.id !== initial?.id && b.payment !== 'cancelled')
      .map(b => b.round);
    return Array.from(new Set([...takenRounds, ...takenForDate]));
  }, [bookings, form.date, initial?.id, takenRounds.join('|')]);

  if (!open) return null;
  const isEdit = !!initial;

  const set = (k, v) => setForm(f => ({ ...f, [k]: v }));
  const toggleStation = (i) => set('stations', form.stations.includes(i)
    ? form.stations.filter(x => x !== i) : [...form.stations, i].sort((a,b)=>a-b));
  const selectedRounds = Array.isArray(form.rounds) && form.rounds.length ? form.rounds : (form.round ? [form.round] : []);
  const hasLockedRounds = !!lockedDate && initialLockedRounds.length > 0 && !isEdit;
  const toggleRound = (slot) => {
    if (isEdit) {
      setForm(f => ({ ...f, round: slot, rounds: [slot] }));
      return;
    }
    setForm(f => {
      const current = Array.isArray(f.rounds) ? f.rounds : (f.round ? [f.round] : []);
      const next = current.includes(slot)
        ? current.filter(x => x !== slot)
        : [...current, slot].sort();
      return { ...f, rounds: next, round: next[0] || '' };
    });
  };

  const total = (Number(form.adults) || 0) + (Number(form.kids) || 0);

  const validate = () => {
    const e = {};
    if (!form.customerName.trim()) e.customerName = 'กรุณาระบุชื่อลูกค้า';
    if (!form.contactName.trim()) e.contactName = 'กรุณาระบุผู้ประสานงาน';
    if (!form.contactPhone.trim()) e.contactPhone = 'กรุณาระบุเบอร์';
    if (!form.date) e.date = 'กรุณาเลือกวันที่';
    if (selectedRounds.length === 0) e.round = 'กรุณาเลือกรอบ';
    if (form.groupType === 'school' && !form.schoolSubType) e.schoolSubType = 'เลือกประเภทโรงเรียน';
    if (total <= 0) e.total = 'ระบุจำนวนผู้เข้าชม';
    setErrors(e);
    return Object.keys(e).length === 0;
  };

  const handleSave = () => {
    if (!validate()) return;
    onSave({ ...form, id: form.id || `B-${Date.now().toString().slice(-7)}`, round: selectedRounds[0], rounds: selectedRounds });
  };

  // Disable already-taken rounds when creating
  const isRoundDisabled = (slot) => {
    if (initialLockedRounds.includes(slot)) return false;
    if (isEdit && initial.round === slot && initial.date === form.date) return false;
    return dateTakenRounds.includes(slot);
  };

  return (
    <Modal open={open} onClose={onClose} width={960}>
      <div className="modal-head">
        <div>
          <div className="modal-eyebrow">{isEdit ? 'รายการจองที่บันทึกไว้' : 'จองรอบเข้าชม'}</div>
          <h2 className="modal-title">
            {isEdit ? initial.id : 'รายการจองใหม่'}
            {form.date && <span className="modal-date"> · {fmtThaiDateLong(form.date)}</span>}
          </h2>
        </div>
        <button className="icon-btn" onClick={onClose}><Icon name="x" size={20} /></button>
      </div>

      {isEdit && (
        <div className="booking-tabs" role="tablist" aria-label="โหมดรายการจอง">
          <button type="button"
                  role="tab"
                  aria-selected={activeTab === 'view'}
                  className={`booking-tab ${activeTab === 'view' ? 'is-active' : ''}`}
                  onClick={() => setActiveTab('view')}>
            <Icon name="eye" size={16}/> ดูข้อมูล
          </button>
          <button type="button"
                  role="tab"
                  aria-selected={activeTab === 'edit'}
                  className={`booking-tab ${activeTab === 'edit' ? 'is-active' : ''}`}
                  onClick={() => setActiveTab('edit')}>
            <Icon name="pencil" size={16}/> แก้ไข
          </button>
        </div>
      )}

      {isEdit && activeTab === 'view' ? (
        <>
          <BookingDetailView booking={initial} />
          <div className="modal-foot">
            <div className="foot-left"></div>
            <div className="foot-right">
              <button className="btn btn-ghost" onClick={onClose}>ปิด</button>
              <button className="btn btn-primary" onClick={() => setActiveTab('edit')}>
                <Icon name="pencil" size={16}/> แก้ไขรายการ
              </button>
            </div>
          </div>
        </>
      ) : (
        <>
      <div className="modal-body">
        {/* Section 1 — Customer */}
        <section className="form-section">
          <h3 className="form-section-title">ข้อมูลลูกค้า</h3>
          <div className="grid-2">
            <Field label="ชื่อลูกค้า / องค์กร" error={errors.customerName} required>
              <input value={form.customerName} onChange={e => set('customerName', e.target.value)}
                     placeholder="เช่น บริษัท ABC จำกัด"/>
            </Field>
            <Field label="เลขประจำตัวผู้เสียภาษี (Tax ID)">
              <input value={form.taxId} onChange={e => set('taxId', e.target.value)}
                     placeholder="0105xxxxxxxxx" inputMode="numeric"/>
            </Field>
          </div>
          <Field label="ที่อยู่">
            <textarea rows={2} value={form.address} onChange={e => set('address', e.target.value)}
                      placeholder="ที่อยู่สำหรับออกใบเสร็จ / ใบกำกับภาษี"/>
          </Field>
          <div className="grid-2">
            <Field label="ผู้ประสานงาน" error={errors.contactName} required>
              <input value={form.contactName} onChange={e => set('contactName', e.target.value)}
                     placeholder="ชื่อ-สกุล"/>
            </Field>
            <Field label="เบอร์ติดต่อ" error={errors.contactPhone} required>
              <input value={form.contactPhone} onChange={e => set('contactPhone', e.target.value)}
                     placeholder="0xx-xxx-xxxx"/>
            </Field>
          </div>
        </section>

        {/* Section 2 — Group + Visitors */}
        <section className="form-section">
          <h3 className="form-section-title">ประเภทกรุ๊ปและจำนวนผู้เข้าชม</h3>
          <div className="grid-2">
            <div>
              <Field label="ประเภทกรุ๊ปจอง">
                <div className="seg-vert">
                  {GROUP_TYPES.map(g => (
                    <label key={g.key} className={`seg-row ${form.groupType === g.key ? 'is-on' : ''}`}>
                      <input type="radio" name="groupType" checked={form.groupType === g.key}
                             onChange={() => { set('groupType', g.key); if (g.key !== 'school') set('schoolSubType', null); }}/>
                      <span className="seg-dot"></span>
                      <span>{g.label}</span>
                    </label>
                  ))}
                </div>
              </Field>
              {form.groupType === 'school' && (
                <Field label="ประเภทโรงเรียน" error={errors.schoolSubType}>
                  <div className="check-row">
                    {['รัฐ', 'เอกชน', 'นานาชาติ'].map(s => (
                      <label key={s} className={`check-chip ${form.schoolSubType === s ? 'is-on' : ''}`}>
                        <input type="checkbox" checked={form.schoolSubType === s}
                               onChange={() => set('schoolSubType', form.schoolSubType === s ? null : s)}/>
                        <span>{s}</span>
                      </label>
                    ))}
                  </div>
                </Field>
              )}
            </div>
            <div>
              <Field label="จำนวนผู้เข้าชม" error={errors.total}>
                <div className="visitor-row">
                  <div className="num-stepper">
                    <span className="stepper-label">ผู้ใหญ่</span>
                    <button onClick={() => set('adults', Math.max(0, Number(form.adults) - 1))}>−</button>
                    <input type="number" value={form.adults} onChange={e => set('adults', Math.max(0, Number(e.target.value) || 0))}/>
                    <button onClick={() => set('adults', Number(form.adults) + 1)}>+</button>
                  </div>
                  <div className="num-stepper">
                    <span className="stepper-label">เด็ก</span>
                    <button onClick={() => set('kids', Math.max(0, Number(form.kids) - 1))}>−</button>
                    <input type="number" value={form.kids} onChange={e => set('kids', Math.max(0, Number(e.target.value) || 0))}/>
                    <button onClick={() => set('kids', Number(form.kids) + 1)}>+</button>
                  </div>
                </div>
                <div className="visitor-total">รวมทั้งหมด <b>{total}</b> คน</div>
              </Field>
              <Field label="ผู้ขาย">
                <div className="seg-h">
                  {SELLERS.map(s => (
                    <label key={s} className={`seg-pill ${form.seller === s ? 'is-on' : ''}`}>
                      <input type="radio" name="seller" checked={form.seller === s} onChange={() => set('seller', s)}/>
                      {s}
                    </label>
                  ))}
                </div>
              </Field>
            </div>
          </div>
        </section>

        {/* Section 3 — Date + round */}
        <section className="form-section">
          <h3 className="form-section-title">วันที่และรอบเข้าชม</h3>
          <div className="grid-2">
            <Field label="วันที่เข้าใช้บริการ" error={errors.date} required>
              <input type="date" value={form.date}
                     onChange={e => setForm(f => ({ ...f, date: e.target.value, round: '', rounds: [] }))}
                     disabled={!!lockedDate}/>
            </Field>
            <Field label="รอบเข้าชม" error={errors.round} required>
              {hasLockedRounds && (
                <div className="selected-round-summary">
                  {selectedRounds.map(round => <span key={round} className="selected-round-chip">{round}</span>)}
                  <span className="selected-round-note">เลือกไว้ {selectedRounds.length} รอบจากปฏิทิน</span>
                </div>
              )}
              <div className="round-grid">
                {TIME_SLOTS.map(t => {
                  const disabled = isRoundDisabled(t);
                  return (
                    <button key={t} type="button"
                            className={`round-pill ${selectedRounds.includes(t) ? 'is-on' : ''} ${disabled ? 'is-disabled' : ''}`}
                            onClick={() => !disabled && toggleRound(t)} disabled={disabled}>
                      {t}
                    </button>
                  );
                })}
              </div>
              <div className="hint">เลือกได้หลายรอบ ระบบจะสร้าง 1 รายการจองต่อ 1 รอบ · รอบที่จองแล้วจะถูกปิด</div>
            </Field>
          </div>
        </section>

        {/* Section 4 — Booking note */}
        <section className="form-section">
          <h3 className="form-section-title">หมายเหตุการจอง</h3>
          <Field label="หมายเหตุ">
            <textarea rows={4} value={form.note} onChange={e => set('note', e.target.value)}
                      placeholder="ข้อมูลเพิ่มเติม เช่น ภาษา, อาหาร, การเดินทาง, จุดนัดพบ, คำขอพิเศษ"/>
          </Field>
        </section>

        {/* Section 5 — Stations */}
        <section className="form-section">
          <h3 className="form-section-title">ฐานการบรรยาย <span className="muted">ไม่บังคับ · เลือกแล้ว {form.stations.length}/8</span></h3>
          <div className="station-grid">
            {STATIONS.map((name, i) => (
              <label key={i} className={`station-card ${form.stations.includes(i) ? 'is-on' : ''}`}>
                <input type="checkbox" checked={form.stations.includes(i)} onChange={() => toggleStation(i)}/>
                <span className="station-no">{i + 1}</span>
                <span className="station-name">{name}</span>
                <span className="station-check"><Icon name="check" size={14}/></span>
              </label>
            ))}
          </div>
        </section>

        {/* Section 6 — Payment */}
        <section className="form-section">
          <h3 className="form-section-title">สถานะการโอนเงิน</h3>
          <Field label="สถานะการโอนเงิน">
            <div className="seg-vert payment-options">
              {PAYMENT_STATUSES.map(p => (
                <label key={p.key} className={`seg-row ${form.payment === p.key ? 'is-on' : ''}`}>
                  <input type="radio" name="payment" checked={form.payment === p.key} onChange={() => set('payment', p.key)}/>
                  <span className="seg-dot" style={{ background: form.payment === p.key ? p.dot : 'transparent', borderColor: p.dot }}></span>
                  <span style={{ color: p.color, fontWeight: 600 }}>{p.label}</span>
                </label>
              ))}
            </div>
          </Field>
        </section>
      </div>

      <div className="modal-foot">
        <div className="foot-left">
          {isEdit && onDelete && (
            <button className="btn btn-ghost btn-danger" onClick={() => {
              if (confirm(`ยืนยันลบรายการ ${initial.id}?`)) onDelete(initial.id);
            }}>
              <Icon name="trash" size={16}/> ลบรายการ
            </button>
          )}
        </div>
        <div className="foot-right">
          <button className="btn btn-ghost" onClick={onClose}>ยกเลิก</button>
          <button className="btn btn-primary" onClick={handleSave}>
            {isEdit ? 'บันทึกการแก้ไข' : 'ยืนยันการจอง'}
          </button>
        </div>
      </div>
        </>
      )}
    </Modal>
  );
}

function BookingDetailView({ booking }) {
  const total = (Number(booking.adults) || 0) + (Number(booking.kids) || 0);
  const stations = Array.isArray(booking.stations) ? booking.stations.filter(i => STATIONS[i]) : [];

  return (
    <div className="modal-body booking-view">
      <section className="booking-view-hero">
        <div>
          <div className="booking-view-id">{booking.id}</div>
          <h3>{booking.customerName || 'ไม่ระบุชื่อลูกค้า'}</h3>
          <div className="booking-view-meta">
            <span><Icon name="calendar" size={14}/> {fmtThaiDateLong(booking.date)}</span>
            <span><Icon name="clock" size={14}/> {booking.round}</span>
            <span><Icon name="users" size={14}/> {total} คน</span>
          </div>
        </div>
        <PaymentBadge status={booking.payment} size="md"/>
      </section>

      <section className="detail-section">
        <h3 className="form-section-title">ข้อมูลการจอง</h3>
        <div className="detail-grid">
          <DetailItem label="ประเภทกรุ๊ป">
            <GroupChip type={booking.groupType} sub={booking.schoolSubType}/>
          </DetailItem>
          <DetailItem label="ผู้ขาย">{booking.seller || '-'}</DetailItem>
          <DetailItem label="ผู้ใหญ่">{booking.adults || 0} คน</DetailItem>
          <DetailItem label="เด็ก">{booking.kids || 0} คน</DetailItem>
        </div>
      </section>

      <section className="detail-section">
        <h3 className="form-section-title">ข้อมูลติดต่อ</h3>
        <div className="detail-grid">
          <DetailItem label="ผู้ประสานงาน">{booking.contactName || '-'}</DetailItem>
          <DetailItem label="เบอร์ติดต่อ">{booking.contactPhone || '-'}</DetailItem>
          <DetailItem label="Tax ID">{booking.taxId || '-'}</DetailItem>
          <DetailItem label="ที่อยู่">{booking.address || '-'}</DetailItem>
        </div>
      </section>

      <section className="detail-section">
        <h3 className="form-section-title">ฐานการบรรยาย</h3>
        {stations.length ? (
          <div className="detail-stations">
            {stations.map(i => (
              <span key={i} className="station-tag">{i + 1}. {STATIONS[i]}</span>
            ))}
          </div>
        ) : (
          <div className="detail-empty">ไม่ได้เลือกฐานการบรรยาย</div>
        )}
      </section>

      <section className="detail-section">
        <h3 className="form-section-title">หมายเหตุ</h3>
        <div className="detail-note">{booking.note || 'ไม่มีหมายเหตุ'}</div>
      </section>
    </div>
  );
}

function DetailItem({ label, children }) {
  return (
    <div className="detail-item">
      <div className="detail-label">{label}</div>
      <div className="detail-value">{children}</div>
    </div>
  );
}

function Field({ label, error, required, children }) {
  return (
    <div className={`field ${error ? 'has-error' : ''}`}>
      {label && <label className="field-label">{label}{required && <span className="req">*</span>}</label>}
      {children}
      {error && <div className="field-error">{error}</div>}
    </div>
  );
}

Object.assign(window, { BookingForm, Field });
