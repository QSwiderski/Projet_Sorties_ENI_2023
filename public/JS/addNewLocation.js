button = document.getElementById('loc_create');
button.addEventListener('click', CreateNewLocation);

function CreateNewLocation() {
    let keys = ['name', 'dateStart', 'dateFinish', 'dateLimit', 'peopleMax', 'description'];      // tableau des valeurs à transmettre

    let hidden = document.getElementById('hidden')    //trouver la partie de mon doc où cacher le fantome
    let f = document.createElement("form");      //créer un formulaire fantome
    f.name = 'hiddenForm';
    f.method = 'POST';
    f.action = hidden.innerText;

    for (let key of keys) {
        let value = document.getElementById('event_' + key).value; //chercher les valeurs dans la page
        let input = document.createElement('input');               //créer des input pour contenir les données
        input.name = 'mem_'+key;            //renseigner la clé
        input.value = value;         //renseigner la valeur
        f.appendChild(input);        //ajouter ce clé & valeur au fantome
    }

    hidden.appendChild(f);
    //intégrer le fantome et le submit
    // console.log(f);
    f.submit();
}