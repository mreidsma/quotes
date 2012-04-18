
function getSelectedValues (selectObj) {
    //create and initialise total variable (done once)
    var total = 0.0
    
    //loop through EVERY option
    for (var i = 0; i < selectObj.length; i++) {
        //check if selected
        if (selectObj.options[i].selected == true) {
            //if so, add this value to running total (done many times..)
            total += parseFloat(selectObj.options[i].value);
            //note must change value from string to number (float)
        }
    }
//output new total
document.getElementById('total-price').innerHTML=total;    
}

$(document).ready(function()
	{
	 //hide the all of the element with class msg_body
		 $(".msg_body").hide();
		
	 //toggle the componenet with class msg_body
	 $(".msg_head").click(function()
	 {
	  $(this).next(".msg_body").slideToggle(400);
	 });
});
