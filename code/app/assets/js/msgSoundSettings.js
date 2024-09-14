let msgSoundFrom = document.querySelector("#msg-sound-form");

if (msgSoundFrom) {
    let msgSound = msgSoundFrom.querySelector("#msg-sound");
    msgSound.value = localStorage.getItem('msgSound');

    msgSound.addEventListener("change", (event) => {
        let numSound = msgSound.value;
        var audio = new Audio('/app/assets/audio/'+ numSound + '.mp3');
        audio.play();
        localStorage.setItem('msgSound', msgSound.value);
    })
}
