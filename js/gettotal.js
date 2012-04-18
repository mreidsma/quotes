


/* This script and many more are available free online at
The JavaScript Source :: http://javascript.internet.com
Created by: Kevin Hartig :: http://www.grafikfx.net/ */

// Calculate the total for items in the form which are selected.
function calculateTotal(inputItem) {
  with (inputItem.form) {
    // Process each of the different input types in the form.
    if (inputItem.type == "checkbox") {   
    	if (inputItem.checked == false) {   // Item was uncheck. Subtract item value from total.
          calculatedTotal.value = eval(calculatedTotal.value) - eval(inputItem.value);
      } else {   // Item was checked. Add the item value to the total.
          calculatedTotal.value = eval(calculatedTotal.value) + eval(inputItem.value);
      } 
      } else {
    // Process select menus.
      // Subtract the previously selected radio button value from the total.
      calculatedTotal.value = eval(calculatedTotal.value) - eval(previouslySelectedRadioButton.value);
      // Save the current radio selection value.
      previouslySelectedRadioButton.value = eval(inputItem.value);
      // Add the current radio button selection value to the total.
      calculatedTotal.value = eval(calculatedTotal.value) + eval(inputItem.value);
    } 

    // Total value should never be less than 0.
    if (calculatedTotal.value < 0) {
      InitForm();
    }

    // Return total value.
    return(formatCurrency(calculatedTotal.value));
  }
}

// Format a value as currency.
function formatCurrency(num) {
  num = num.toString().replace(/\$|\,/g,'');
  if(isNaN(num))
     num = "0";
  sign = (num == (num = Math.abs(num)));
  num = Math.floor(num*100+0.50000000001);
  cents = num%100;
  num = Math.floor(num/100).toString();
  if(cents<10)
      cents = "0" + cents;
  for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
      num = num.substring(0,num.length-(4*i+3)) + ',' + num.substring(num.length-(4*i+3));
  return (((sign)?'':'-') + '$' + num + '.' + cents);
}

// This function initialzes all the form elements to default values.
function InitForm() {
  // Reset values on form.
  document.cartoonifyme.total.value='$0';
  document.cartoonifyme.calculatedTotal.value=0;
  document.cartoonifyme.previouslySelectedRadioButton.value=0;

  // Set all checkboxes and radio buttons on form to unchecked.
  for (i=0; i < document.cartoonifyme.elements.length; i++) {
    if (document.selectionForm.elements[i].type == 'checkbox' | document.selectionForm.elements[i].type == 'radio') {
      document.selectionForm.elements[i].checked = false;
    }
  }
}







	

