// let message from chat view
let timer;
let isPaused = false;

window.addEventListener('wheel', function(){
	isPaused = true;
	clearTimeout(timer);
	timer = window.setTimeout(function(){
		isPaused = false;
	}, 1000);
})

window.setInterval(function(){
	if(!isPaused && messages) {
        messages.scrollTop = messages.scrollHeight;
	}
}, 500);
