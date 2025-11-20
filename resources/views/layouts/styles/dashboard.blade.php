<style>
    .dashboard-content-box {
        /* display: grid;
        grid-template-columns: 450px 1fr; */
        display: grid;
        gap: 16px;
        margin-top: 24px;
    }

    .main-dashboard-container {
        display: flex;
        /* flex-direction: row; */
        /* justify-content: stretch; */
        /* align-items: stretch; */
        /* flex-wrap: wrap; */
        gap: 24px;
    }

    .main-dashboard-card {
        background-color: white;
        padding: 24px 16px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 47%;
    }

    .main-dashboard-card h2 {
        margin-top: 24px !important;
        margin-bottom: 8px !important;
    }

    .main-dashboard-card p {
        font-size: 1.5rem !important;
        font-weight: 600 !important;
        color: var(--main-color) !important;
    }

    .chart-dashboard-card {
        background-color: white;
        padding: 24px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 100%;
    }

    .chart-dashboard-card h2 {
        font-size: 1.125rem !important;
        font-weight: 600 !important;
        color: var(--main-color) !important;
        margin-bottom: 24px !important;
    }

    span.customer-icon {
        background-color: #def5fc;
        color: #9ae7f7;
        border-radius: .375rem !important;
        padding: 8px;
    }

    span.invoice-icon {
        background-color: #fef1e0;
        color: #fccd66;
        border-radius: .375rem !important;
        padding: 8px;
    }

    span.payment-icon {
        background-color: #e8fadf;
        color: #89e362;
        border-radius: .375rem !important;
        padding: 8px;
    }

    span.balance-icon {
        background-color: #fce0db;
        color: #f53d1f;
        border-radius: .375rem !important;
        padding: 8px;
    }

    /* PHONE MEDIA QUERY */
    @media only screen and (max-width: 767px) {
        .dashboard-content-box {
            /* grid-template-columns: 100%; */

        }

        .main-dashboard-container {
            flex-direction: column;
        }

        .main-dashboard-card {
            width: 100%;
        }

        .chart-dashboard-card{
            padding: 24px 16px 24px 16px;
            width: fit-content;
        }

        .main-dashboard-card h2 {
        margin-top: 16px !important;
    }

    }

    /* TABLET AND IPAD QUERY */
    @media (min-width: 768px) and (max-width: 1024px) {
        .dashboard-content-box {
            grid-template-columns: 100%;
        }

        .main-dashboard-container {
            flex-wrap: wrap;
           justify-content: space-between;
        }

        .main-dashboard-card {
            width: 48%;
        }
    }
</style>