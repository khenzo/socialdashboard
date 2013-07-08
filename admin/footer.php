<!-- Footer
================================================== -->

	</div> <!-- /.span10 -->
	</div> <!-- /.row -->
	<footer>
		<hr>
		<p>
			<a href="http://jigowatt.co.uk" target="_TOP">&copy; Jigowatt 2009-2012</a>
			<?php
				if ( empty($setTranslate) ) $setTranslate = new Translate();
				$setTranslate->languageSelector();
			?>
		</p>
	</footer>

</div> <!-- /.container -->

	<!-- Le javascript -->
	<script src="../assets-admin/js/bootstrap-transition.js"></script>
	<script src="../assets-admin/js/bootstrap-collapse.js"></script>
	<script src="../assets-admin/js/bootstrap-modal.js"></script>
	<script src="../assets-admin/js/bootstrap-dropdown.js"></script>
	<script src="../assets-admin/js/bootstrap-button.js"></script>
	<script src="../assets-admin/js/bootstrap-tab.js"></script>
	<script src="../assets-admin/js/bootstrap-alert.js"></script>
	<script src="../assets-admin/js/bootstrap-tooltip.js"></script>
	<script src="../assets-admin/js/jquery.ba-hashchange.min.js"></script>
	<script src="../assets-admin/js/jquery.validate.min.js"></script>
	<script src="../assets-admin/js/jquery.placeholder.min.js"></script>
	<script src="../assets-admin/js/jquery.jigowatt.js"></script>

	<!-- admin only -->

	<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="assets/js/excanvas.min.js"></script><![endif]-->
	<script src="assets-admin/js/prettify.js"></script>
	<script src="assets-admin/js/bootstrap-datepicker.js"></script>
	<script src="assets-admin/js/select2/select2.min.js"></script>
	<script src="assets-admin/js/jquery-jigowatt-admin.js"></script>
  </body>
</html>
<?php ob_flush(); ?>