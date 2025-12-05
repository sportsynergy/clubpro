
 </div> <!--col -->
  </div> <!--row -->

  <div class="row">
    <div class="col">
     <div style="height: 35px"></div>

        <div id="ft">
        <div style="text-align: center;"> 
            
            <span>
            <?php if( ! empty($_SESSION["footermessage"]) ){ ?>
            <?php echo $_SESSION["footermessage"]; ?>
            <?php } ?>
            </span> <br/>
        </div>

        <div style="text-align: center;"> 
            <span class="lighttext">
                Got a question? Let us <a href="mailto:support@sportsynergy.net">know</a>.
            </span> 
        </div>
        <div style="text-align: center; padding-bottom: 15px"> 
            <span class="lighttext">
                &copy;2025 <a href="https://www.sportsynergy.net" target="_blank">Sportsynergy</a> 
            </span> 
        </div>

        </div>


    </div> <!--col -->
  </div> <!--row -->
</div> <!--con -->



<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>

<script type="text/javascript">


var myElement;
switch ("<?=$DOC_TITLE ?>" ) {
  case "Sportsynergy Clubpro":
        myElement = document.getElementById("bookings-navlink");
        myElement.classList.add("active");
    break;
  case "Member Directory":
        myElement = document.getElementById("directory-navlink");
        myElement.classList.add("active");
    break;
  case "Players Wanted":
        myElement = document.getElementById("playerswanted-navlink");
        myElement.classList.add("active");
    break;
 case "Player Ladder":
        myElement = document.getElementById("rankings-navlink");
        myElement.classList.add("active");
    break;
case "Sportsynergy Box Leagues":
        myElement = document.getElementById("leagues-navlink");
        myElement.classList.add("active");
    break;
case "Edit Account":
        myElement = document.getElementById("myaccount-navlink");
        myElement.classList.add("active");
    break;
case "Change Password":
        myElement = document.getElementById("myaccount-navlink");
        if (myElement){
            myElement.classList.add("active");
        } else {
             myElement = document.getElementById("changepassword-navlink");
             myElement.classList.add("active");
        }  
    break;
case "My Buddy List":
        myElement = document.getElementById("myaccount-navlink");
        myElement.classList.add("active");
    break;
 case "My Reservations":
        myElement = document.getElementById("myaccount-navlink");
        myElement.classList.add("active");
    break;
    case "Player Rankings":
        myElement = document.getElementById("pointrankings-navlink");
        myElement.classList.add("active");
    break;
case "Club Teams":
        myElement = document.getElementById("leagues-navlink");
        myElement.classList.add("active");
    break;
case "League Schedule":
        myElement = document.getElementById("leagues-navlink");
        myElement.classList.add("active");
    break;
    
    
    // Site Admin 
case "User Registration":
        myElement = document.getElementById("tools-navlink");
        myElement.classList.add("active");
    break;
case "Account Maintenance":
        myElement = document.getElementById("tools-navlink");
        myElement.classList.add("active");
    break;
case "Email Players":
        myElement = document.getElementById("tools-navlink");
        myElement.classList.add("active");
    break;
case "Box League Setup":
        myElement = document.getElementById("tools-navlink");
        myElement.classList.add("active");
    break;
case "Club Teams Setup":
        myElement = document.getElementById("tools-navlink");
        myElement.classList.add("active");
    break;
 case "Club Preferences":
        myElement = document.getElementById("tools-navlink");
        myElement.classList.add("active");
    break;
case "Record Scores":
        myElement = document.getElementById("tools-navlink");
        myElement.classList.add("active");
    break;
case "Club Events":
        myElement = document.getElementById("tools-navlink");
        myElement.classList.add("active");
    break;
case "Club Reports":
        myElement = document.getElementById("tools-navlink");
        myElement.classList.add("active");
    break;
case "Skill Range Policy Setup":
        myElement = document.getElementById("tools-navlink");
        myElement.classList.add("active");
    break;
case "Scheduling Policy Setup":
        myElement = document.getElementById("tools-navlink");
        myElement.classList.add("active");
    break;

case "Manage Box League":
        myElement = document.getElementById("tools-navlink");
        myElement.classList.add("active");
    break;

    case "Box League History":
        myElement = document.getElementById("tools-navlink");
        myElement.classList.add("active");
    break;

    case "Manage Club Team":
        myElement = document.getElementById("tools-navlink");
        myElement.classList.add("active");
    break;
    
    
    // System Admin
case "Club Dashboard":
        myElement = document.getElementById("clubdashboard-navlink");
        myElement.classList.add("active");
    break; 
case "System Preferences":
        myElement = document.getElementById("systemprefs-navlink");
        myElement.classList.add("active");
    break; 
    case "User Account Maintenance":
        myElement = document.getElementById("accountmaintenance-navlink");
        myElement.classList.add("active");
    break; 
    case "Change Password":
        myElement = document.getElementById("changepassword-navlink");
        myElement.classList.add("active");
    break; 
    case "Club Policy":
        myElement = document.getElementById("clubpolicies-navlink");
        myElement.classList.add("active");
    break; 
  default:
     myElement = document.getElementById("bookings-navlink");
     if (myElement) myElement.classList.add("active");



}

</script>




</body>
</html>

