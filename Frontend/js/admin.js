// Admin Page Script

document.addEventListener('DOMContentLoaded', () => {
    loadAdminRsos();
    loadActiveRsosForEventCreation();
    initMapInput();

    const createEventButton = document.querySelector('#createEventForm button');
    if (createEventButton) {
        createEventButton.addEventListener('click', submitEventForm);
    }
});

function loadAdminRsos() {
    fetch("/Cop4710_Project/WAMPAPI/api/rsos/get_admin_rso.php")
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('manage-rso-select');
            const message = document.getElementById('no-admin-rsos-message');
            select.innerHTML = '<option value="">Select an RSO</option>';

            if (data.status === "success" && data.data.length) {
                data.data.forEach(rso => {
                    const option = new Option(rso.name, rso.id);
                    select.appendChild(option);
                });
                message.style.display = 'none';
            } else {
                message.style.display = 'block';
            }
        });
}

function loadActiveRsosForEventCreation() {
    fetch("/Cop4710_Project/WAMPAPI/api/rsos/get_active_admin_rsos.php")
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('event_rso');
            select.innerHTML = '<option value="">Select RSO</option>';

            if (data.status === "success" && data.data.length) {
                data.data.forEach(rso => {
                    const option = new Option(rso.name, rso.id);
                    select.appendChild(option);
                });
            }
        });
}

function loadRsoMembers() {
    const rsoId = document.getElementById('manage-rso-select').value;
    const container = document.getElementById('rso-management-container');

    if (!rsoId) return container.style.display = 'none';

    container.style.display = 'block';

    fetch(`/Cop4710_Project/WAMPAPI/api/rsos/get_rso_details.php?rso_id=${rsoId}`)
        .then(res => res.json())
        .then(data => {
            if (data.status === "success") {
                const rso = data.data;
                document.getElementById('rso-name-display').textContent = rso.name;
                document.getElementById('rso-description-display').textContent = rso.description;
                document.getElementById('rso-status-display').textContent = rso.status;
                document.getElementById('rso-university-display').textContent = rso.university_name;
                document.getElementById('rso-member-count-display').textContent = rso.member_count;

                return fetch(`/Cop4710_Project/WAMPAPI/api/rsos/get_rso_members.php?rso_id=${rsoId}`);
            } else {
                throw new Error(data.message);
            }
        })
        .then(res => res.json())
        .then(data => {
            const list = document.getElementById('rso-members-list');
            if (data.status === "success") {
                list.innerHTML = data.data.map(member => `
                    <tr>
                        <td>${member.first_name} ${member.last_name}</td>
                        <td>${member.username}</td>
                        <td>${member.role}</td>
                        <td>
                            ${member.role !== 'admin' ? `<button class="action-button" onclick="promoteToAdmin(${rsoId}, ${member.user_id})">Make Admin</button>` : ''}
                            <button class="action-button remove" onclick="removeMember(${rsoId}, ${member.user_id})">Remove</button>
                        </td>
                    </tr>`).join('');
            } else {
                list.innerHTML = '<tr><td colspan="4">Failed to load members</td></tr>';
            }
        })
        .catch(() => {
            document.getElementById('rso-members-list').innerHTML = '<tr><td colspan="4">Error loading members</td></tr>';
        });
}

function promoteToAdmin(rsoId, userId) {
    if (!confirm("Are you sure you want to make this user an admin?")) return;

    fetch("/Cop4710_Project/WAMPAPI/api/rsos/promote_member.php", {
        method: "POST",
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ rso_id: rsoId, user_id: userId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            alert("Member promoted to admin successfully!");
            loadRsoMembers();
        } else {
            alert("Failed to promote member: " + data.message);
        }
    });
}

function removeMember(rsoId, userId) {
    if (!confirm("Are you sure you want to remove this member from the RSO?")) return;

    fetch("/Cop4710_Project/WAMPAPI/api/rsos/remove_member.php", {
        method: "POST",
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ rso_id: rsoId, user_id: userId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            if (data.reload_session) {
                fetch("/Cop4710_Project/WAMPAPI/api/users/check_user_role.php", {
                    method: "POST",
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ update_role: true })
                })
                .then(res => res.json())
                .then(role => {
                    window.location.href = role.success ? "/Cop4710_Project/Frontend/pages/student_dashboard.php" : "/Cop4710_Project/Frontend/pages/index.html";
                });
            } else {
                loadRsoMembers();
                alert("Member removed successfully!");
            }
        } else {
            alert("Failed to remove member: " + data.message);
        }
    });
}

function toggleRsoSelection() {
    const isRso = document.getElementById('event_type').value === "rso";
    document.getElementById('rso-selection-container').style.display = isRso ? 'block' : 'none';
}

function initMapInput() {
    const eventMap = document.getElementById('event-map');
    if (eventMap) {
        eventMap.addEventListener('click', () => {
            console.log("Map clicked - set coordinates here in real implementation");
        });
    }
}

function submitEventForm() {
    const form = document.getElementById('createEventForm');
    const result = document.getElementById('createEventResult');

    const eventData = {
        name: form.event_name.value.trim(),
        category: form.event_category.value,
        description: form.event_description.value.trim(),
        date: form.event_date.value,
        time: form.event_time.value,
        location: form.event_location.value.trim(),
        latitude: form.event_latitude.value,
        longitude: form.event_longitude.value,
        contact_phone: form.event_contact_phone.value.trim(),
        contact_email: form.event_contact_email.value.trim(),
        type: form.event_type.value,
        rso_id: form.event_type.value === "rso" ? form.event_rso.value : null
    };

    if (
        !eventData.name || !eventData.category || !eventData.description || 
        !eventData.date || !eventData.time || !eventData.location ||
        !eventData.latitude || !eventData.longitude || 
        !eventData.contact_phone || !eventData.contact_email || 
        !eventData.type
    ) {
        result.innerHTML = '<div class="error-message">Please fill in all required fields</div>';
        return;
    }
    
    if (eventData.type === "rso" && !eventData.rso_id) {
        result.innerHTML = '<div class="error-message">Please select an RSO for this event</div>';
        return;
    }

    const formData = new FormData();
    for (const [key, value] of Object.entries(eventData)) {
        if (value !== null) formData.append(key, value);
    }

    fetch("/Cop4710_Project/WAMPAPI/api/rsos/create_event.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            result.innerHTML = '<div class="success-message">Event created successfully!</div>';
            form.reset();
            document.getElementById('rso-selection-container').style.display = 'none';
            setTimeout(() => result.innerHTML = '', 3000);
        } else {
            result.innerHTML = `<div class="error-message">Failed to create event: ${data.message}</div>`;
        }
    })
    .catch(error => {
        result.innerHTML = `<div class="error-message">Error creating event: ${error}</div>`;
    });
}