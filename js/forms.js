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

}

/**
 * Submits a form even if multiple forms share the same ID or Name.
 * @param {string} formIdentifier - The ID or Name attribute of the form.
 * @param {number} index - (Optional) Which instance to submit if multiple exist.
 */
function safeSubmit(formIdentifier, index = 0) {
    // 1. Try to find all elements with that ID or Name
    // querySelectorAll is robust against duplicate IDs
    const forms = document.querySelectorAll(`#${formIdentifier}, [name="${formIdentifier}"]`);

    if (forms.length === 0) {
        console.error("Form not found:", formIdentifier);
        return;
    }

    if (forms.length > 1) {
        console.warn(`Found ${forms.length} forms with identifier "${formIdentifier}". Submitting index ${index}.`);
        
        const targetForm = forms[index];
        
        // Ensure the element is actually a FORM
        if (targetForm.tagName === 'FORM') {
            targetForm.submit();
        } else {
            console.error("Target element is not a form.");
        }
    } else {
        // Only one form found, submit it directly
        forms[0].submit();
    }
}

function submitForm(theForm)
{ 
	
	var form = eval("document." + theForm);
    form.submit();
    

}

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
