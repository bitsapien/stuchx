	</div>

	<div class="row-fluid font footer-cr">
		<footer><br><br><hr>
			Copyright 2014, STUCHx <img src="img/aditya.png" class="footer-img" />
		</footer>
	</div>
</div>


        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="js/jquery.js"></script>
	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/jquery-te-1.4.0.min.js" charset="utf-8"></script>
	<script>


		//WYSIWYG Editor
	$(".blog_post").jqte(
		{
			fsizes: ["10", "15", "20"],
			outdent: false,
			left: false,
			center: false,
			right: false,
			indent: false,
			button: "SEND"
	
		}

	);
	// settings of status for editor
	var jqteStatus = true;


	$(document).ready(function(){

		$("#toggler").click(function(){
  			$(this).toggleClass('active, inactive');
		})
	})
	</script>
           <!-- Menu Toggle Script -->
    	<script>
    	$("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    	});

// forget password
	$(document).ready(function() {
			$('#submitEmail').click(function() {
				$.ajax({
					type: "POST",
					cache: false,
					url: "forget.php",
					data: $("#forget_form").serialize(),
					success: function(data) {
							alert(data);
						if(data == "1"){
							
							$('#passForget').modal('hide');
							$('#successModal').modal('show');}
						else{
							console.write(data);
							//$('#failModal').modal('show');}
						
						

					}
				});
					

			});

		});



   	</script>

</body>

</html>

