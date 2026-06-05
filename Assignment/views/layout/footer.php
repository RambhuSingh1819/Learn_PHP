    <!-- Footer Section -->
    <footer class="footer mt-auto py-3 border-top" style="background: rgba(15, 23, 42, 0.6); margin-top: 5rem !important;">
        <div class="container text-center">
            <span class="text-muted" style="font-size: 0.85rem;">&copy; <?= date('Y') ?> Secure Auth System. Built using PHP MVC Architecture, cURL Email API integrations, and BCRYPT Password Security. All rights reserved.</span>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle with Popper JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
    <!-- Custom Application JS -->
    <script src="<?= url('/assets/js/app.js') ?>"></script>

    <!-- Handle PHP Session Flash Alerts and Toast Notifications -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            <?php if (isset($_SESSION['success_message'])): ?>
                showToast(<?= json_encode($_SESSION['success_message']) ?>, 'success');
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                showToast(<?= json_encode($_SESSION['error_message']) ?>, 'error');
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['timeout_message'])): ?>
                showToast(<?= json_encode($_SESSION['timeout_message']) ?>, 'warning');
                <?php unset($_SESSION['timeout_message']); ?>
            <?php endif; ?>
        });
    </script>
</body>
</html>
