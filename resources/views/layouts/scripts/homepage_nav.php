<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuButton = document.querySelector('.menu-button');
        const listMenu = document.querySelector('.list-menu .mobile-menu');

        menuButton.addEventListener('click', function() {
            listMenu.classList.toggle('active');
        });
    });
</script>