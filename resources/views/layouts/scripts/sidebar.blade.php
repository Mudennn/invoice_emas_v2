<script>
    document.addEventListener("DOMContentLoaded", function() {
    // Hide all sub-menus initially
    var subMenus = document.querySelectorAll(".sub-menu");
    subMenus.forEach(function(subMenu) {
        subMenu.style.display = "none";
    });

    // Handle click on menu items
    var menuItems = document.querySelectorAll(".menu > ul > li");
    menuItems.forEach(function(menuItem) {
        menuItem.addEventListener("click", function() {
            // Remove active from already active
            var siblings = this.parentElement.children;
            for (var i = 0; i < siblings.length; i++) {
                if (siblings[i] !== this) {
                    siblings[i].classList.remove("active");
                    siblings[i].querySelectorAll("ul").forEach(function(subMenu) {
                        subMenu.style.display = "none";
                    });
                    siblings[i].querySelectorAll("li").forEach(function(subMenuItem) {
                        subMenuItem.classList.remove("active");
                    });
                }
            }

            // Toggle active on clicked item
            this.classList.toggle("active");

            // Toggle the sub-menu
            var subMenu = this.querySelector("ul");
            if (subMenu) {
                if (subMenu.style.display === "none" || subMenu.style.display === "") {
                    subMenu.style.display = "block";
                } else {
                    subMenu.style.display = "none";
                }
            }
        });
    });

    // Sidebar open and close
    var menuBtn = document.querySelector(".menu-btn");
    menuBtn.addEventListener("click", function() {
        var sidebar = document.querySelector(".sidebar");
        sidebar.classList.toggle("active");

        // Ensure sub-menus are hidden when sidebar is toggled
        if (!sidebar.classList.contains("active")) {
            subMenus.forEach(function(subMenu) {
                subMenu.style.display = "none";
            });
        }
    });
});

const sideMenu = document.querySelector(".sidebar");
const menuBtn = document.querySelector("#menu-btn");
const closeBtn = document.querySelector('#close-btn');

menuBtn.addEventListener('click', () => {
    sideMenu.style.display = 'block';
});

closeBtn.addEventListener('click', () => {
    sideMenu.style.display = 'none';
});
</script>