document.addEventListener("DOMContentLoaded", function() {
    // Auto-dismiss alerts after 4 seconds for a smoother UX
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alert) {
            let bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 4000);
});