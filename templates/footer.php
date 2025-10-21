
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
<script src="<?=$_SESSION["CFG"]["wwwroot"]?>/js/forms.js" type="text/javascript"></script>

</body>
</html>

