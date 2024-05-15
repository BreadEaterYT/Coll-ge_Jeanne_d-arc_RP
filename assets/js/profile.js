document.addEventListener("DOMContentLoaded", function (){
    const box = document.getElementById("profilebox")
    const profilebtn = document.getElementById("profile")
    var toggle = 0

    profilebtn.addEventListener("click", function (){
        if (toggle === 1){
            toggle = 0
        } else {
            toggle = 1
        }
    })

    setInterval(function (){
        if (toggle === 1){
            box.style.top = "75px"
        } else {
            box.style.top = "10000px"
        }
    }, 100);
})