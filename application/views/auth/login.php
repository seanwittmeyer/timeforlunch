<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		
		<title>Time for Lunch: Help</title>
		
		<!-- Bootstrap core CSS -->
		<link href="/includes/css/bootstrap.min.css" rel="stylesheet">
		
		<!-- Custom styles for this template -->
		<link href="/includes/css/base.css" rel="stylesheet">
		
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		<script src="https://code.jquery.com/jquery-1.11.2.min.js" type="text/javascript" ></script>
	</head>
	<body>
		<div class="container">
			<div class="header">
				<nav>
					<ul class="nav nav-pills pull-right">
						<li role="presentation" class=""><a href="/">Home</a></li>
						<?php if (!$this->ion_auth->logged_in()) { ?> 
						<!--<li role="presentation"><a id="headerfacebookgo" href="/auth/loginfacebook" onclick="$(this).text('Heading to Facebook...');">Log In w/ Facebook</a></li>-->
						<?php } else { ?> 
						<li role="presentation"><a href="/me"><?php $user = $this->ion_auth->user()->row(); echo $user->first_name.' '.$user->last_name; ?></a></li>
						<li role="presentation"><a href="/auth/logout" onclick="$(this).text('See ya later...');">Log Out</a></li>
						<?php } ?> 
					</ul>
				</nav>
				<h3 class="text-muted">Time for Lunch</h3>
			</div>
			<div class="row marketing">
				<h3>Login</h3>

<?php if (!empty($message)) { ?><div id="infoMessage" class="alert alert-info"><?php echo $message;?></div><?php } ?>

<?php echo form_open("auth/login");?>

  <p>
    <?php echo lang('login_identity_label', 'identity');?>
    <?php echo form_input($identity);?>
  </p>

  <p>
    <?php echo lang('login_password_label', 'password');?>
    <?php echo form_input($password);?>
  </p>

  <p>
    <?php echo lang('login_remember_label', 'remember');?>
    <?php echo form_checkbox('remember', '1', FALSE, 'id="remember"');?>
  </p>


  <p><?php echo form_submit('submit', lang('login_submit_btn'));?></p>

<?php echo form_close();?>

<p><a href="forgot_password"><?php echo lang('login_forgot_password');?></a></p>
			</div>
			<footer class="footer">
				<p>ISU Architecture / Team Awesome</p>
			</footer>
		</div> <!-- /container -->
		


		<!-- Make the site interactive -->
		<script src="/includes/js/bootstrap.min.js"></script>
		<script type="text/javascript">
			$('.tt').tooltip();
			$('.ttb').tooltip({
				placement: 'bottom'
			});
			$('.ttr').tooltip({
				placement: 'right'
			});
			function formatPhone(obj) {
				var numbers = obj.value.replace(/\D/g, ''),
					char = {0:'(',3:') ',6:' - '};
				obj.value = '';
				for (var i = 0; i < numbers.length; i++) {
					obj.value += (char[i]||'') + numbers[i];
				}
			}
			$('#loginmodal').on('shown.bs.modal', function () {
				$('#identity').focus();
			})
			$(function () {
				$('[data-toggle="popover"]').popover()
			})
			$('#neworder').on('shown.bs.modal', function () {
				$('#restaurant').focus();
				var newordermodalheight = ($(window).height() * .8);
				$('#neworder .modal-content').css("height", newordermodalheight);
				//alert(newordermodalheight);
			})
		</script>
	</body>
</html>