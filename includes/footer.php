</div><!-- End .content -->

<?php include 'navigation.php'; ?>

<footer role="contentinfo">

</footer>

<script src="http://gvsulib.com/labs/js/jquery.min.js"></script>
<script src="../js/jquery.validate.min.js"></script>
<script src="../js/respond.js"></script>
<script>
$(document).ready(function() {
	
	$(".save_body").find("label").hide();
	
	$(".save_body").hide();
	
	$(".save_link").click(function() {
		$(this).next(".save_body").slideToggle(400);
	});
});
</script>
</body>
</html>