/**
 * jquery needed
 */
$(document).ready( function(){
    const close_warning=document.querySelector("#close-warnings");
    if(close_warning != null){
        close_warning.addEventListener('click', hideMessageblock);
    }
    function hideMessageblock(){
        $("#weatherwarnings").slideUp("slow");
        document.cookie = "closed Warnings=true";
    };
});