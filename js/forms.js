function newWindow(newContent)
 {
  winContent = window.open(newContent, 'nextWin', 'right=0, top=20,width=350,height=500, toolbar=no,scrollbars=yes, resizable=no')
 }

 //Stop hiding script from old browsers -->


 function submitFormWithAction(theForm, action)
{

        var form = eval("document." + theForm);
        form.action = action;

        // SUBMIT
        form.submit();

}//end function submitForm()

function submitForm(theForm)
{
    alert(theForm);  
	var form = eval("document." + theForm);
      form.submit();

}//end function submitForm()

function loadReservationPage(month,date,year,site){
	
	var submitForm = getNewSubmitForm();
	 createNewFormElement(submitForm, "month", month);
	 createNewFormElement(submitForm, "date", date);
	 createNewFormElement(submitForm, "year", year);
	 submitForm.action= site;
	 submitForm.submit();
	
}

function getNewSubmitForm(){
	 var submitForm = document.createElement("FORM");
	 document.body.appendChild(submitForm);
	 submitForm.method = "POST";
	 return submitForm;
}
