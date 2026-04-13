/* Ground Owner – Maintenance Dashboard (IIFE) */
(function () {
    'use strict';

    /*  state  */
    let facilities    = [];
    let currentMonth  = new Date();
    let currentTaskId = null;
    let toastTimer    = null;

    const $ = id => document.getElementById(id);

    /* ================================================================
       BOOT
    ================================================================ */
    document.addEventListener('DOMContentLoaded', () => {
        loadFacilities().then(() => {
            loadStats();
            loadTasks();
            loadInspections();
            renderCalendar();
        });
        bindControls();
        bindModals();
    });

    /* ================================================================
       API
    ================================================================ */
    async function api(url, opts = {}) {
        const r = await fetch(url, {
            headers: { 'Content-Type': 'application/json' },
            ...opts
        });
        return r.json();
    }

    async function loadFacilities() {
        try {
            const data = await api('/api/ground-owner/facilities');
            if (data.success) {
                facilities = data.facilities || [];
                populateFacilityDropdowns();
            }
        } catch (e) { console.error('facilities', e); }
    }

    async function loadStats() {
        try {
            const data = await api('/api/ground-owner/maintenance/stats');
            if (data.success) renderStats(data.stats || {});
        } catch (e) { console.error('stats', e); }
    }

    async function loadTasks() {
        const list = $('mtTasksList');
        list.innerHTML = '<div class="mt-loading"><div class="mt-spinner"></div><p>Loading tasks…</p></div>';

        const priority   = $('mtPriorityFilter').value;
        const status     = $('mtStatusFilter').value;
        const facilityId = $('mtGroundFilter').value;

        let url = '/api/ground-owner/maintenance/tasks/active';
        const params = [];
        if (priority)   params.push(`priority=${priority}`);
        if (facilityId) params.push(`facility_id=${facilityId}`);
        // if a status filter is set use the full tasks endpoint for flexibility
        if (status || priority || facilityId) {
            url = '/api/ground-owner/maintenance/tasks';
            if (status) params.push(`status=${status}`);
        }
        if (params.length) url += '?' + params.join('&');

        try {
            const data = await api(url);
            if (data.success) renderTasks(data.tasks || []);
            else list.innerHTML = '<div class="mt-empty"><i class="fas fa-exclamation-circle"></i><p>Failed to load tasks.</p></div>';
        } catch (e) {
            list.innerHTML = '<div class="mt-empty"><i class="fas fa-exclamation-circle"></i><p>Network error.</p></div>';
        }
    }

    async function loadInspections() {
        try {
            const data = await api('/api/ground-owner/inspections/upcoming?limit=5');
            if (data.success) renderInspections(data.inspections || []);
        } catch (e) { console.error('inspections', e); }
    }

    async function renderCalendar() {
        const year  = currentMonth.getFullYear();
        const month = currentMonth.getMonth();
        const start = new Date(year, month, 1);
        const end   = new Date(year, month + 1, 0);

        // Update label
        $('mtCalMonth').textContent = currentMonth.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

        try {
            const data = await api(
                `/api/ground-owner/maintenance/calendar?start_date=${fmtISO(start)}&end_date=${fmtISO(end)}`
            );
            buildCalendarGrid(data.success ? data.tasks || [] : [], year, month);
        } catch (e) { buildCalendarGrid([], year, month); }
    }

    /* ================================================================
       RENDER
    ================================================================ */
    function renderStats(s) {
        $('mtActiveTasks').textContent = s.active_tasks    || 0;
        $('mtHighPri').textContent     = s.high_priority_tasks  || 0;
        $('mtMedPri').textContent      = s.medium_priority_tasks || 0;
        $('mtOverdue').textContent     = s.overdue_tasks   || 0;
        $('mtCompleted').textContent   = s.completed_month || 0;
        $('mtCompRate').textContent    = (s.completion_rate || 0) + '%';
        $('mtCost').textContent        = 'LKR ' + Number(s.monthly_cost || 0).toLocaleString();
    }

    function renderTasks(tasks) {
        const list = $('mtTasksList');
        if (!tasks.length) {
            list.innerHTML = '<div class="mt-empty"><i class="fas fa-tasks"></i><p>No active tasks. All maintenance is up to date!</p></div>';
            return;
        }
        list.innerHTML = tasks.map(t => {
            const pc = priorityColor(t.priority);
            const sc = statusColor(t.status);
            return `
            <div class="mt-task-card" style="border-left-color:${pc}" data-task-id="${t.id}">
                <div class="mt-task-row1">
                    <span class="mt-task-title">${esc(t.title)}</span>
                    <span class="mt-priority-badge" style="background:${pc}20;color:${pc}">${esc(t.priority)}</span>
                </div>
                <div class="mt-task-meta">
                    <span><i class="fas fa-map-marker-alt"></i> ${esc(t.facility_name || '—')}</span>
                    <span><i class="fas fa-calendar"></i> ${fmtDate(t.scheduled_date)}</span>
                    <span><i class="fas fa-tools"></i> ${esc(t.task_type)}</span>
                    <span class="mt-task-status" style="background:${sc}20;color:${sc}">${esc(t.status)}</span>
                </div>
            </div>`;
        }).join('');

        // click to view details
        list.querySelectorAll('.mt-task-card').forEach(card => {
            card.addEventListener('click', () => openTaskDetails(+card.dataset.taskId));
        });
    }

    function renderInspections(insp) {
        const list = $('mtInspectionsList');
        if (!insp.length) {
            list.innerHTML = '<div class="mt-empty"><i class="fas fa-clipboard-check"></i><p>No upcoming inspections.</p></div>';
            return;
        }
        list.innerHTML = insp.map(i => `
            <div class="mt-insp-card">
                <div class="mt-insp-row1">
                    <span class="mt-insp-name">${esc(i.facility_name || '—')}</span>
                    <span class="mt-insp-type">${esc(i.inspection_type)}</span>
                </div>
                <div class="mt-insp-meta">
                    <span><i class="fas fa-calendar"></i> ${fmtDate(i.inspection_date)}</span>
                    ${i.inspection_time ? ` &nbsp;<i class="fas fa-clock"></i> ${i.inspection_time.slice(0,5)}` : ''}
                    ${i.inspector ? ` &nbsp;<i class="fas fa-user"></i> ${esc(i.inspector)}` : ''}
                </div>
            </div>`).join('');
    }

    function buildCalendarGrid(tasks, year, month) {
        const container = $('mtCalendar');
        const firstDay  = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const today     = new Date();

        // group tasks by date
        const byDate = {};
        tasks.forEach(t => {
            const d = t.scheduled_date;
            if (!byDate[d]) byDate[d] = [];
            byDate[d].push(t);
        });

        let html = '';
        ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'].forEach(d => {
            html += `<div class="mt-cal-day-header">${d}</div>`;
        });

        for (let i = 0; i < firstDay; i++) html += '<div class="mt-cal-cell empty"></div>';

        for (let day = 1; day <= daysInMonth; day++) {
            const d    = new Date(year, month, day);
            const dStr = fmtISO(d);
            const isToday = d.toDateString() === today.toDateString();
            const dayTasks = byDate[dStr] || [];

            html += `<div class="mt-cal-cell${isToday ? ' today' : ''}">
                <div class="mt-cal-day-num">${day}</div>
                ${dayTasks.slice(0,2).map(t => `
                    <div class="mt-cal-task-dot ${t.status}" title="${esc(t.title)}">
                        ${esc(t.title.slice(0,14))}${t.title.length > 14 ? '…' : ''}
                    </div>`).join('')}
                ${dayTasks.length > 2 ? `<div class="mt-cal-more">+${dayTasks.length - 2} more</div>` : ''}
            </div>`;
        }

        container.innerHTML = html;
    }

    /* ================================================================
       TASK DETAILS MODAL
    ================================================================ */
    async function openTaskDetails(taskId) {
        currentTaskId = taskId;
        const modal = $('mtDetailModal');
        modal.style.display = 'flex';

        // reset
        ['dtTitle','dtGround','dtType','dtPriority','dtStatus','dtDate','dtDuration','dtEstCost','dtActCost','dtAssigned','dtDesc','dtTools'].forEach(id => {
            $(id).textContent = '—';
        });
        $('dtProgressList').innerHTML = '';

        try {
            const data = await api(`/api/ground-owner/maintenance/tasks/${taskId}`);
            if (!data.success) { toast('Failed to load task details.', 'error'); return; }

            const t = data.task;
            $('dtTitle').textContent    = t.title        || '—';
            $('dtGround').textContent   = t.facility_name || '—';
            $('dtType').textContent     = t.task_type    || '—';
            $('dtDesc').textContent     = t.description  || '—';
            $('dtTools').textContent    = t.required_tools || '—';
            $('dtAssigned').textContent = t.assigned_to  || '—';
            $('dtDate').textContent     = fmtDate(t.scheduled_date);
            $('dtDuration').textContent = t.estimated_duration ? t.estimated_duration + ' hrs' : '—';
            $('dtEstCost').textContent  = t.estimated_cost ? 'LKR ' + Number(t.estimated_cost).toLocaleString() : '—';
            $('dtActCost').textContent  = t.actual_cost   ? 'LKR ' + Number(t.actual_cost).toLocaleString()   : '—';

            // Priority + Status badges
            const pc = priorityColor(t.priority);
            const sc = statusColor(t.status);
            $('dtPriority').innerHTML = `<span style="background:${pc}20;color:${pc};padding:2px 8px;border-radius:12px;font-weight:700;font-size:12px;">${t.priority}</span>`;
            $('dtStatus').innerHTML   = `<span style="background:${sc}20;color:${sc};padding:2px 8px;border-radius:12px;font-weight:700;font-size:12px;">${t.status}</span>`;

            // Show/hide complete button
            $('mtCompleteTaskBtn').style.display = t.status === 'completed' ? 'none' : 'flex';

            // Progress updates
            const updates = t.progress_updates || [];
            $('dtProgressList').innerHTML = updates.length
                ? updates.map(u => `
                    <div class="mt-progress-item">
                        <p>${esc(u.update_text)}</p>
                        <small>${u.created_at ? fmtDate(u.created_at) : ''}</small>
                    </div>`).join('')
                : '<p style="font-size:13px;color:#9ca3af;margin:0 0 8px;">No progress updates yet.</p>';

        } catch (e) {
            toast('Network error.', 'error');
        }
    }

    /* ================================================================
       COMPLETE TASK
    ================================================================ */
    async function completeTask() {
        if (!currentTaskId) return;
        const btn = $('mtCompleteTaskBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Completing…';

        try {
            const data = await api(`/api/ground-owner/maintenance/tasks/${currentTaskId}/complete`, { method: 'PUT', body: '{}' });
            if (data.success) {
                toast('Task marked as completed!', 'success');
                closeModal('mtDetailModal');
                loadStats();
                loadTasks();
                renderCalendar();
            } else {
                toast(data.message || 'Could not complete task.', 'error');
            }
        } catch (e) {
            toast('Network error.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i> Mark Complete';
        }
    }

    /* ================================================================
       DELETE TASK
    ================================================================ */
    async function deleteTask() {
        if (!currentTaskId) return;
        if (!confirm('Delete this task? This cannot be undone.')) return;

        try {
            const data = await api(`/api/ground-owner/maintenance/tasks/${currentTaskId}`, { method: 'DELETE' });
            if (data.success) {
                toast('Task deleted.', 'info');
                closeModal('mtDetailModal');
                loadStats();
                loadTasks();
                renderCalendar();
            } else {
                toast(data.message || 'Could not delete task.', 'error');
            }
        } catch (e) {
            toast('Network error.', 'error');
        }
    }

    /* ================================================================
       EDIT TASK — opens the add/edit modal pre-filled
    ================================================================ */
    async function editTask() {
        if (!currentTaskId) return;
        closeModal('mtDetailModal');

        try {
            const data = await api(`/api/ground-owner/maintenance/tasks/${currentTaskId}`);
            if (!data.success) { toast('Could not load task.', 'error'); return; }

            const t = data.task;
            $('mtTaskModalTitle').innerHTML = '<i class="fas fa-edit"></i> Edit Maintenance Task';
            $('mtEditTaskId').value         = t.id;
            $('mtFormGround').value         = t.facility_id   || '';
            $('mtFormType').value           = t.task_type     || '';
            $('mtFormTitle').value          = t.title         || '';
            $('mtFormDesc').value           = t.description   || '';
            $('mtFormPriority').value       = t.priority      || 'medium';
            $('mtFormCategory').value       = t.category      || 'routine';
            $('mtFormDate').value           = t.scheduled_date || '';
            $('mtFormDuration').value       = t.estimated_duration || '';
            $('mtFormCost').value           = t.estimated_cost || '';
            $('mtFormAssigned').value       = t.assigned_to   || '';
            $('mtFormTools').value          = t.required_tools || '';
            $('mtFormBlock').checked        = !!+t.block_bookings;
            $('mtFormNotify').checked       = t.send_notifications === null ? true : !!+t.send_notifications;

            $('mtTaskModal').style.display = 'flex';
        } catch (e) {
            toast('Network error.', 'error');
        }
    }

    /* ================================================================
       ADD PROGRESS UPDATE
    ================================================================ */
    async function addProgressUpdate() {
        if (!currentTaskId) return;
        const ta   = $('dtProgressText');
        const text = (ta.value || '').trim();
        if (!text) { toast('Please write a progress note first.', 'info'); return; }

        const btn = $('mtAddProgressBtn');
        btn.disabled = true;

        try {
            const data = await api(`/api/ground-owner/maintenance/tasks/${currentTaskId}/progress`, {
                method: 'POST',
                body: JSON.stringify({ update_text: text, progress_percentage: 0 })
            });
            if (data.success) {
                toast('Progress note added.', 'success');
                ta.value = '';
                // Re-open details to refresh progress list
                openTaskDetails(currentTaskId);
            } else {
                toast(data.message || 'Could not add note.', 'error');
            }
        } catch (e) {
            toast('Network error.', 'error');
        } finally {
            btn.disabled = false;
        }
    }

    /* ================================================================
       SAVE TASK (create or update)
    ================================================================ */
    async function saveTask() {
        const editId = $('mtEditTaskId').value;
        const payload = {
            facility_id:          $('mtFormGround').value,
            task_type:            $('mtFormType').value,
            title:                $('mtFormTitle').value.trim(),
            description:          $('mtFormDesc').value.trim(),
            priority:             $('mtFormPriority').value,
            category:             $('mtFormCategory').value,
            scheduled_date:       $('mtFormDate').value,
            estimated_duration:   $('mtFormDuration').value || null,
            estimated_cost:       $('mtFormCost').value     || 0,
            assigned_to:          $('mtFormAssigned').value.trim() || null,
            required_tools:       $('mtFormTools').value.trim()    || null,
            block_bookings:       $('mtFormBlock').checked,
            send_notifications:   $('mtFormNotify').checked
        };

        if (!payload.facility_id || !payload.task_type || !payload.title || !payload.scheduled_date || !payload.priority) {
            toast('Please fill in all required fields.', 'info');
            return;
        }

        const btn = $('mtTaskModalSave');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…';

        try {
            const isEdit = !!editId;
            const url    = isEdit ? `/api/ground-owner/maintenance/tasks/${editId}` : '/api/ground-owner/maintenance/tasks';
            const method = isEdit ? 'PUT' : 'POST';

            const data = await api(url, { method, body: JSON.stringify(payload) });
            if (data.success) {
                toast(isEdit ? 'Task updated!' : 'Task created!', 'success');
                closeModal('mtTaskModal');
                resetTaskForm();
                loadStats();
                loadTasks();
                renderCalendar();
            } else {
                toast(data.message || 'Could not save task.', 'error');
            }
        } catch (e) {
            toast('Network error.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save"></i> Save Task';
        }
    }

    /* ================================================================
       SAVE INSPECTION
    ================================================================ */
    async function saveInspection() {
        const payload = {
            facility_id:     $('mtInspGround').value,
            inspection_type: $('mtInspType').value,
            inspection_date: $('mtInspDate').value,
            inspection_time: $('mtInspTime').value || null,
            inspector:       $('mtInspector').value.trim() || null,
            notes:           $('mtInspNotes').value.trim() || null
        };

        if (!payload.facility_id || !payload.inspection_date) {
            toast('Please fill in required fields.', 'info');
            return;
        }

        const btn = $('mtInspectionSave');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Scheduling…';

        try {
            const data = await api('/api/ground-owner/inspections', { method: 'POST', body: JSON.stringify(payload) });
            if (data.success) {
                toast('Inspection scheduled!', 'success');
                closeModal('mtInspectionModal');
                $('mtInspectionForm').reset();
                loadInspections();
            } else {
                toast(data.message || 'Could not schedule inspection.', 'error');
            }
        } catch (e) {
            toast('Network error.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-calendar-plus"></i> Schedule';
        }
    }

    /* ================================================================
       FACILITY DROPDOWNS
    ================================================================ */
    function populateFacilityDropdowns() {
        const opts = facilities.map(f => `<option value="${f.id}">${esc(f.name)}</option>`).join('');
        [$('mtFormGround'), $('mtInspGround')].forEach(sel => {
            if (sel) sel.innerHTML = '<option value="">Select Ground</option>' + opts;
        });
        const filter = $('mtGroundFilter');
        if (filter) filter.innerHTML = '<option value="">All Grounds</option>' + opts;
    }

    /* ================================================================
       BIND CONTROLS & MODALS
    ================================================================ */
    function bindControls() {
        $('mtPriorityFilter').addEventListener('change', loadTasks);
        $('mtStatusFilter').addEventListener('change', loadTasks);
        $('mtGroundFilter').addEventListener('change', loadTasks);
        $('mtPrevMonth').addEventListener('click', () => { currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() - 1, 1); renderCalendar(); });
        $('mtNextMonth').addEventListener('click', () => { currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 1); renderCalendar(); });
    }

    function bindModals() {
        // Add task buttons
        $('mtBtnAdd').addEventListener('click', () => {
            $('mtTaskModalTitle').innerHTML = '<i class="fas fa-tools"></i> Add Maintenance Task';
            $('mtEditTaskId').value = '';
            resetTaskForm();
            $('mtTaskModal').style.display = 'flex';
        });

        // Inspection buttons
        $('mtBtnInspection').addEventListener('click', () => { $('mtInspectionModal').style.display = 'flex'; });
        $('mtBtnInspection2').addEventListener('click', () => { $('mtInspectionModal').style.display = 'flex'; });

        // Task modal
        $('mtTaskModalClose').addEventListener('click', () => { closeModal('mtTaskModal'); resetTaskForm(); });
        $('mtTaskModalCancel').addEventListener('click', () => { closeModal('mtTaskModal'); resetTaskForm(); });
        $('mtTaskModalSave').addEventListener('click', saveTask);

        // Detail modal
        $('mtDetailClose').addEventListener('click', () => closeModal('mtDetailModal'));
        $('mtDetailClose2').addEventListener('click', () => closeModal('mtDetailModal'));
        $('mtCompleteTaskBtn').addEventListener('click', completeTask);
        $('mtDeleteTaskBtn').addEventListener('click', deleteTask);
        $('mtEditTaskBtn').addEventListener('click', editTask);
        $('mtAddProgressBtn').addEventListener('click', addProgressUpdate);

        // Inspection modal
        $('mtInspectionClose').addEventListener('click', () => { closeModal('mtInspectionModal'); });
        $('mtInspectionCancel').addEventListener('click', () => { closeModal('mtInspectionModal'); });
        $('mtInspectionSave').addEventListener('click', saveInspection);

        // Backdrop click to close
        ['mtTaskModal','mtDetailModal','mtInspectionModal'].forEach(id => {
            $(id).addEventListener('click', e => { if (e.target === $(id)) closeModal(id); });
        });
    }

    /* ================================================================
       HELPERS
    ================================================================ */
    function closeModal(id) {
        const el = typeof id === 'string' ? $(id) : id;
        if (el) el.style.display = 'none';
    }

    function resetTaskForm() {
        $('mtTaskForm').reset();
        $('mtEditTaskId').value = '';
        $('mtFormNotify').checked = true;
    }

    function priorityColor(p) {
        return { urgent: '#ef4444', high: '#f59e0b', medium: '#3b82f6', low: '#10b981' }[p] || '#6b7280';
    }
    function statusColor(s) {
        return { scheduled: '#3b82f6', 'in-progress': '#f59e0b', completed: '#10b981', cancelled: '#6b7280', overdue: '#ef4444' }[s] || '#6b7280';
    }

    function fmtDate(str) {
        if (!str) return '—';
        // parse YYYY-MM-DD safely avoiding UTC offset issues
        const [y, m, d] = String(str).split('T')[0].split('-').map(Number);
        const date = new Date(y, m - 1, d);
        return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    function fmtISO(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function esc(str) {
        if (str == null) return '';
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }

    function toast(msg, type = 'info') {
        const el = $('mtToast');
        if (!el) return;
        clearTimeout(toastTimer);
        el.textContent = msg;
        el.className   = `mt-toast ${type} show`;
        toastTimer = setTimeout(() => el.classList.remove('show'), 3200);
    }

})();
