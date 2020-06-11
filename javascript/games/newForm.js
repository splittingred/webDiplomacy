// Get the modal
let modal1 = document.getElementById('delayModal');
let modal2 = document.getElementById('scoringModal');
let modal3 = document.getElementById('variantModal');
let modal4 = document.getElementById('phaseLengthModal');
let modal5 = document.getElementById('betModal');
let modal6 = document.getElementById('anonModal');
let modal7 = document.getElementById('messagingModal');
let modal8 = document.getElementById('botModal');

// Get the button that opens the modal
let btn1 = document.getElementById("modBtnDelays");
let btn2 = document.getElementById("modBtnScoring");
let btn3 = document.getElementById("modBtnVariant");
let btn4 = document.getElementById("modBtnPhaseLength");
let btn5 = document.getElementById("modBtnBet");
let btn6 = document.getElementById("modBtnAnon");
let btn7 = document.getElementById("modBtnMessaging");
let btn8 = document.getElementById("modBtnBot");

// Get the <span> element that closes the modal
let span1 = document.getElementsByClassName("close1")[0];
let span2 = document.getElementsByClassName("close2")[0];
let span3 = document.getElementsByClassName("close3")[0];
let span4 = document.getElementsByClassName("close4")[0];
let span5 = document.getElementsByClassName("close5")[0];
let span6 = document.getElementsByClassName("close6")[0];
let span7 = document.getElementsByClassName("close7")[0];
let span8 = document.getElementsByClassName("close8")[0];

// When the user clicks the button, open the modal
btn1.onclick = function() { modal1.style.display = "block"; }
btn2.onclick = function() { modal2.style.display = "block"; }
btn3.onclick = function() { modal3.style.display = "block"; }
btn4.onclick = function() { modal4.style.display = "block"; }
btn5.onclick = function() { modal5.style.display = "block"; }
btn6.onclick = function() { modal6.style.display = "block"; }
btn7.onclick = function() { modal7.style.display = "block"; }
btn8.onclick = function() { modal8.style.display = "block"; }

// When the user clicks on <span> (x), close the modal
span1.onclick = function() { modal1.style.display = "none"; }
span2.onclick = function() { modal2.style.display = "none"; }
span3.onclick = function() { modal3.style.display = "none"; }
span4.onclick = function() { modal4.style.display = "none"; }
span5.onclick = function() { modal5.style.display = "none"; }
span6.onclick = function() { modal6.style.display = "none"; }
span7.onclick = function() { modal7.style.display = "none"; }
span8.onclick = function() { modal8.style.display = "none"; }

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal1) { modal1.style.display = "none"; }
    if (event.target == modal2) { modal2.style.display = "none"; }
    if (event.target == modal3) { modal3.style.display = "none"; }
    if (event.target == modal4) { modal4.style.display = "none"; }
    if (event.target == modal5) { modal5.style.display = "none"; }
    if (event.target == modal6) { modal6.style.display = "none"; }
    if (event.target == modal7) { modal7.style.display = "none"; }
    if (event.target == modal8) { modal8.style.display = "none"; }
}

function setBotFill(){
    content = document.getElementById("botFill");

    ePress = document.getElementById("pressType");
    pressType = ePress.options[ePress.selectedIndex].value;

    eVariant = document.getElementById("variant");
    variant = eVariant.options[eVariant.selectedIndex].value;

    if (pressType == "NoPress" && variant == 1){
        content.style.display = "block";
    }
    else{
        content.style.display = "none";
        document.getElementById("botBox").checked = false;
    }
}

// Display nextPhaseMinutes paragraph only if phaseSwitchPeriod has selected a period.
nextPhaseMinutesPara = document.getElementById("nextPhaseMinutesPara");

selectPhaseSwitchPeriod = document.getElementById("selectPhaseSwitchPeriod");
phaseSwitchPeriodPara = document.getElementById("phaseSwitchPeriodPara");

selectPhaseMinutes = document.getElementById("selectPhaseMinutes");

nextPhaseMinutesPara.style.display = "none";
phaseSwitchPeriodPara.style.display = "none";


function updatePhasePeriod(){
    if (selectPhaseMinutes.value > 60){
        phaseSwitchPeriodPara.style.display = "none";
        nextPhaseMinutesPara.style.display = "none";
    }
    else{
        phaseSwitchPeriodPara.style.display = "block";

        if (selectPhaseSwitchPeriod.value == -1){
            nextPhaseMinutesPara.style.display = "none";
        }
        else{
            nextPhaseMinutesPara.style.display = "block";
        }
    }


    let phaseLength = parseInt(selectPhaseMinutes.value);


    for (i = 0; i < selectPhaseSwitchPeriod.length; i++){
        let optVal = parseInt(selectPhaseSwitchPeriod.options[i].value);
        if (optVal <= 0 || optVal > phaseLength){
            selectPhaseSwitchPeriod.options[i].hidden = false;
            selectPhaseSwitchPeriod.options[i].disabled = false;
        }
        else{
            selectPhaseSwitchPeriod.options[i].hidden = true;
            selectPhaseSwitchPeriod.options[i].disabled = true;
        }
    }
}




selectPhaseSwitchPeriod.addEventListener("change", updatePhasePeriod);
selectPhaseMinutes.addEventListener("change", updatePhasePeriod);