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

      var form = eval("document." + theForm);
      form.submit();

}//end function submitForm()
