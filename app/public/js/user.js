function showUser(id, firstName, lastName, address) {
    document.getElementById('id').value = id;
    document.getElementById('firstName').value = firstName;
    document.getElementById('lastName').value = lastName;
    document.getElementById('address').value = address;
}

function confirmDelete(userId) {
    if (confirm("Are you sure you want to delete this user?")) {
        window.location.href = "/user?action=delete&id=" + userId;
    }
}