var activeTrackingID = null; // Variable to track which ID is active

function toggleCardDetails(trackingID) {
    var allCards = document.querySelectorAll('.card');
    var currentCard = document.getElementById('card-' + trackingID);
    var button = document.getElementById('toggle-btn-' + trackingID);

    allCards.forEach(card => {
        if (card !== currentCard) {
            card.classList.remove('card-expanded');
            var otherButton = card.querySelector('.card-link');
            if (otherButton) {
                otherButton.innerHTML = 'View Details';
            }
        }
    });

    if (currentCard.classList.contains('card-expanded')) {
        currentCard.classList.remove('card-expanded');
        button.innerHTML = 'View Details';
    } else {
        currentCard.classList.add('card-expanded');
        button.innerHTML = 'Hide Details';
    }
}

function toggleDocumentDetails(trackingID) {
    var allDocumentDetails = document.querySelectorAll('.document-details');
    var currentDocumentDetails = document.getElementById('documents-' + trackingID);
    var button = document.getElementById('view-docs-' + trackingID);
    var statusUpdate = document.getElementById('status-update-form-' + trackingID);


    // Hide all other document details and update their buttons
    allDocumentDetails.forEach(details => {
        if (details !== currentDocumentDetails) {
            details.style.display = 'none';
            var otherButton = details.previousElementSibling.querySelector('.btn-info');
            if (otherButton) {
                otherButton.innerHTML = 'View Documents';
            }
        }
    });

     // Hide status update form if visible
     if (statusUpdate.style.display === 'block') {
        statusUpdate.style.display = 'none';
        var sbutton = document.getElementById('update-status-' + trackingID);
        if (sbutton) {
            sbutton.innerHTML = 'Update Status';
        }
    }

    if (currentDocumentDetails.style.display === 'none') {
        currentDocumentDetails.style.display = 'block';
        button.innerHTML = 'Hide Documents';
    } else {
        currentDocumentDetails.style.display = 'none';
        button.innerHTML = 'View Documents';
    }
}

function toggleUpdateStatus(trackingID) {
    var statusUpdate = document.getElementById('status-update-form-' + trackingID);
    var button = document.getElementById('update-status-' + trackingID);
    var documentDetails = document.getElementById('documents-' + trackingID);
    
    // Hide document details if visible
    if (documentDetails.style.display === 'block') {
        documentDetails.style.display = 'none';
        var docButton = document.getElementById('view-docs-' + trackingID);
        if (docButton) {
            docButton.innerHTML = 'View Documents';
        }
    }

    // Toggle status update form
    if (statusUpdate.style.display === 'none') {
        statusUpdate.style.display = 'block';
        button.innerHTML = 'Hide Status Update';
    } else {
        statusUpdate.style.display = 'none';
        button.innerHTML = 'Update Status';
    }
}

function updateStatus(trackingID) {
    var newStatus = document.getElementById('status-' + trackingID).value;
    
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'applicants.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var response = xhr.responseText;
                var statusLabel = document.querySelector('#card-' + trackingID + ' .status-label');
                
                if (response === 'success') {
                    statusLabel.textContent = 'Status: ' + newStatus;
                    statusLabel.className = 'status-label ' + getStatusClass(newStatus);
                } else {
                    alert('Failed to update status.');
                }
                
                // Hide the status update form
                var statusUpdateForm = document.getElementById('status-update-form-' + trackingID);
                if (statusUpdateForm) {
                    statusUpdateForm.style.display = 'none';
                }
            } else {
                alert('An error occurred.');
            }
        }
    };
    
    xhr.send('update_status=true&trackingID=' + encodeURIComponent(trackingID) + '&status=' + encodeURIComponent(newStatus));
}



function getStatusClass(status) {
    switch (status) {
        case 'Application details submitted':
            return 'bg-warning text-dark'; // Yellow
        case 'Application under review':
            return 'bg-orange text-white'; // Orange
        case 'Visa approved':
            return 'bg-success text-white'; // Green
        case 'Visa denied':
            return 'bg-danger text-white'; // Red
        default:
            return 'bg-secondary text-white'; // Default
    }
}

function resetActiveFunctionality() {
    if (activeTrackingID) {
        var card = document.getElementById('card-' + activeTrackingID);
        var docDetails = document.getElementById('documents-' + activeTrackingID);
        var statusUpdate = document.getElementById('status-update-form-' + activeTrackingID);

        if (card) {
            card.classList.remove('card-expanded');
            var cardButton = document.getElementById('toggle-btn-' + activeTrackingID);
            if (cardButton) {
                cardButton.innerHTML = 'View Details';
            }
        }

        if (docDetails) {
            docDetails.style.display = 'none';
            var docButton = document.getElementById('view-docs-' + activeTrackingID);
            if (docButton) {
                docButton.innerHTML = 'View Documents';
            }
        }

        if (statusUpdate) {
            statusUpdate.style.display = 'none';
            var statusButton = document.getElementById('update-status-' + activeTrackingID);
            if (statusButton) {
                statusButton.innerHTML = 'Update Status';
            }
        }

        activeTrackingID = null;
    }
}
