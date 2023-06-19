
///TODO: si on fait plusieurs pages, il faut call cette fonction après le chargement de la page
function getAllBuyButtons(){
    let buttons = document.querySelectorAll('[name="buy"]');

    buttons.forEach(button => {
        //Get the form the button is in
        let form = button.parentElement;
        let id_skin = form.querySelector('.id_skin').value;

        //Add event listener to the button
        button.addEventListener('click', function(){
           sendRequest({id_skin: id_skin, token: token},'POST', '/shop').then((response) => {
                switch(response.status){
                     case 200:
                          window.alert("Skin bought!");
                          break;
                     case 402:
                          window.alert("You don't have enough money!");
                          break;
                     default:
                          window.alert("Something went wrong!");
                          break;
                }
           });
        });
    });
}


getAllBuyButtons();