// --- DEFINE THESE AT THE TOP (Global Scope) ---
let auditCurrentPage = 1; 
const auditRowsPerPage = 10; 
let filteredAuditData = [];

function escapeHtml(value) {
    return String(value ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function getAuditAdminName(log) {
    return log.admin_name || log.display_name || "System";
}

// 1. Updated Filter Logic to handle Year, Action, Admin, and Search
function filterAuditLog() {
    auditCurrentPage = 1; 
    
    const yearVal = document.getElementById("filterYear").value;
    const actionVal = document.getElementById("filterAction").value.toUpperCase();
    const adminVal = document.getElementById("filterAdmin").value.toLowerCase();
    const searchVal = document.getElementById("auditSearch").value.toLowerCase().trim();

    const filteredLogs = window.auditLogs.filter(log => {
        const logYear = new Date(log.timestamp).getFullYear().toString();
        const logAdmin = String(getAuditAdminName(log)).toLowerCase();
        const logAction = String(log.action_type || "").toUpperCase();
        const logDetails = String(log.details || "").toLowerCase();

        const matchesYear = yearVal === "" || logYear === yearVal;
        const matchesAction = actionVal === "" || logAction === actionVal;
        const matchesAdmin = adminVal === "" || logAdmin === adminVal;
        const matchesSearch = searchVal === "" || logDetails.includes(searchVal);

        return matchesYear && matchesAction && matchesAdmin && matchesSearch;
    });

    filteredAuditData = filteredLogs;
    renderAuditTableWithData(filteredAuditData);
}

// 2. Modified Render Function
function renderAuditTableWithData(dataList) {
    const tbody = document.getElementById("auditLogBody");
    if (!tbody) return;

    // PAGINATION MATH
    const totalPages = Math.ceil(dataList.length / auditRowsPerPage);
    const start = (auditCurrentPage - 1) * auditRowsPerPage; 
    const end = start + auditRowsPerPage;
    const paginatedLogs = dataList.slice(start, end);

    tbody.innerHTML = "";

    if (paginatedLogs.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" style="text-align:center; padding: 40px; color: #6b6b6b;">No matching logs found.</td></tr>`;
        if (typeof renderAuditPagination === "function") renderAuditPagination(0, 0);
        return;
    }

    paginatedLogs.forEach(log => {
        const tr = document.createElement("tr");
        const rawAction = String(log.action_type || "").toUpperCase();
        const safeAction = escapeHtml(rawAction);
        const safeAdmin = escapeHtml(getAuditAdminName(log));
        const safeDetails = escapeHtml(log.details);
        const safeTimestamp = escapeHtml(log.timestamp);
        const safeLogId = escapeHtml(log.log_id);
        const safeAdminId = escapeHtml(log.admin_id);
        
        // --- UPDATED COLOR CODING LOGIC ---
        let actionClass = "login"; // Default (Gray/Dark)

        if (['DELETED', 'DELETE', 'REMOVE', 'DELETE_ADMIN'].includes(rawAction)) {
            actionClass = "delete"; // RED
        } 
        else if (['CREATED', 'ADD', 'CREATE', 'INSERT', 'CREATE_ADMIN', 'ADDED'].includes(rawAction)) {
            actionClass = "create"; // GREEN
        } 
        else if (['UPDATED', 'EDIT', 'UPDATE', 'MODIFIED', 'UPDATE_ADMIN'].includes(rawAction)) {
            actionClass = "update"; // BLUE (Ensure your CSS .update class is blue)
        }

        tr.innerHTML = `
            <td style="color: #64748b;">#${safeLogId}</td>
            <td>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="admin-badge">ID: ${safeAdminId}</span>
                    <strong style="color: #1a1a1a; font-size: 13px;">${safeAdmin}</strong>
                </div>
            </td>
            <td><span class="action-tag ${actionClass}">${safeAction}</span></td>
            <td class="details-cell" style="max-width: 400px; color: #1a1a1a; line-height: 1.5;">${safeDetails}</td>
            <td class="time-cell">${safeTimestamp}</td>
        `;
        tbody.appendChild(tr);
    });

    if (typeof renderAuditPagination === "function") {
        renderAuditPagination(auditCurrentPage, totalPages);
    }
}

function renderAuditPagination(currentPage, totalPages) {
    const table = document.getElementById("auditLogTable");
    if (!table) return;

    let controls = document.getElementById("auditPagination");
    if (!controls) {
        controls = document.createElement("div");
        controls.id = "auditPagination";
        controls.className = "audit-pagination";
        table.insertAdjacentElement("afterend", controls);
    }

    if (totalPages <= 1) {
        controls.innerHTML = "";
        return;
    }

    controls.innerHTML = `
        <button type="button" ${currentPage <= 1 ? "disabled" : ""} onclick="changeAuditPage(${currentPage - 1})">Previous</button>
        <span>Page ${currentPage} of ${totalPages}</span>
        <button type="button" ${currentPage >= totalPages ? "disabled" : ""} onclick="changeAuditPage(${currentPage + 1})">Next</button>
    `;
}

function changeAuditPage(page) {
    const totalPages = Math.ceil(filteredAuditData.length / auditRowsPerPage);
    auditCurrentPage = Math.min(Math.max(page, 1), Math.max(totalPages, 1));
    renderAuditTableWithData(filteredAuditData);
}

function resetAuditFilters() {
    ["filterYear", "filterAction", "filterAdmin", "auditSearch"].forEach(id => {
        const field = document.getElementById(id);
        if (field) field.value = "";
    });

    filterAuditLog();
}

document.addEventListener("DOMContentLoaded", () => {
    if (window.auditLogs) {
        filterAuditLog(); 
    }
});
