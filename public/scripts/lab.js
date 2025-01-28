let labIdToDelete = null;       

function deleteLab(id, labName) {
    labIdToDelete = id;
    document.getElementById('deleteLabModalText').textContent = `Are you sure you want to delete this laboratory: ${labName}?`;
    document.getElementById('deleteLabConfirmationModal').style.display = 'block';
}       

function closeLabModal() {
    document.getElementById('deleteLabConfirmationModal').style.display = 'none';
    labIdToDelete = null;
}       

function confirmDeleteLab() {
    if (labIdToDelete) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "lab.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // Reload the page after successful deletion
                    window.location.reload();
                } else {
                    console.error("Error deleting laboratory:", xhr.status);
                }
            }
        };
        xhr.send("labId=" + labIdToDelete);
    }
}

//function to update lab
function updateLab(labId, labName, description, contactInfo, location) {
    // Display the modal
    document.getElementById('updateLabModal').style.display = 'block';

    // Populate the form fields
    document.getElementById('updateLabId').value = labId;
    document.getElementById('updateLabName').value = labName;
    document.getElementById('updateDescription').value = description;
    document.getElementById('updateContactInfo').value = contactInfo;
    document.getElementById('updateLocation').value = location;
}

// Show Add Lab Modal
document.getElementById('addLabButton').addEventListener('click', function () {
    document.getElementById('addLabModal').style.display = 'block';
});

// Close Modals
document.querySelectorAll('[data-close]').forEach(function (closeButton) {
    closeButton.addEventListener('click', function () {
        closeButton.closest('.custom-modal').style.display = 'none';
    });
});

// Close modals on outside click
window.addEventListener('click', function(event) {
    if (event.target.classList.contains('custom-modal')) {
        event.target.style.display = 'none';
    }
});