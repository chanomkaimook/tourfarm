// Main app — routing, state, persistence
const { useState: useStateA, useEffect: useEffectA, useMemo: useMemoA } = React;

function App() {
  const firebaseServices = useMemoA(() => (
    window.initFirebaseServices
      ? window.initFirebaseServices()
      : { configured: false, error: 'ไม่พบไฟล์ firebase-config.js' }
  ), []);
  const firebaseReady = firebaseServices.configured;
  const [page, setPage] = useStateA(() => localStorage.getItem('fb_page') || 'dashboard');
  const [bookings, setBookings] = useStateA(() => {
    try {
      const stored = localStorage.getItem('fb_bookings');
      if (stored) return JSON.parse(stored);
    } catch (e) {}
    return SAMPLE_BOOKINGS;
  });
  const [formOpen, setFormOpen] = useStateA(false);
  const [formInitial, setFormInitial] = useStateA(null);
  const [lockedDate, setLockedDate] = useStateA(null);
  const [lockedRound, setLockedRound] = useStateA(null);
  const [lockedRounds, setLockedRounds] = useStateA([]);
  const [lockedTaken, setLockedTaken] = useStateA([]);
  const [theme, setTheme] = useStateA(() => localStorage.getItem('fb_theme') || 'light');
  const [authReady, setAuthReady] = useStateA(!firebaseReady);
  const [user, setUser] = useStateA(null);
  const [authLoading, setAuthLoading] = useStateA('');
  const [authError, setAuthError] = useStateA('');
  const [dataReady, setDataReady] = useStateA(!firebaseReady);
  const [dbError, setDbError] = useStateA('');
  const [toast, setToast] = useStateA(null);

  useEffectA(() => { localStorage.setItem('fb_page', page); }, [page]);
  useEffectA(() => {
    if (!firebaseReady) localStorage.setItem('fb_bookings', JSON.stringify(bookings));
  }, [bookings, firebaseReady]);
  useEffectA(() => {
    document.documentElement.dataset.theme = theme;
    localStorage.setItem('fb_theme', theme);
  }, [theme]);

  useEffectA(() => {
    if (!firebaseReady) return;
    const unsub = firebaseServices.auth.onAuthStateChanged(nextUser => {
      setUser(nextUser);
      setAuthReady(true);
      setAuthError('');
      if (!nextUser) setDataReady(false);
    });
    return () => unsub();
  }, [firebaseReady, firebaseServices]);

  useEffectA(() => {
    if (!firebaseReady || !user) return;
    setDataReady(false);
    setDbError('');
    const unsub = firebaseServices.bookingsRef
      .orderBy('date', 'asc')
      .onSnapshot(snapshot => {
        const rows = snapshot.docs
          .map(doc => ({ id: doc.id, ...doc.data() }))
          .sort((a, b) => a.date.localeCompare(b.date) || a.round.localeCompare(b.round));
        setBookings(rows);
        setDataReady(true);
      }, err => {
        setDbError(err.message || 'โหลดข้อมูลจาก Firestore ไม่สำเร็จ');
        setDataReady(true);
      });
    return () => unsub();
  }, [firebaseReady, user?.uid, firebaseServices]);

  // Stats for sidebar (today)
  const todayISO = toISO(new Date(2026, 4, 24));
  const sidebarStats = useMemoA(() => {
    const today = bookings.filter(b => b.date === todayISO && b.payment !== 'cancelled');
    return {
      todayRounds: today.length,
      todayVisitors: today.reduce((s,b) => s + b.adults + b.kids, 0),
      todayDeposit: today.filter(b => b.payment === 'deposit').length,
    };
  }, [bookings, todayISO]);

  const showToast = (msg) => {
    setToast(msg);
    setTimeout(() => setToast(null), 2500);
  };

  const login = async (email, password) => {
    if (!firebaseReady) return;
    setAuthLoading('email');
    setAuthError('');
    try {
      await firebaseServices.auth.signInWithEmailAndPassword(email, password);
    } catch (err) {
      setAuthError(err.message || 'เข้าสู่ระบบไม่สำเร็จ');
    } finally {
      setAuthLoading('');
    }
  };

  const loginWithGoogle = async () => {
    if (!firebaseReady) return;
    setAuthLoading('google');
    setAuthError('');
    try {
      const provider = new firebase.auth.GoogleAuthProvider();
      provider.setCustomParameters({ prompt: 'select_account' });
      await firebaseServices.auth.signInWithPopup(provider);
    } catch (err) {
      if (['auth/popup-blocked', 'auth/operation-not-supported-in-this-environment'].includes(err.code)) {
        try {
          const provider = new firebase.auth.GoogleAuthProvider();
          provider.setCustomParameters({ prompt: 'select_account' });
          await firebaseServices.auth.signInWithRedirect(provider);
          return;
        } catch (redirectErr) {
          setAuthError(redirectErr.message || 'เข้าสู่ระบบด้วย Gmail ไม่สำเร็จ');
        }
      } else if (err.code !== 'auth/popup-closed-by-user') {
        setAuthError(err.message || 'เข้าสู่ระบบด้วย Gmail ไม่สำเร็จ');
      }
    } finally {
      setAuthLoading('');
    }
  };

  const signOut = async () => {
    if (!firebaseReady) return;
    await firebaseServices.auth.signOut();
    showToast('ออกจากระบบแล้ว');
  };

  const stripTransientBookingFields = (booking) => {
    const { rounds, ...single } = booking;
    return single;
  };

  // Form handlers
  const openCreate = (date = null, round = null, taken = []) => {
    const rounds = Array.isArray(round) ? round : (round ? [round] : []);
    setFormInitial(null);
    setLockedDate(date);
    setLockedRound(rounds[0] || null);
    setLockedRounds(rounds);
    setLockedTaken(taken);
    setFormOpen(true);
  };
  const openEdit = (b) => {
    setFormInitial(b);
    // compute taken rounds for that date (excluding self)
    const taken = bookings
      .filter(x => x.date === b.date && x.id !== b.id && x.payment !== 'cancelled')
      .map(x => x.round);
    setLockedDate(null);
    setLockedRound(null);
    setLockedRounds([]);
    setLockedTaken(taken);
    setFormOpen(true);
  };
  const handleSave = async (data) => {
    const selectedRounds = Array.isArray(data.rounds) && data.rounds.length ? data.rounds : [data.round];
    const savedRounds = selectedRounds.length;
    try {
      if (firebaseReady && user) {
        const now = firebase.firestore.FieldValue.serverTimestamp();
        if (formInitial) {
          const single = stripTransientBookingFields(data);
          await firebaseServices.bookingsRef.doc(single.id).set({
            ...single,
            updatedAt: now,
            updatedBy: user.email || user.uid,
          });
        } else {
          const batch = firebaseServices.db.batch();
          const baseId = data.id || `B-${Date.now().toString().slice(-7)}`;
          selectedRounds.forEach((round, i) => {
            const docId = selectedRounds.length > 1 ? `${baseId}-${String(i + 1).padStart(2, '0')}` : baseId;
            const single = stripTransientBookingFields({ ...data, id: docId, round });
            batch.set(firebaseServices.bookingsRef.doc(docId), {
              ...single,
              createdAt: now,
              createdBy: user.email || user.uid,
              updatedAt: now,
              updatedBy: user.email || user.uid,
            });
          });
          await batch.commit();
        }
      } else {
        setBookings(prev => {
          const idx = prev.findIndex(b => b.id === data.id);
          if (idx >= 0) {
            const single = stripTransientBookingFields(data);
            const next = [...prev]; next[idx] = single;
            return next;
          }
          if (selectedRounds.length > 1) {
            const baseId = data.id || `B-${Date.now().toString().slice(-7)}`;
            const created = selectedRounds.map((round, i) => stripTransientBookingFields({
              ...data,
              id: `${baseId}-${String(i + 1).padStart(2, '0')}`,
              round,
            }));
            return [...prev, ...created];
          }
          return [...prev, stripTransientBookingFields({ ...data, round: selectedRounds[0] })];
        });
      }
      setFormOpen(false);
      showToast(formInitial
        ? `บันทึกการแก้ไข ${data.id} แล้ว`
        : `เพิ่มรายการจอง ${savedRounds} รอบแล้ว`);
    } catch (err) {
      showToast(err.message || 'บันทึกรายการไม่สำเร็จ');
    }
  };
  const handleDelete = async (id) => {
    try {
      if (firebaseReady && user) {
        await firebaseServices.bookingsRef.doc(id).delete();
      } else {
        setBookings(prev => prev.filter(b => b.id !== id));
      }
      setFormOpen(false);
      showToast(`ลบรายการ ${id} แล้ว`);
    } catch (err) {
      showToast(err.message || `ลบรายการ ${id} ไม่สำเร็จ`);
    }
  };

  // Excel export (CSV with BOM, opens in Excel)
  const exportExcel = (rows) => {
    const headers = [
      'รหัส', 'วันที่', 'รอบ', 'ชื่อลูกค้า', 'ที่อยู่', 'Tax ID',
      'ผู้ประสานงาน', 'เบอร์ติดต่อ', 'ผู้ใหญ่', 'เด็ก', 'รวมคน',
      'ประเภทกรุ๊ป', 'ประเภทย่อย', 'ผู้ขาย', 'ฐานการบรรยาย',
      'สถานะการโอน', 'หมายเหตุ'
    ];
    const data = rows.map(b => [
      b.id, b.date, b.round,
      b.customerName, b.address, b.taxId,
      b.contactName, b.contactPhone,
      b.adults, b.kids, b.adults + b.kids,
      GROUP_TYPES.find(g => g.key === b.groupType)?.label || '',
      b.schoolSubType || '', b.seller,
      b.stations.map(i => `${i+1}.${STATIONS[i]}`).join(' | '),
      PAYMENT_STATUSES.find(p => p.key === b.payment)?.label || '',
      b.note || ''
    ]);
    const csv = [headers, ...data]
      .map(r => r.map(c => {
        const s = String(c ?? '');
        return /[",\n]/.test(s) ? `"${s.replace(/"/g, '""')}"` : s;
      }).join(','))
      .join('\n');
    const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `farm-bookings-${todayISO}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    showToast(`ออกรายงาน ${rows.length} รายการเป็น Excel แล้ว`);
  };

  if (!authReady) {
    return <div className="app-loading">กำลังเตรียมระบบ...</div>;
  }

  if (!firebaseReady || !user) {
    return (
      <>
        <AuthScreen
          configured={firebaseReady}
          setupError={firebaseServices.error}
          loading={authLoading}
          error={authError}
          onLogin={login}
          onGoogleLogin={loginWithGoogle}
          theme={theme}
          onToggleTheme={() => setTheme(t => t === 'dark' ? 'light' : 'dark')}
        />
        <Toast msg={toast}/>
      </>
    );
  }

  return (
    <div className="app">
      <Sidebar page={page}
               onNavigate={setPage}
               stats={sidebarStats}
               theme={theme}
               onToggleTheme={() => setTheme(t => t === 'dark' ? 'light' : 'dark')}
               user={user}
               onSignOut={signOut}/>
      <div className="main" data-screen-label={page}>
        {!dataReady && <div className="sync-banner">กำลังโหลดข้อมูลจาก Firestore...</div>}
        {dbError && <div className="sync-banner sync-banner-error">{dbError}</div>}
        {page === 'dashboard' && (
          <DashboardPage bookings={bookings} onNavigate={setPage} onEdit={openEdit}/>
        )}
        {page === 'calendar' && (
          <CalendarPage bookings={bookings} onCreate={openCreate} onEdit={openEdit}/>
        )}
        {page === 'list' && (
          <ListPage bookings={bookings} onEdit={openEdit}
                    onCreate={() => openCreate(null, null, [])}
                    onExport={exportExcel}/>
        )}
      </div>

      <BookingForm
        open={formOpen}
        initial={formInitial}
        lockedDate={lockedDate}
        lockedRound={lockedRound}
        lockedRounds={lockedRounds}
        takenRounds={lockedTaken}
        bookings={bookings}
        onClose={() => setFormOpen(false)}
        onSave={handleSave}
        onDelete={handleDelete}
      />

      <Toast msg={toast}/>
    </div>
  );
}

ReactDOM.createRoot(document.getElementById('root')).render(<App/>);
