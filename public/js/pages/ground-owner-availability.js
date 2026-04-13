/**
 * Ground Owner – Availability Management
 */

let currentFacilityId = null;
let originalSchedule  = [];

const ALL_DAYS    = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
const DAY_SHORT   = { Monday:'MON', Tuesday:'TUE', Wednesday:'WED', Thursday:'THU', Friday:'FRI', Saturday:'SAT', Sunday:'SUN' };
const REASON_META = {
    maintenance: { label:'Maintenance',   cls:'maintenance', icon:'<i class="fas fa-wrench"></i>' },
    personal:    { label:'Personal Use',  cls:'personal',    icon:'<i class="fas fa-user"></i>' },
    event:       { label:'Private Event', cls:'event',       icon:'<i class="fas fa-bullhorn"></i>' },
    weather:     { label:'Weather',       cls:'weather',     icon:'' },
    other:       { label:'Other',         cls:'other',       icon:'<i class="fas fa-thumbtack"></i>' },
};

//  Init 

document.addEventListener('DOMContentLoaded', () => {
    loadFacilities();

    document.getElementById('groundSelector').addEventListener('change', e => {
        currentFacilityId = e.target.value || null;
        if (currentFacilityId) {
            document.getElementById('noGroundNotice').style.display = 'none';
            document.getElementById('availContent').style.display   = 'block';
            loadAll();
        } else {
            document.getElementById('noGroundNotice').style.display = 'block';
            document.getElementById('availContent').style.display   = 'none';
        }
    });
});

async function loadAll() {
    await Promise.all([ loadWeeklySchedule(), loadBlockedDates() ]);
}

//  Facilities 

async function loadFacilities() {
    try {
        const res  = await fetch((window.BASE_URL||'')+'/api/ground-owner/facilities');
        const data = await res.json();
        const sel  = document.getElementById('groundSelector');
        const list = data.facilities || data.data || data || [];

        list.forEach(f => {
            const o = document.createElement('option');
            o.value = f.id;
            o.textContent = f.name;
            sel.appendChild(o);
        });

        if (list.length === 1) {
            sel.value = list[0].id;
            sel.dispatchEvent(new Event('change'));
        }
    } catch (err) {
        console.error('Failed to load facilities:', err);
    }
}

//  Tabs 

function switchTab(tab) {
    document.querySelectorAll('.av-tab-btn').forEach(btn =>
        btn.classList.toggle('active', btn.dataset.tab === tab)
    );
    document.getElementById('tab-weekly').style.display  = tab === 'weekly'  ? 'block' : 'none';
    document.getElementById('tab-blocked').style.display = tab === 'blocked' ? 'block' : 'none';
}

//  Weekly Schedule 

async function loadWeeklySchedule() {
    if (!currentFacilityId) return;
    showSkeletonRows('weeklyScheduleTable', 7);
    try {
        const res  = await fetch(`${window.BASE_URL||""}/api/ground-owner/availability?facility_id=${currentFacilityId}`);
        const data = await res.json();
        if (!data.success) throw new Error(data.message);
        originalSchedule = JSON.parse(JSON.stringify(data.schedule));
        renderScheduleRows(data.schedule);
        updateStatCards(data.schedule, null);
    } catch (err) {
        document.getElementById('weeklyScheduleTable').innerHTML =
            `<div class="av-empty-state"><div class="av-empty-icon" style="background:#fee2e2;color:#ef4444;"><i class="fas fa-exclamation-triangle"></i></div><h4>Failed to load</h4><p>${err.message}</p></div>`;
    }
}

function renderScheduleRows(schedule) {
    const container = document.getElementById('weeklyScheduleTable');
    let html = '<div class="av-day-rows">';

    schedule.forEach(row => {
        const isOn    = parseInt(row.is_available) === 1;
        const dayName = row.day_of_week;
        const dayId   = dayName.toLowerCase();
        const dis     = isOn ? '' : 'disabled';
        const rowCls  = isOn ? 'av-open' : 'av-closed';
        const openVal  = (row.opening_time || '08:00:00').substring(0, 5);
        const closeVal = (row.closing_time || '22:00:00').substring(0, 5);

        html += `
        <div class="av-day-row ${rowCls}" id="row-${dayId}">
            <div class="av-day-name-col">
                <div class="av-day-name">${dayName}</div>
                <div class="av-day-short">${DAY_SHORT[dayName] || ''}</div>
            </div>

            <div>
                <span class="av-day-status-pill" id="pill-${dayId}">
                    <span class="av-dot"></span>
                    ${isOn ? 'Open' : 'Closed'}
                </span>
            </div>

            <div class="av-day-fields">
                <div class="av-field-group">
                    <span class="av-field-label">Opens</span>
                    <input type="time" id="open-${dayId}" value="${openVal}" ${dis}>
                </div>
                <span class="av-time-separator">→</span>
                <div class="av-field-group">
                    <span class="av-field-label">Closes</span>
                    <input type="time" id="close-${dayId}" value="${closeVal}" ${dis}>
                </div>
                <div class="av-field-group">
                    <span class="av-field-label">Slot</span>
                    <select id="slot-${dayId}" ${dis} style="width:110px;">
                        ${slotOptions(row.slot_duration)}
                    </select>
                </div>
                <div class="av-field-group">
                    <span class="av-field-label">Buffer</span>
                    <select id="buf-${dayId}" ${dis} style="width:110px;">
                        ${bufferOptions(row.buffer_time)}
                    </select>
                </div>
            </div>

            <div class="av-toggle-col">
                <label class="av-switch" title="${isOn ? 'Click to close' : 'Click to open'}">
                    <input type="checkbox" id="toggle-${dayId}" ${isOn ? 'checked' : ''}
                           onchange="toggleDay('${dayId}', this.checked)">
                    <span class="av-switch-slider"></span>
                </label>
            </div>
        </div>`;
    });

    html += '</div>';
    container.innerHTML = html;
}

function slotOptions(current) {
    const opts = [[30,'30 min'],[60,'1 hour'],[90,'1.5 hrs'],[120,'2 hours'],[180,'3 hours']];
    return opts.map(([v,l]) => `<option value="${v}" ${(!current&&v===60)||current==v?'selected':''}>${l}</option>`).join('');
}
function bufferOptions(current) {
    const opts = [[0,'None'],[15,'15 min'],[30,'30 min'],[60,'1 hour']];
    return opts.map(([v,l]) => `<option value="${v}" ${(!current&&v===0)||current==v?'selected':''}>${l}</option>`).join('');
}

function toggleDay(dayId, isOn) {
    const row   = document.getElementById(`row-${dayId}`);
    const pill  = document.getElementById(`pill-${dayId}`);
    row.classList.toggle('av-open',  isOn);
    row.classList.toggle('av-closed', !isOn);
    if (pill) pill.innerHTML = `<span class="av-dot"></span>${isOn ? 'Open' : 'Closed'}`;
    ['open','close','slot','buf'].forEach(f => {
        const el = document.getElementById(`${f}-${dayId}`);
        if (el) el.disabled = !isOn;
    });
}

function resetSchedule() {
    if (originalSchedule.length) {
        renderScheduleRows(originalSchedule);
        updateStatCards(originalSchedule, null);
        showToast('Reset to last saved schedule', 'info');
    }
}

async function saveSchedule() {
    if (!currentFacilityId) return;
    const btn = document.getElementById('saveScheduleBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…';

    const schedule = ALL_DAYS.map(day => {
        const d = day.toLowerCase();
        return {
            day_of_week:   day,
            is_available:  document.getElementById(`toggle-${d}`)?.checked ? 1 : 0,
            opening_time:  document.getElementById(`open-${d}`)?.value  || '08:00',
            closing_time:  document.getElementById(`close-${d}`)?.value || '22:00',
            slot_duration: parseInt(document.getElementById(`slot-${d}`)?.value)  || 60,
            buffer_time:   parseInt(document.getElementById(`buf-${d}`)?.value)   || 0,
        };
    });

    try {
        const res  = await fetch((window.BASE_URL||'')+'/api/ground-owner/availability', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ facility_id: currentFacilityId, schedule })
        });
        const data = await res.json();
        if (data.success) {
            originalSchedule = JSON.parse(JSON.stringify(schedule));
            updateStatCards(schedule, null);
            showToast('Schedule saved!', 'success');
        } else {
            showToast(data.message || 'Failed to save', 'error');
        }
    } catch {
        showToast('Network error. Please try again.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> Save Schedule';
    }
}

//  Blocked Dates 

async function loadBlockedDates() {
    if (!currentFacilityId) return;
    showSkeletonRows('blockedDatesList', 3);
    try {
        const res  = await fetch(`${window.BASE_URL||""}/api/ground-owner/blocked-dates?facility_id=${currentFacilityId}`);
        const data = await res.json();
        if (!data.success) throw new Error(data.message);
        renderBlockedCards(data.blocked_dates || []);
        updateStatCards(null, data.blocked_dates || []);
    } catch (err) {
        document.getElementById('blockedDatesList').innerHTML =
            `<div class="av-empty-state"><div class="av-empty-icon" style="background:#fee2e2;color:#ef4444;"><i class="fas fa-exclamation-triangle"></i></div><h4>Failed to load</h4><p>${err.message}</p></div>`;
    }
}

function renderBlockedCards(blocks) {
    const container = document.getElementById('blockedDatesList');
    const badge     = document.getElementById('blockedCount');
    const count     = blocks.filter(b => new Date(b.end_date) >= new Date()).length;

    if (badge) {
        badge.textContent    = count;
        badge.style.display  = count > 0 ? 'inline-block' : 'none';
    }

    if (blocks.length === 0) {
        container.innerHTML = `
            <div class="av-empty-state av-empty-blocks">
                <div class="av-empty-icon"><i class="fas fa-calendar-check"></i></div>
                <h4>No blocked dates</h4>
                <p>This ground has no blocked periods. Click "Block Dates" to add restrictions.</p>
                <button class="av-btn av-btn-primary" onclick="openBlockModal()">
                    <i class="fas fa-plus"></i> Block Dates
                </button>
            </div>`;
        return;
    }

    const future = blocks.filter(b => new Date(b.end_date) >= new Date());
    const past   = blocks.filter(b => new Date(b.end_date) <  new Date());

    let html = '';
    if (future.length) {
        html += buildBlockSection('Upcoming Blocks', future, false);
    }
    if (past.length) {
        html += buildBlockSection('Past Blocks', past, true);
    }
    container.innerHTML = html;
}

function buildBlockSection(title, blocks, isPast) {
    let html = `
        <div style="margin-bottom:${isPast?'0':'24px'};">
            <h4 style="font-size:12px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;margin:0 0 12px;">${title}</h4>
            <div class="av-blocks-grid">`;

    blocks.forEach(b => {
        const meta    = REASON_META[b.reason] || REASON_META.other;
        const sameDay = b.start_date === b.end_date;
        const dateStr = sameDay
            ? formatDate(b.start_date)
            : `${formatDate(b.start_date)} → ${formatDate(b.end_date)}`;
        const timeStr = (b.start_time && b.end_time)
            ? `${b.start_time.substring(0,5)} – ${b.end_time.substring(0,5)}`
            : 'All day';

        html += `
            <div class="av-block-card${isPast?' av-past':''}">
                <div class="av-block-header">
                    <div>
                        <div class="av-block-dates">
                            ${sameDay ? '' : '<i class="fas fa-calendar-alt" style="font-size:11px;color:#9ca3af;margin-right:4px;"></i>'}
                            ${dateStr}
                            ${isPast ? '<span class="av-past-badge" style="display:inline-block;margin-left:6px;">Past</span>' : ''}
                        </div>
                    </div>
                    <button class="av-block-remove" onclick="removeBlock(${b.id})" title="Remove">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="av-block-meta">
                    <span class="av-reason-chip ${meta.cls}">${meta.icon} ${meta.label}</span>
                    <span class="av-time-chip"><i class="fas fa-clock" style="font-size:9px;"></i> ${timeStr}</span>
                </div>
                ${b.notes ? `<div class="av-block-notes">${escHtml(b.notes)}</div>` : ''}
            </div>`;
    });

    html += '</div></div>';
    return html;
}

//  Add Block Modal 

function openBlockModal() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('newBlockStart').value    = today;
    document.getElementById('newBlockEnd').value      = today;
    document.getElementById('newBlockStartTime').value = '';
    document.getElementById('newBlockEndTime').value   = '';
    document.getElementById('newBlockReason').value    = '';
    document.getElementById('newBlockNotes').value     = '';
    document.getElementById('blockModal').style.display = 'flex';
}

function closeBlockModal() {
    document.getElementById('blockModal').style.display = 'none';
}

function handleModalBackdrop(e) {
    if (e.target === document.getElementById('blockModal')) closeBlockModal();
}

async function submitBlock() {
    const startDate = document.getElementById('newBlockStart').value;
    const endDate   = document.getElementById('newBlockEnd').value;
    const reason    = document.getElementById('newBlockReason').value;

    if (!startDate || !endDate || !reason) {
        showToast('Please fill Start Date, End Date, and Reason', 'error');
        return;
    }
    if (endDate < startDate) {
        showToast('End date must be on or after start date', 'error');
        return;
    }

    const btn = document.getElementById('submitBlockBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…';

    try {
        const res  = await fetch((window.BASE_URL||'')+'/api/ground-owner/blocked-dates', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                facility_id: currentFacilityId,
                start_date:  startDate,
                end_date:    endDate,
                start_time:  document.getElementById('newBlockStartTime').value || null,
                end_time:    document.getElementById('newBlockEndTime').value   || null,
                reason,
                notes: document.getElementById('newBlockNotes').value || null
            })
        });
        const data = await res.json();
        if (data.success) {
            closeBlockModal();
            showToast('Dates blocked successfully!', 'success');
            loadBlockedDates();
        } else {
            showToast(data.message || 'Failed to block dates', 'error');
        }
    } catch {
        showToast('Network error. Please try again.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-ban"></i> Block Dates';
    }
}

async function removeBlock(blockId) {
    if (!confirm('Remove this blocked date?')) return;
    try {
        const res  = await fetch(`${window.BASE_URL||""}/api/ground-owner/blocked-dates/${blockId}`, { method: 'DELETE' });
        const data = await res.json();
        if (data.success) {
            showToast('Blocked date removed', 'success');
            loadBlockedDates();
        } else {
            showToast(data.message || 'Failed to remove', 'error');
        }
    } catch {
        showToast('Network error. Please try again.', 'error');
    }
}

//  Helpers 

function updateStatCards(schedule, blocks) {
    if (schedule) {
        const open   = schedule.filter(d => parseInt(d.is_available) === 1).length;
        const closed = 7 - open;
        const s = document.getElementById('statOpenDays');
        const c = document.getElementById('statClosedDays');
        if (s) s.textContent = open;
        if (c) c.textContent = closed;
    }
    if (blocks !== null) {
        const upcoming = (blocks || []).filter(b => new Date(b.end_date) >= new Date()).length;
        const el = document.getElementById('statBlockedPeriods');
        if (el) el.textContent = upcoming;
    }
}

function showSkeletonRows(containerId, count) {
    const container = document.getElementById(containerId);
    if (!container) return;
    let html = '<div class="av-loading-rows">';
    for (let i = 0; i < count; i++) html += '<div class="av-skel-row"></div>';
    html += '</div>';
    container.innerHTML = html;
}

function formatDate(str) {
    if (!str) return '';
    const d = new Date(str + 'T00:00:00');
    return d.toLocaleDateString('en-US', { weekday:'short', month:'short', day:'numeric', year:'numeric' });
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function showToast(message, type = 'info') {
    const colors = { success:'#10b981', error:'#ef4444', info:'#3b82f6' };
    const icons  = { success:'check-circle', error:'exclamation-circle', info:'info-circle' };
    const toast  = document.createElement('div');
    toast.style.cssText = `
        position:fixed;top:24px;right:24px;
        background:${colors[type]||colors.info};
        color:#fff;padding:13px 18px;border-radius:11px;
        box-shadow:0 8px 24px rgba(0,0,0,.18);
        z-index:10000;font-size:14px;font-weight:500;
        display:flex;align-items:center;gap:9px;
        max-width:340px;animation:slideIn .3s ease;
    `;
    toast.innerHTML = `<i class="fas fa-${icons[type]||icons.info}"></i><span>${escHtml(message)}</span>`;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'slideOut .3s ease forwards';
        setTimeout(() => toast.remove(), 300);
    }, 3200);
}
