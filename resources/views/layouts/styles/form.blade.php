<style>
    /* SELECT2 */
    .select2-container--default .select2-selection--single{
        padding: 6px !important;
        border: 1px solid #ddd !important;
        border-radius: 8px !important;
        background-color: var(--bg-screen) !important;
        width: 100% !important;
        font-size: 0.875rem !important;
        height: 42px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow{
        top: 50% !important;
        transform: translateY(-50%) !important;
    }

    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable{
        background-color: var(--main-color) !important;
    }

    /* FORM */

    .form-container{
        /* display: flex; */
        background: white;
        /* padding: 40px 32px 24px 32px; */
        border: 1px solid rgba(0, 0, 0, 0.175);
        border-radius: 0.375rem;
        width: 650px;
        flex: 1;
        margin: 2% auto 0 auto;
        box-shadow: 8px 8px 6px 0 rgba(0, 0, 0, 0.1);
    }
   
    .invoice-form-container{
        /* display: flex; */
        background: white;
        /* padding: 40px 32px 24px 32px; */
        border: 1px solid rgba(0, 0, 0, 0.175);
        border-radius: 0.375rem;
        flex: 1;
        /* margin: 5% auto; */
        margin-top: 2%;
        box-shadow: 8px 8px 6px 0 rgba(0, 0, 0, 0.1);
    }

    .form-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
    }

    .form-header .token-available {
        background: var(--third-color);
        border-radius: 50px;
        padding: 4px 16px;
    }

    .token-available p {
        color: black !important;
        font-weight: 600 !important;
    }

    form {
        width: 100%;
    }

    .input-form {
        display: flex;
        flex-direction: column;
        margin: 16px 0;
        flex: 1;
    }

    .input-form textarea {
        height: 200px !important;
        padding: 8px 12px !important;
        border: 1px solid #ddd !important;
        border-radius: 8px !important;
        background-color: var(--bg-screen) !important;
    }

    .form-control {
        padding: 8px 12px !important;
        border: 1px solid #ddd !important;
        border-radius: 8px !important;
        background-color: var(--bg-screen) !important;
        width: 100% !important;
        font-size: 0.875rem !important
    }

    .input-form label {
        margin-bottom: 8px !important;
        font-size: 0.875rem !important;
    }

    input::placeholder {
        font-size: 0.875rem !important;
    }

    .form-control:focus {
        border: 1px solid var(--main-color) !important;
        /* border-color: var(--main-color);  */
        outline: none !important;
        box-shadow: none !important;
    }

    .input-group{
        flex-wrap: nowrap !important;
    }

    .form-controll{
        padding: 8px 12px !important;
        border: 1px solid #ddd !important;
        border-radius: 8px 0 0 8px !important;
        background-color: var(--bg-screen) !important;
        font-size: 0.875rem !important;
        width: 100% !important;
    }

    .form-controll:focus {
        border: 1px solid var(--main-color) !important;
        /* border-color: var(--main-color);  */
        outline: none !important;
        box-shadow: none !important;
    }

    .btn-outline-secondary:hover{
        background-color: var(--main-color) !important;
        color: white !important;
    }

    .row-form {
        display: flex;
        align-items: stretch;
        justify-content: space-between;
        gap: 16px;
        /* display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px; */
    }
 
    .invoice-row-form {
        display: flex;
        align-items: stretch;
        gap: 16px;
        /* display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px; */
    }

    .invoice-row-form-left {
        width: 25%;
    }

    .invoice-row-form-right {
        width: 25%;
    }

    .form-button-container {
        margin: 24px 0 24px 24px;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
    }

    .form-container h4 {
        color: var(--main-color) !important;
        font-weight: 600 !important;
        margin-top: 32px !important; 
    }

    /* UNTUK FORM YANG ADA ICON */
    .form-icon {
        padding: 8px 12px !important;
        border: 1px solid #ddd !important;
        border-radius: 8px 0 0 8px !important;
        background-color: var(--bg-screen) !important;
        width: 100% !important;
        font-size: 0.875rem !important
    }

    .input-group-text{
        display: inline !important;
        background-color: var(--bg-screen) !important;
        border: 1px solid #ddd !important;
        border-radius: 0 8px 8px 0 !important;
        font-size: 1rem !important;
    }

    /* CREDIT NOTE */
    .credit-note-form-container{
        display: grid;
        grid-template-columns: 400px 1fr;
        gap: 24px;
    }

    .credit-note-form-container .invoice-detailss{
        margin-top: 24px;
    }

    .credit-note-form-container .invoice-detailss h2{
        font-size: 0.875rem !important;
        font-weight: 600 !important;
        color: black !important;
    }

    /* PHONE MEDIA QUERY */
@media only screen and (max-width: 767px) {
    .form-container{
        width: 100%;
    }

    .input-form {
        margin: 8px 0;

    }

    .form-header {
        flex-direction: column;
        gap: 16px;
    }

    .row-form {
        display: grid;
        grid-template-columns: 100%;
        gap: 0px;
    }

    .invoice-row-form {
        flex-direction: column;
    }

    .invoice-row-form-left {
        width: 100%;
    }

    .invoice-row-form-right {
        width: 100%;
    }

    .credit-note-form-container{
        grid-template-columns: 100%;
    }
  }
  
  /* TABLET AND IPAD QUERY */
  @media (min-width: 768px) and (max-width: 1024px) {
    .form-container{
        min-width: 80%;
    }

    .invoice-row-form-left {
        width: 100%;
    }

    .invoice-row-form-right {
        width: 100%;
    }
    
  }
</style>
