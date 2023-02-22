//JS présent sur base.html.twig

const allbuttons = document.querySelectorAll('button[id = glowing-btn]')
allbuttons.forEach(btn=> {
    btn.addEventListener('mouse', playNeonSound());
});

function playNeonSound(){
    console.log('neon')
    let neonAudio = document.getElementById("neon-audio");
    neonAudio.play();
    return null;
}

//konami audio
let bgaudio = document.getElementById("bg-audio");
//Haut, haut, bas, bas, gauche, droite, gauche, droite, B, A
var k = [38, 38, 40, 40, 37, 39, 37, 39, 66, 65],
    n = 0;
document.addEventListener('keydown',function (e) {
    if (e.keyCode === k[n++]) {
        if (n === k.length) {
            //NE PAS METTRE A FOND JE VOUS AURAI PREVENU
            bgaudio.volume = 0.10;
            bgaudio.play();
            n = 0;
            return false;
        }
    }
    else {
        n = 0;
    }
});