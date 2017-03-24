<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		
		<title>Time for Lunch - Archive</title>
		
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
						<li role="presentation"><a data-toggle="modal" href="#loginmodal" data-target="#loginmodal">Log In</a></li>
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
					<h4 class="text-center">All open and cancelled orders</h4>
					<p>Orders that faded into oblivion because they weren't claimed and delivered.</p>
					<table class="table table-condensed">
						<thead>
							<tr>
								<th>Time</th>
								<th>Who</th>
								<th>What</th>
								<th>Where</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php //get our orders
								$orders = $this->shared->get_orders(false,array('status'=>'open'));
								foreach ($orders as $order) { ?> 
							<tr class="pohover" data-toggle="popover" data-html="true" data-placement="top" data-trigger="hover" title="<?php echo $this->shared->q($order['restaurant']); ?>" data-content="<strong>Where</strong>: <?php echo $this->shared->q($order['restaurant']); ?><br><strong>What:</strong> <?php echo $this->shared->q($order['order']); ?><br><strong>Cost(ish):</strong> $<?php echo $this->shared->q($order['cost']); ?><br><strong>Tip:</strong> <?php echo $this->shared->q($order['tip']); ?><br><strong>Can be trusted?:</strong> <?php echo ($order['order'] == 'off') ? '?':'They say so'; ?>">
								<th scope="row"><span class="label label-<?php $_time = time() - $order['time']; echo ($_time < 7200) ? ($_time < 3600) ? ($_time < 1800) ? 'success' : 'default' : 'warning' : 'danger'; ?>"><?php echo $this->shared->twitterdate($order['time']); ?></span></th>
								<td><?php $_user = $this->ion_auth->user($order['user'])->row(); echo $_user->first_name.' '.$_user->last_name; ?></td>
								<td><?php echo $order['restaurant']; ?></td>
								<td><?php echo $order['location']; ?></td>
								<td> </td>
							</tr>
							<?php } ?> 
						</tbody>
					</table>					
					<?php $orders = $this->shared->get_orders(false,array('status'=>'inprogress')); if ($orders !== false and count($orders) != 0) { ?>

					<h4 class="text-center">Orders in progress of delivery!</h4>
					<p>These orders and their deliverers are about to fight hunger.</p>
					<table class="table table-condensed">
						<thead>
							<tr>
								<th>When?</th>
								<th>Go!</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php //get our orders
								foreach ($orders as $order) { ?> 
							<tr class="pohover" data-toggle="popover" data-html="true" data-placement="top" data-trigger="hover" title="<?php echo $this->shared->q($order['restaurant']); ?>" data-content="<strong>Where</strong>: <?php echo $this->shared->q($order['restaurant']); ?><br><strong>What:</strong> <?php echo $this->shared->q($order['order']); ?><br><strong>Cost(ish):</strong> $<?php echo $this->shared->q($order['cost']); ?><br><strong>Tip:</strong> <?php echo $this->shared->q($order['tip']); ?><br><strong>Can be trusted?:</strong> <?php echo ($order['order'] == 'off') ? '?':'They say so'; ?>">
								<th scope="row"><span class="label label-<?php $_time = time() - $order['timeclaimed']; echo ($_time < 7200) ? ($_time < 3600) ? ($_time < 1800) ? 'success' : 'default' : 'warning' : 'danger'; ?>"><?php echo $this->shared->twitterdate($order['timeclaimed']); ?></span></th>
								<td><?php $_ouser = $this->ion_auth->user($order['user'])->row(); $_cuser = $this->ion_auth->user($order['claimuser'])->row(); echo $_cuser->first_name.' '.$_cuser->last_name; ?> -> <?php echo $order['restaurant']; ?> -> <?php echo $_ouser->first_name.' '.$_ouser->last_name; ?></td>
								<td><?php if ($this->ion_auth->logged_in() && $user->id == $order['claimuser']) { ?><button type="button" class="btn btn-primary btn-xs tt" onclick="unclaim(<?php echo $order['id']; ?>, this);" title="When you click this, <?php echo $_user->first_name; ?> will be notified that you are reopening their lunch order, sad day.">Unclaim?</button> <button type="button" class="btn btn-primary btn-xs tt" onclick="marksuccess(<?php echo $order['id']; ?>, this);" title="Click to mark this order as delivered and as a success!">Delivered?</button> <?php } if ($this->ion_auth->logged_in() && $user->id == $order['user']) { ?><button type="button" class="btn btn-primary btn-xs tt" title="Click to mark this order as delivered and as a success!" onclick="marksuccess(<?php echo $order['id']; ?>, this);">Delivered?</button> <?php } ?></td>
							</tr>
							<?php } ?> 
						</tbody>
					</table>					
					<?php } ?>

					<?php $orders = $this->shared->get_orders(false,array('status'=>'complete')); if ($orders !== false and count($orders) != 0) { ?>

					<h4 class="text-center">Completed Orders</h4>
					<p>Hunger was defeated when these orders were successfully delivered. Deliver an order above to see your name on this hall of fame.</p>
					<table class="table table-condensed">
						<thead>
							<tr>
								<th>Successful Orders</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php //get our orders
								foreach ($orders as $order) { ?> 
							<tr>
								<td><?php $_ouser = $this->ion_auth->user($order['user'])->row(); $_cuser = $this->ion_auth->user($order['claimuser'])->row(); echo $_cuser->first_name.' '.$_cuser->last_name; ?> delivered <?php echo $order['restaurant']; ?> to <?php echo $_ouser->first_name.' '.$_ouser->last_name; ?> <i>(<?php echo $this->shared->twitterdate($order['timeclaimed']); ?>)</i></td>
								<td><?php if ($this->ion_auth->logged_in() && $user->id == $order['user']) { ?><button type="button" class="btn btn-primary btn-xs tt" onclick="dispute(<?php echo $order['id']; ?>, this);" title="Is this a false claim? This doesn't do anything yet...">Not a success?</button> <?php } ?></td>
							</tr>
							<?php } ?> 
						</tbody>
					</table>					
					<?php } ?>
				<!-- soon <div class="col-lg-6">
					<h4 class="text-center">These folks want lunch</h4>
					<p>Get karma by going out and delivering lunch for these fine people. Warning, tips and friendships might ensue.</p>
				</div>
				<div class="col-lg-6">
					<h4 class="text-center">These people are getting lunch</h4>
					<p>Add an order for one of the restaurants below and hopefully one of these people will deliver it to you.</p>
				</div>
				-->
			</div>
			<footer class="footer">
				<p><strong>What is this?</strong> Great question! Time for Lunch is a self organizing lunch delivery network that connects people who like lunch.</p>
				<p>ISU Architecture / Team Awesome</p>
				<p>This is an open source platform, <a href="https://github.com/seanwittmeyer/timeforlunch" target="_blank">see it evolve at GitHub</a>.</p>
			</footer>
		</div> <!-- /container -->
		
		<!-- Login Popup -->
		<div class="modal fade" id="loginmodal" tabindex="-1" role="dialog" aria-labelledby="loginmodal" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">Please sign in</h4>
					</div>
					<div class="modal-body">
						<?php echo form_open("auth/login");?>
						<div class="panel panel-default">
							<div class="panel-body">
								<a href="/auth/loginfacebook" class="btn btn-lg btn-primary btn-block" onclick="$(this).text('signing you in...');">Log in / Sign up with Facebook &rarr;</a>
							</div>
						</div>
						<h4>or...</h4>
						<div class="panel panel-default">
							<div class="panel-body">
								<label for="identity" class="sr-only">Email address</label>
								<input type="email" id="identity" name="identity" class="form-control" placeholder="Email address" required autofocus>
								<label for="inputPassword" class="sr-only">Password</label>
								<input type="password" name="password" class="form-control" placeholder="Password" required>
								<label><a href="/auth/forgot_password">Forgot your password?</a></label>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="remember" value="1"> Remember me
									</label>
								</div>
								<div class="btn-group btn-block" role="group" aria-label="logincreateaccount">
									<button class="btn btn-lg btn-info btn-block" style="width:45%;margin-top:0;" type="submit" data-loading-text="checking...">Log in &rarr;</button>
									<a href="#" class="btn btn-lg btn-default btn-block hidden-xs" style="width:10%;margin-top:0;" >or</a>
									<button class="btn btn-lg btn-success btn-block tt" style="width:45%;margin-top:0;"  data-toggle="tooltip" title="By doing this, you are saying you'll be nice." type="submit">Sign up &rarr;</button>
								</div>
							</div>
						</div>
						</form>
					</div>
					<!--<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary">Save changes</button>
					</div>-->
				</div>
			</div>
		</div>
		<!-- End Login Popup -->

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
			$(function () {
				$('[data-toggle="popover"]').popover()
			})
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
			$('#newordermodal').on('shown.bs.modal', function () {
				$('#restaurant').focus();
				var newordermodalheight = ($(window).height() * .8);
				$('#newordermodal .modal-content').css("height", newordermodalheight);
				//alert(newordermodalheight);
			})
			function postOrder() {
				$('#neworderbuttons').hide(); 
				$('#neworderloading').show();  
				// get vars and ajax post to api
				// wait for reply
				$('#neworderloading').hide(); 
				$('#newordersuccess').show();
			}
			// New Order
			$('#addneworder').click(function() {
				$.ajax({
					type: "POST",
					beforeSend: function() {
						$('#neworderbuttons').hide(); 
						$('#neworderfail').hide(); 
						$('#neworderloading').show();
					},
					url: "/api/orders/new",
					data: $("#neworder").serialize(),
					statusCode: {
						200: function() {
							$('#neworderloading').hide(); 
							$('#newordersuccess').show();
							window.location.assign("<?php echo base_url(); ?>");
							var newclass = $('#neworder').serializeObject();
							$('#neworder').each(function(){this.reset();});
							alert('success');
							//if (newclass['new']['open'] == 'on') { var newicon = 'ok teal'; } else { var newicon = 'remove red'; }
							//var i = 1;
							//var newblocks = '';
							//for (b in newclass['new']['blocks']) { if (i>1) newblocks = newblocks + ', '; newblocks = newblocks + b; i++; }
							//var newrow = '<tr><td><i class="icon-' + newicon + '"></i></td><td><strong>' + newclass['new']['title'] + '</strong></td><td>' + newclass['new']['soft'] + ' / <strong>' + newclass['new']['hard'] + '</strong></td><td>' + newblocks + '</td><td>$' + newclass['new']['amount'] + '</td><td></td></tr>';
							//$('#tablebottom').before(newrow);
							//$('.fncheckbox').each(function(){$(this).removeAttr('checked')});
							//$('#fnalert').html('<button type="button" class="close" data-dismiss="alert">&times;</button><i class="icon-ok teal"></i> ' + newclass['new']['title'] + ' has been added').removeClass('hidden');
							//$('#fnmessage').text('');
							//$('#newclass').addClass('hidden');
							//$('#fnsubmit').removeClass('hidden').button('reset');
							// reload page with order listed
						},
						403: function() {
							//$('#fnmessage').text('Well Snap! Couldn\'t create the class. Retry?');
							//$('#fnsubmit').removeClass('hidden').button('reset');
							$('#neworderloading').hide(); 
							$('#neworderfail').show();
							$('#neworderbuttons').show(); 
						},
						404: function() {
							//$('#fnmessage').text('Well Snap! Couldn\'t create the class. Retry?');
							//$('#fnsubmit').removeClass('hidden').button('reset');
							$('#neworderloading').hide(); 
							$('#neworderfail').show();
							$('#neworderbuttons').show(); 
						}
					}
				});
			});
			// Claim the order
			function claim(orderId, buttonElement) {
				$.ajax({
					type: "POST",
					beforeSend: function() {
						$(buttonElement).text('claiming...'); 
					},
					url: "/api/claim/"+orderId,
					statusCode: {
						200: function() {
							$(buttonElement).text('done!').addClass('btn-success'); 
							window.location.assign("<?php echo base_url(); ?>");
						},
						403: function() {
							$(buttonElement).text('login first!').addClass('btn-danger');
							$('#loginmodal').modal('show');
						},
						404: function() {
							$(buttonElement).text('login first!').addClass('btn-danger');
							$('#loginmodal').modal('show');
						}
					}
				});
			}
			// mark as success
			function marksuccess(orderId, buttonElement) {
				$.ajax({
					type: "POST",
					beforeSend: function() {
						$(buttonElement).text('becoming awesome...'); 
					},
					url: "/api/success/"+orderId,
					statusCode: {
						200: function() {
							$(buttonElement).text('done!').addClass('btn-success'); 
							window.location.assign("<?php echo base_url(); ?>");
						},
						403: function() {
							$(buttonElement).text('uh oh, retry?').addClass('btn-danger');
							$('#loginmodal').modal('show');
						},
						404: function() {
							$(buttonElement).text('fail, retry?').addClass('btn-danger');
							$('#loginmodal').modal('show');
						}
					}
				});
			}
			// unclaim an order
			function unclaim(orderId, buttonElement) {
				$.ajax({
					type: "POST",
					beforeSend: function() {
						$(buttonElement).text('becoming sad...'); 
					},
					url: "/api/unclaim/"+orderId,
					statusCode: {
						200: function() {
							$(buttonElement).text('done!').addClass('btn-success'); 
							window.location.assign("<?php echo base_url(); ?>");
						},
						403: function() {
							$(buttonElement).text('uh oh, retry?').addClass('btn-danger');
							$('#loginmodal').modal('show');
						},
						404: function() {
							$(buttonElement).text('fail, retry?').addClass('btn-danger');
							$('#loginmodal').modal('show');
						}
					}
				});
			}
		</script>
	</body>
</html>