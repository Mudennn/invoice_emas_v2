<style>
    .topbar{
    background-color: white;
    padding: 24px;
    display: flex;
    justify-content: end;
    /* margin-bottom: 32px; */
    border-bottom: 1px solid #e8e8e8;
    /* border-radius: 8px; */
}

.topbar button{
    display: none;
}

.topbar .username a{
    color: black !important;
}

.menu-mobile-button{
    color: black; 
    border: none !important;
    background-color: transparent;
    padding: 0px;
    margin: 0px;
}

/* MOBILE SCREEN */
@media screen and (max-width: 767px) {
    .topbar{
        justify-content: space-between;
    }
    .topbar button{
        display: block;
    }
     
}

/* TABLET AND IPAD QUERY */
@media (min-width: 768px) and (max-width: 1024px) {
    .topbar{
        justify-content: space-between;
    }
    .topbar button{
        display: block;
    }
}
</style>