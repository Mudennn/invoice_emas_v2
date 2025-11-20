<style>
    .table-header {
        padding: 24px 0 !important;
        background-color: transparent !important;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card {
        background: white !important;
        box-shadow: 4px 4px 6px 0 rgba(0, 0, 0, 0.1);
    }

    .gold-prices-table {
        width: 500px;
        overflow-y: auto;
    }


    /* DATATABLE */

    /* ------------------------------------------------------------ */
    /* new add 23/04/2025 */
    /* When using fixed table layout, ensure proper styling for cells */
    table[style*="table-layout: fixed"] th,
    table[style*="table-layout: fixed"] td {
     white-space: normal;
     word-wrap: break-word;
     overflow-wrap: break-word;
    }

    /* Allow remark column to wrap text */
    table[style*="table-layout: fixed"] .text-wrap {
        white-space: normal !important;
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
    }
    /* ------------------------------------------------------------ */

    table,
    th,
    td {
        padding: .782rem 1.25rem !important;
        border-collapse: collapse;
        text-align: left !important;
        font-size: 14px;
    }

    table th {
        background-color: #f0f0f0 !important;
        color: black!important;
        padding-block: 1.161rem !important;
    }

    tfoot tr th {
        padding-block: 0.75rem !important;
    }


    div.dt-container .top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 32px !important;
    }

    /* div.dt-container div.dt-lenght{
        width: 30% !important;
        display: flex !important;
        gap: 8px !important;
    }

    div.dt-container .top .form-select-sm{
        width: 20%;
    }

    div.dt-container .top lable {
        width: 100%;
    } */

    /* SEARCH BAR */
    div.dt-container div.dt-search {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 30%;
    }
    div.dt-container .dt-search input {
        border: 1px solid #ddd !important;
        border-radius: 8px !important;
        margin-left: 8px !important;
    }

    /* SELECT ENTRIES */
    div.dt-container select.dt-input {
        border: 1px solid #ddd !important;
        border-radius: 8px !important;
        margin-right: 16px !important;
        margin-bottom: 16px !important;
        padding: 4px 12px !important;
    }

    div.dt-container .bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 32px !important;
    }

    .dt-column-order {
        display: none !important;
    }

    .dt-column-order:hover {
        display: block !important;
    }

    /* PAGINATION */
    div.dt-container .dt-paging .dt-paging-button.current,
    div.dt-container .dt-paging .dt-paging-button.current:hover {
        border-radius: 8px !important;
        border: 1px solid #ddd !important;
        color: white !important;
    }

    /* No Pagination */
    .page-link.active,
    .active>.page-link {
        background-color: var(--main-color) !important;
        border: 1px solid var(--main-color) !important;
        transition: all 0.3s;
    }

    .page-link.active,
    .active>.page-link:hover {
        background-color: white !important;
        border: 1px solid var(--heading-color) !important;
        color: var(--heading-color) !important;
    }

   

    /* MOBILE SCREEN */
    @media screen and (max-width: 767px) {
        .table-header{
            padding: 24px 0 24px 0 !important;
            /* flex-direction: column; */
            gap: 16px;
            align-items: flex-start; 
        }

        /* .table-header .primary-button {
            width: 100% !important;
        } */

        div.dt-container div.dt-search {
            width: 100%;
        }

        div.dt-container .top,
        div.dt-container .bottom {
            flex-direction: column;
            gap: 24px;
        }

        .gold-prices-table {
            width: 100%;
            overflow-y: auto;
        }

        .table-responsive {
            width: 100% !important;
            overflow-x: auto !important;
        }
    }

    /* TABLET AND IPAD QUERY */
    @media (min-width: 768px) and (max-width: 1024px) {}
</style>
