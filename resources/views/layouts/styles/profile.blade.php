<style>
    .profile-detail-content{
    width: 700px;
}

.profile-header{
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 24px;
}

/* NAME CONTAINER */
.name-box {
    margin-top: 24px;
    display: flex;
    align-items: center;
    justify-content: start;
    gap: 8px;
    border: 1px solid var(--main-color);
    border-radius: 8px;
    padding: 16px;
    background-color: white;
}

.name-box img{
    width: 64px;
    height: 64px;
}

/* ---------------------------- */

/* PERSONAL INFROMATION DETAILS */

.personal-information{
    margin-top: 24px;
    border: 1px solid var(--main-color);
    border-radius: 8px;
    padding: 16px;
    background-color: white;
}


.personal-information-details{
    display: grid;
    width: 100%;
    gap: 16px 32px;
    margin-top: 16px;
}

.personal-information-details p{
    font-size: 1rem !important;
    color: black !important;
    font-weight: 500 !important;
    margin-top: 4px !important;
}

.email-phone-container{
    display: flex;
    justify-content: space-between;
    align-items: start;
    width: 100%;
}

.personal-information-details .email-phone-container .email-detail, .personal-information-details .email-phone-container .phone-detail, .personal-information-details .start-date {
    width: 47%;
}

/* ---------------------------- */

/* ADDRESS DETAILS */
.address-information{
    margin-top: 24px;
    border: 1px solid var(--main-color);
    border-radius: 8px;
    padding: 16px;
    background-color: white;
}


.address-information-details{
    display: flex;
    align-items: start;
    justify-content: start;
    flex-wrap: wrap;
    width: 100%;
    gap: 16px 32px;
    margin-top: 16px;
}

.address-information-details p{
    font-size: 1rem !important;
    color: black !important;
    font-weight: 500 !important;
    margin-top: 4px !important;
}

.address-postcode-container{
    display: flex;
    justify-content: space-between;
    align-items: start;
    width: 100%;
}

.city-state-container{
    display: flex;
    justify-content: space-between;
    align-items: start;
    width: 100%;
}

.address-information-details .address-postcode-container .address-detail, .address-information-details .address-postcode-container .postcode-detail, .address-information-details .city-state-container .city-detail, .address-information-details .city-state-container .state-detail {
    width: 47%;
}

/* ---------------------------- */

/* PHONE MEDIA QUERY */
@media only screen and (max-width: 767px) {
    .content .title-edit-container{
        width: 100%;
    }

    .profile-detail-content{
        width: 100%;
    }
    .personal-information-details, .address-information-details{
        width: 100%;
    }

    .email-phone-container, .address-postcode-container, .city-state-container{
        display: grid;
        gap: 16px;
        width: 100%;
    }

    
    .personal-information-details .email-phone-container .email-detail, 
    .personal-information-details .email-phone-container .phone-detail, 
    .personal-information-details .start-date,
    .address-information-details .address-postcode-container .address-detail, .address-information-details .address-postcode-container .postcode-detail, .address-information-details .city-state-container .city-detail, .address-information-details .city-state-container .state-detail {
        width: 100%;
    }
  }
  
  /* TABLET AND IPAD QUERY */
  @media (min-width: 768px) and (max-width: 1024px) {
    .profile-detail-content{
        width: 100%;
    }
    
  }
</style>