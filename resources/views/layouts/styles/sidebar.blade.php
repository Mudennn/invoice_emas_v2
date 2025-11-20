<style>

    .divider {
        border-bottom: 3px solid var(--main-color);
        margin: 16px -4px;
        width: 100%;
    }

    .content-container {
        padding: 0 2% 5% 2%;
        width: 100%;
    }

    aside {
        display: flex;
        align-items: stretch;
    }

    .sidebar {
        position: relative;
        width: 256px;
        min-height: 100vh;
        /* height: 100vh; */
        display: flex;
        flex-direction: column;
        background-color: white;
        /* padding: 24px; */
        transition: all 0.3s;
        border-right: 1px solid #e8e8e8;
    }

    aside .logo {
        display: flex;
        /* justify-content: space-around; */
        align-items: center;
        justify-content: center;
        gap: 20px;
        padding: 37px 16px;
        /* asal 6px */
        border-bottom: 1px solid #e8e8e8;
        background-color: white;
        color: white;
    }

    .logo .image img {
        width: 100%;
        height: 60px;
        object-fit: cover;
    }

    /* .logo .image h6{
    color: white;
} */

    /* HIDE CLOSE BUTTON FOR MOBILE */
    aside .logo div.close {
        display: none;
    }

    /* LIST MENU */
    .nav {
        margin-top: 20px;
        padding: 0 16px;
        flex: 1;

    }

    .menu ul li {
        position: relative;
        list-style: none;
        margin-bottom: 8px;
    }

    .menu ul li a {
        display: flex;
        align-items: center;
        gap: 8px !important;
        font-size: 14px !important;
        font-weight: 500 !important;
        padding: 12px 12px !important;
        margin-left: -12px !important;
        border-radius: 8px !important;
        text-decoration: none !important;
        color: black !important;
        transition: all 0.3s !important;
    }


    /* WHEN LI IS ACTIVE */
    .menu ul li>a:hover,
    .menu ul li.active>a {
        color: white !important;
        background-color: var(--main-color) !important;

    }

    .menu ul li a .text {
        flex: 1;
    }

    /* LOGOUT BUTTON */
    .sidebar .logout {
        padding: 16px;

    }

    .sidebar .logout ul li a {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        font-size: 14px !important;
        font-weight: 500 !important;
        padding: 12px !important;
        margin-left: -32px !important;
        border-radius: 8px !important;
        text-decoration: none !important;
        color: white !important;
        transition: all 0.3s !important;
    }

    .sidebar .logout ul li>a:hover {
        color: white !important;
        background-color: var(--secondary-color) !important;
    }

    .sidebar .logout ul li {
        position: relative;
        list-style: none !important;
        margin-bottom: 8px !important;
    }

    /* ----------------------------------------- */

    /* SUB MENU */
    .menu .sub-menu {
        /* display: none; */
        margin-left: 20px;
        padding-left: 20px;
        padding-top: 8px;
        border-left: 1px solid var(--main-color);
    }

    .menu .sub-menu li a {
        padding: 10px 8px !important;
        font-size: 12px !important;
    }

    .menu ul li .arrow {
        transition: all 0.3s;
    }

    .menu ul li.active .arrow {
        transform: rotate(180deg);
    }

    /* ----------------------------------------- */

    /* OPEN CLOSE BUTTON */
    .menu-btn {
        position: absolute;
        right: -14px;
        top: 2.5%;
        width: 24px;
        height: 24px;
        border-radius: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: white;
        cursor: pointer;
        color: #757575;
        border: 2px solid #f6f6f6;
        transition: all 0.3s;
        opacity: 0;
        visibility: hidden;
    }

    .menu-btn:hover {
        color: black;
    }

    aside:hover .menu-btn {
        opacity: 1;
        visibility: visible;
    }

    /* ----------------------------------------- */

    /* SIDEBAR WIDTH WHEN CLOSE */
    aside .sidebar.active {
        width: 92px;
    }

    .sidebar.active .header .text {
        display: none;
    }

    .sidebar.active .logout .text {
        display: none;
    }

    /* .sidebar.active .logo{
    padding: 16px 0 16px 0;
} */
    .sidebar.active .image img {
        height: 40px;
        width: 100%;
        object-fit: cover;
        margin: 10px 0 10px 0;
    }

    /* .sidebar.active .header .material-symbols-outlined{
    padding-left: px;
} */

    .sidebar.active .menu-btn {
        transform: rotate(180deg);
    }

    .sidebar.active .menu ul li .arrow {
        display: none;
    }

    .sidebar.active .menu>ul>li>a {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sidebar.active .logout>ul>li>a {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;

    }

    /* FLOATING TEXT WHEN HOVER */
    .sidebar.active .menu>ul>li>a .text {
        position: absolute;
        left: 70px;
        top: 50%;
        transform: translate(24px, -50%);
        border-radius: 4px;
        color: white;
        background-color: var(--main-color);
        padding: 10px;
        opacity: 0;
        visibility: hidden;
        z-index: 100;
        text-wrap: nowrap;
    }

    .sidebar.active .menu>ul>li>a .text::after {
        content: "";
        position: absolute;
        left: -5px;
        top: 20%;
        width: 20px;
        height: 20px;
        border-radius: 2px;
        background-color: var(--main-color);
        transform: rotate(45deg);
        z-index: -1;
    }

    .sidebar.active .menu>ul>li>a:hover .text {
        left: 50px;
        opacity: 1;
        visibility: visible;
    }

    .sidebar.active .menu .sub-menu {
        position: absolute;
        top: 0;
        left: 32px;
        width: 200px;
        border-radius: 20px;
        padding: 10px 20px;
        border: 1px solid var(--main-color);
        background-color: white;
        box-shadow: 0px 10px 8px rgba(0, 0, 0, 0.1);
        z-index: 100;
    }

    /* ------------------------------------------------ */

    /* HIDE NAVBAR FOR MOBILE */
    .main-content div.navbar {
        display: none;
    }

    .main-content {
        width: 100%;
        background-color: var(--bg-color);
        flex-grow: 1;
    }

    .user {
        background-color: white;
        display: flex;
        align-items: center;
        justify-content: end;
        padding: 13px 32px 13.24px 0px;
        gap: 8px;
    }

    .user img {
        width: 40px;
        height: 40px;
    }

    /* PHONE MEDIA QUERY */
    @media only screen and (max-width: 767px) {
        .container {
            width: 100%;
            grid-template-columns: 1fr;
        }

        .content-container {
            padding: 5%;
        }

        .content-box .content {
            grid-template-columns: 1fr;
            padding: 0 16px 32px 16px;
        }

        /* SIDEBAR */
        aside .sidebar.active {
            width: 300px;
            background-color: white;
        }


        .sidebar {
            position: fixed;
            left: -100%;
            z-index: 10;
            height: 100vh;
            display: none;
            animation: showMenu 400ms ease forwards;
            width: 300px;
            background-color: white !important;
        }

        @keyframes showMenu {
            to {
                left: 0;
            }
        }

        .sidebar .menu{
        height: 80vh ;
        overflow-y: scroll
        }

        .sidebar.active .menu ul li .arrow {
            display: block;
        }

        .sidebar.active .menu-btn {
            display: none;
        }

        /* UNTUK BAGI TEXT KELUAR */
        .sidebar.active .menu ul li a .text {
            display: block;
            position: relative;
            left: 0;
            top: 0;
            transform: translateY(0);
            border-radius: 0;
            background-color: transparent;
            padding: 0;
            opacity: 1;
            visibility: visible;
            color: black;
        }

        .sidebar.active .menu>ul>li>a .text::after {
            content: none;
        }

        .sidebar.active .logout ul li a .text {
            display: block;
            position: relative;
            left: 0;
            top: 0;
            transform: translateY(0);
            border-radius: 0;
            background-color: transparent;
            padding: 0;
            opacity: 1;
            visibility: visible;
            color: white !important;
        }

        .sidebar.active .logout>ul>li>a {
            display: flex;
            align-items: center;
            justify-content: start;
        }

        /* UNTUK BAGI TAKDE BORDER UNTUK SUB MENU */
        .sidebar.active .menu .sub-menu {
            display: block;
            position: relative;
            top: 0;
            left: 0;
            width: 100%;
            border-radius: 0;
            border: none;
            background-color: transparent;
            box-shadow: none;
            /* margin-left: 20px;
        padding-left: 20px; */
            border-left: 1px solid #e8e8e8;
            color: var(--menu-text-color);
        }

        .sidebar.active .image img {
            height: 43px;
            width: 100%;
            object-fit: cover;
            margin: 0;
        }

        .sidebar div.logo {
            justify-content: space-between;
            /* padding: 8px 12px 8px 24px; */
            padding: 25px 12px 25px 24px;
        }

        .sidebar .logo div.close {
            display: block;
            color: black;
        }

        /* TEXT COLOR FOR MENU*/
        .menu ul li a {
            color: white;
        }

        .sidebar.active .menu ul li.active a .text {
            color: white;
        }

        .sidebar.active .menu .sub-menu li a .text {
            color: var(--menu-text-color) !important;
        }

        .sidebar.active .menu>ul>li>a:hover .text {
            left: 0;
            opacity: 1;
            visibility: visible;
            color: white;
        }

        /* NAVBAR */
        .main-content div.navbar {
            display: block;
        }

        .main-content div.navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 24px;
            border-bottom: 1px solid #e8e8e8;
            background-color: var(--main-color);
        }

        .main-content div.navbar .image img {
            width: 70px;
        }

        .main-content .navbar button {
            /* display: none; */
            border: none;
            background: none;
            color: white;
        }

        /* USER BOX */
        .content-box .user {
            justify-content: start;
            padding-left: 16px;
            padding-top: 16px;
            padding-bottom: 16px;
            gap: 8px;
        }



        /* CLOSE BUTTON COLOR */
        .close .material-symbols-outlined {
            color: red;
        }

    }

    /* TABLET AND IPAD QUERY */
    @media (min-width: 768px) and (max-width: 1024px) {
        .container {
            width: 100%;
            grid-template-columns: 1fr;
        }

        .content-container {
            padding: 3%;
        }

        /* SIDEBAR */
        aside .sidebar.active {
            width: 300px;
        }

        .sidebar {
            position: fixed;
            left: -100%;
            z-index: 10;
            height: 100vh;
            display: none;
            animation: showMenu 400ms ease forwards;
            width: 300px;
            background-color: white !important;
        }

        @keyframes showMenu {
            to {
                left: 0;
            }
        }


        .sidebar.active .menu ul li .arrow {
            display: block;
        }

        .sidebar.active .menu-btn {
            display: none;
        }

        /* UNTUK BAGI TEXT KELUAR */
        .sidebar.active .menu ul li a .text {
            display: block;
            position: relative;
            left: 0;
            top: 0;
            transform: translateY(0);
            border-radius: 0;
            background-color: transparent;
            padding: 0;
            opacity: 1;
            visibility: visible;
            color: var(--menu-text-color);
        }

        .sidebar.active .menu>ul>li>a .text::after {
            content: none;
        }

        .sidebar.active .logout ul li a .text {
            display: block;
            position: relative;
            left: 0;
            top: 0;
            transform: translateY(0);
            border-radius: 0;
            background-color: transparent;
            padding: 0;
            opacity: 1;
            visibility: visible;
            color: white !important;
        }

        .sidebar.active .logout>ul>li>a {
            display: flex;
            align-items: center;
            justify-content: start;
        }

        /* UNTUK BAGI TAKDE BORDER UNTUK SUB MENU */
        .sidebar.active .menu .sub-menu {
            display: block;
            position: relative;
            top: 0;
            left: 0;
            width: 100%;
            border-radius: 0;
            border: none;
            background-color: transparent;
            box-shadow: none;
            margin-left: 20px;
            padding-left: 20px;
            border-left: 1px solid #e8e8e8;
            color: var(--menu-text-color);
        }

        .sidebar.active .image img {
            height: 62px;
            width: 100%;
            object-fit: cover;
            margin: 0;
        }

        .sidebar div.logo {
            justify-content: space-between;
            /* padding: 7.5px 12px 7.5px 24px; */
            padding: 25px 12px 25px 24px;
        }

        .sidebar .logo div.close {
            display: block;
            color: black;
        }

        /* TEXT COLOR FOR MENU*/
        .menu ul li a {
            color: white;
        }

        .sidebar.active .menu ul li.active a .text {
            color: white;
        }

        .sidebar.active .menu .sub-menu li a .text {
            color: var(--menu-text-color) !important;
        }

        .sidebar.active .menu>ul>li>a:hover .text {
            left: 0;
            opacity: 1;
            visibility: visible;
            color: white;
        }

        /* NAVBAR */
        .main-content div.navbar {
            display: block;
        }

        /* MAIN CONTENT */
        .main-content {
            width: 100%;
            padding: 0;
            height: 100vh;
        }

        .main-content div.navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 24px;
            border-bottom: 1px solid #e8e8e8;
            background-color: var(--main-color);
        }

        .main-content div.navbar .image img {
            width: 100px;
        }

        .main-content .navbar button {
            /* display: none; */
            border: none;
            background: none;
            color: white;
        }

        /* CLOSE BUTTON COLOR */
        .close .material-symbols-outlined {
            color: red;
        }

        /* USER BOX */
        .content-box .user {
            justify-content: start;
            padding-left: 16px;
            padding-top: 16px;
            padding-bottom: 16px;
            gap: 8px;
        }


    }
</style>
