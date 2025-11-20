<style>
    /* @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"); */
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap');

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        /* font-family: "Poppins", sans-serif; */
        font-family: "Plus Jakarta Sans", serif;
    }

    html,
    body {
        scroll-behavior: smooth;
    }

    body {
        background-color: #f5f5f9 !important;
        color: black;
        /* background-color: rgba(255, 255, 255, 0.692) !important; */
    }

    :root {
        --main-color: #7b64e3;
        --main-bg-color: white;
        --heading-color: #37322F;
        --heading-color-2: #646e78;
        --text: #888581;
        --border-color: #95877A;
        --red-color: #C83000;
        --bg-screen: #f4f4f4;
        --yellow-color: #FBBC05;
    }

    h1 {
        font-size: 1.5rem !important;
        font-weight: 700 !important;
        color: var(--main-color) !important;
        margin-bottom: 0 !important;
    }

    h2 {
        font-size: 1rem !important;
        font-weight: 400 !important;
        color: var(--text) !important;
        margin-bottom: 0 !important;
    }

    h3 {
        font-size: 1.125rem !important;
        font-weight: 500 !important;
        color: var(--main-color) !important;
        margin-bottom: 0 !important;
    }

    h4 {
        font-size: 0.875rem !important;
        font-weight: 300 !important;
        margin-bottom: 0 !important;
        color: var(--text) !important;
    }

    h6 {
        font-size: 0.875rem !important;
        font-weight: 500 !important;
        margin-bottom: 0 !important;
        color: var(--main-color) !important;
    }

    /* h5 {
  font-size: 0.875rem !important;
  font-weight: 300 !important;
} */

    p {
        font-size: 0.875rem !important;
        color: var(--text) !important;
        margin-bottom: 0 !important;
    }

    hr {
        border-top: 1px var(--border-color) solid !important;
        color: var(--border-color) !important;
        margin: 0 !important;
        opacity: 1 !important;
    }

    a {
        font-size: 0.875rem !important;
        text-decoration: none !important;
        margin-bottom: 0 !important;
        color: var(--main-color) !important;
        transition: all 0.3s !important;
        display: flex;
        align-items: center;
        gap: 8px;

        &:hover {
            color: rgba(35, 143, 251, 0.8) !important;
        }
    }


    .content-box {
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        width: 70%;
    }

    .primary-button {
        background-color: var(--main-color) !important;
        border: none !important;
        padding: 4px 16px !important;
        border-radius: 8px !important;
        font-weight: 400 !important;
        font-size: 1rem  !important;
        text-align: center !important;
        /* box-shadow: 4px 4px 0 rgba(0, 0, 0, 1); */
        transition: all 0.3s !important;
        color: white !important;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .primary-button:hover {
        color: white !important;
        background-color: rgb(67, 67, 67) !important;
    }

    .table-button {
        background-color: var(--third-color) !important;
        border: none !important;
        padding: 4px 32px !important;
        border-radius: 50px !important;
        font-weight: 400 !important;
        font-size: 1.125rem !important;
        /* box-shadow: 4px 4px 0 rgba(0, 0, 0, 1); */
        transition: all 0.3s !important;
        color: black !important;
        text-align: center !important;
    }

    .table-button:hover {
        background-color: var(--main-color) !important;
        color: white !important;
    }
  

    .third-button {
        background-color: rgb(35, 143, 251) !important;
        border: none !important;
        padding: 4px 16px !important;
        border-radius: 8px !important;
        font-weight: 300 !important;
        font-size: 1rem !important;
        transition: all 0.3s !important;
        color: white !important;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .third-button:hover {
        background-color: rgba(35, 143, 251, 0.8) !important;
        color: white !important;
    }

    .form-primary-button {
        background-color: var(--main-color) !important;
        border: none !important;
        padding: 4px 16px !important;
        border-radius: 8px !important;
        font-weight: 400 !important;
        font-size: 1rem !important;
        transition: all 0.3s !important;
        color: white !important;
    }

    .form-primary-button:hover {
        background-color: rgb(67, 67, 67) !important;
        color: white !important;
    }

    .form-secondary-button {
        background-color: white !important;
        border: 1px solid var(--main-color) !important;
        padding: 4px 16px !important;
        border-radius: 8px !important;
        font-weight: 400 !important;
        font-size: 1rem !important;
        transition: all 0.3s !important;
        color: black !important;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-secondary-button:hover {
        background-color: var(--main-color) !important;
        color: white !important;
    }

    .form-delete-button {
        background-color: var(--red-color) !important;
        border: 1px solid var(--red-color) !important;
        padding: 4px 16px !important;
        border-radius: 8px !important;
        font-weight: 400 !important;
        font-size: 1rem !important;
        transition: all 0.3s !important;
        color: white !important;
    }

    .form-delete-button:hover {
        background-color: rgba(199, 46, 0, 0.6) !important;
        color: white !important;
    }

    span.badge.text-bg-primary {
        background-color: var(--main-color) !important;
    }




    /* PHONE MEDIA QUERY */
    @media only screen and (max-width: 767px) {

        .content-box,
        .crew-box {
            padding: 5%;
            width: 100%;
        }

        .primary-button {
            font-size: 0.875rem !important;
        }

        .form-primary-button,
        .form-secondary-button {
            font-size: 14px !important;
        }

    }

    /* TABLET AND IPAD QUERY */
    @media (min-width: 768px) and (max-width: 1024px) {

        .content-box,
        .crew-box {
            padding: 5%;
            width: 100%;
        }
    }
</style>
