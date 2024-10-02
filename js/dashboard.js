function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content-area');
    sidebar.classList.toggle('collapsed');
    content.classList.toggle('collapsed');
}

document.getElementById('applicants-link').addEventListener('click', function(event) {
    event.preventDefault();
    loadApplicants();
});

function loadApplicants() {
    var contentArea = document.getElementById('content-area');
    contentArea.innerHTML = '<div class="loading"><img src="../images/loading.gif" alt="Loading..."></div>';

    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'applicants.php', true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                contentArea.innerHTML = xhr.responseText;
            } else {
                contentArea.innerHTML = '<p>Error loading applicants. Please try again later.</p>';
            }
        }
    };
    xhr.send();
}
