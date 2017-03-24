<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		
		<title>Time for Lunch</title>
		
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
						<li role="presentation" class="active"><a href="/">Home</a></li>
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
			<div class="jumbotron">
				<h1>Ride Share for Lunch</h1>
				<p class="lead"><strong>Time for lunch</strong> is a bulletin board for lunch orders and offers to go and pick them up. Make friends, make deliveries and tips, and enjoy lunch. All this for free (plus the cost of your lunch).</p>
				<p><a class="btn btn-lg btn-success" data-toggle="modal" data-target="#newordermodal">Place your order &rarr;</a></p>
			</div>
			<div class="row marketing">
					<?php //get our orders
						$diff = time()-43200;
						$orders = $this->shared->get_orders(false,array('status'=>'open','time >'=>$diff));
						if ($orders !== false and count($orders) != 0) {
						?>
					<h4 class="text-center">These folks want lunch</h4>
					<p>Get karma by going out and delivering lunch for these fine people. Warning, tips and friendships might ensue.</p>
					<table class="table table-condensed">
						<thead>
							<tr>
								<th>Fresh?</th>
								<th>Who</th>
								<th>What</th>
								<th>Where</th>
								<th>Go!</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($orders as $order) { ?> 
							<tr class="pohover" data-toggle="popover" data-html="true" data-placement="top" data-trigger="hover" title="<?php echo $this->shared->q($order['restaurant']); ?>" data-content="<strong>Where</strong>: <?php echo $this->shared->q($order['restaurant']); ?><br><strong>What:</strong> <?php echo $this->shared->q($order['order']); ?><br><strong>Cost(ish):</strong> $<?php echo $this->shared->q($order['cost']); ?><br><strong>Tip:</strong> <?php echo $this->shared->q($order['tip']); ?><br><strong>Can be trusted?:</strong> <?php echo ($order['order'] == 'off') ? '?':'They say so'; ?>">
								<th scope="row"><span class="label label-<?php $_time = time() - $order['time']; echo ($_time < 7200) ? ($_time < 3600) ? ($_time < 1800) ? 'success' : 'default' : 'warning' : 'danger'; ?>"><?php echo $this->shared->twitterdate($order['time']); ?></span></th>
								<td><?php $_user = $this->ion_auth->user($order['user'])->row(); echo $_user->first_name.' '.$_user->last_name; ?></td>
								<td><?php echo $order['restaurant']; ?></td>
								<td><?php echo $order['location']; ?></td>
								<td> <button type="button" class="btn btn-primary btn-xs tt" title="When you click this, <?php echo $_user->first_name; ?> will be notified that you are going to deliver their lunch. Rise to the challenge!" onclick="claim(<?php echo $order['id']; ?>, this);">Claim</button> </td>
							</tr>
							<?php } ?> 
						</tbody>
					</table>	
					<?php } else { ?>				
					<h4 class="text-center">These folks want lunch</h4>
					<p class="alert alert-info">No lunch orders! Hungry? <button class="btn btn-xs btn-info" data-toggle="modal" data-target="#newordermodal">Add an order!</button></p>
					<?php } ?>				
					<p>This is a list of orders from the last 12 hours. <a href="<?php echo base_url('archive'); ?>">See all?</a></p>
					<?php $orders = $this->shared->get_orders(false,array('status'=>'inprogress','time >'=>$diff)); if ($orders !== false and count($orders) != 0) { ?>

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
								<td><?php if ($this->ion_auth->logged_in() && $user->id == $order['claimuser']) { ?><button type="button" class="btn btn-primary btn-xs tt" onclick="unclaim(<?php echo $order['id']; ?>, this);" title="When you click this, <?php echo $_ouser->first_name; ?> will be notified that you are reopening their lunch order, sad day.">Unclaim?</button> <button type="button" class="btn btn-primary btn-xs tt" onclick="marksuccess(<?php echo $order['id']; ?>, this);" title="Click to mark this order as delivered and as a success!">Delivered?</button> <?php } if ($this->ion_auth->logged_in() && $user->id == $order['user']) { ?><button type="button" class="btn btn-primary btn-xs tt" title="Click to mark this order as delivered and as a success!" onclick="marksuccess(<?php echo $order['id']; ?>, this);">Delivered?</button> <?php } ?></td>
							</tr>
							<?php } ?> 
						</tbody>
					</table>					
					<?php } ?>

					<?php 
						$diff = time()-604800;
						$orders = $this->shared->get_orders(false,array('status'=>'complete','time >'=>$diff)); if ($orders !== false and count($orders) != 0) { ?>

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
		<!-- New Order Popup -->
		<div class="modal fade" id="newordermodal" tabindex="-1" role="dialog" aria-labelledby="neworder" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="myModalLabel">Handle your hunger here...</h4>
					</div>
					<div class="modal-body">
					<form id="neworder" >
						<?php if (!$this->ion_auth->logged_in()) { ?><h4>Want to log in real fast?</h4><p>This is a community based on trust. Login to make things easy and prove you are worthy to play :)</p><?php } ?> 
						<div class="panel panel-default">
							<div class="panel-body">
								<?php if ($this->ion_auth->logged_in()) { ?> 
								<a href="#" class="btn btn-lg btn-link btn-block" >Ordering as <?php echo $user->first_name.' '.$user->last_name; ?>. Great!</a>
								<input id="user" name="user" type="hidden" value="<?php echo $user->id; ?>" /><!-- let's match the user with the session data to check on things -->
								<?php } else { ?> 
								<input id="user" name="user" type="hidden" value="anon" /><!-- anon is only non-numeric value we'll handle, which means we'll simply mine the signup from the about section -->
								<div class="btn-group btn-block" role="group" aria-label="signingroup">
									<a href="/auth/loginfacebook" class="btn btn-lg btn-primary btn-block ttb" style="width:45%;margin-top:0;" onclick="$(this).text('One moment chief...');" data-toggle="tooltip" title="Only takes 38 milliseconds, do it!">Log in with Facebook &rarr;</a>
									<a href="#" class="btn btn-lg btn-default btn-block hidden-xs" style="width:10%;margin-top:0;" >or</a>
									<a href="#" class="btn btn-lg btn-info btn-block" data-toggle="modal" style="width:45%;margin-top:0;" href="#loginmodal" data-target="#loginmodal" onclick="$('#newordermodal').modal('hide'); return false;">Normal Log in &rarr;</a>
								</div>
								<?php } ?>
							</div>
						</div>
						<h4>About Your Order</h4>
						<p>What are you looking for? Be sure to fill out everything here so there is no confusion.</p>
						<div class="panel panel-default">
							<div class="panel-body">
									<div class="form-group">
										<label for="restaurant" class="">Restaurant* (<a href="http://www.yelp.com/ames-ia-us" target="_blank">ideas...</a>)</label>
										<div class="">
											<input type="text" class="form-control" id="restaurant" name="restaurant" placeholder="Who makes the food you want?">
										</div>
									</div>
									<div class="form-group">
										<label for="order" class="">Order*</label>
										<div class="">
											<textarea type="text" class="form-control" id="order" name="order" placeholder="What do you want, be specific..."></textarea>
										</div>
									</div>
									<div class="form-group">
										<label for="cost" class="">Cost* (<a href="http://www.yelp.com/ames-ia-us" target="_blank">lookup here...)</a> or <a href="#" data-toggle="popover" title="Paying for Lunch" data-content="You need to work with the delivery person to figure out how to pay for your meal. You could call your order in to the restaurant or have cash ready. Payments are not handled on this site. When someone claims your order, they will likely be in touchto figure this out." data-trigger="focus" data-placement="bottom">(how does money work?</a>)</label>
										<div class="">
											<div class="input-group">
												<span class="input-group-addon">$</span>
												<input type="amount" class="form-control ttb" id="cost" name="cost" placeholder="How much will this cost, about?" data-toggle="tooltip" title="Over estimate and don't forget about taxes!">
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="tip" class="">Tip*</label>
										<div class="">
											<div class="input-group">
												<span class="input-group-addon">$</span>
												<input type="amount" class="form-control" id="tip" name="tip" placeholder="$2 or keep the change...">
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="">
											<div class="checkbox">
												<label class="ttr"  data-toggle="tooltip" title="When you post an order, you will need to pay for it, along with any tip you promise.">
													<input type="checkbox" id="trusted" name="trusted"> I can be trusted*
												</label>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="">
											<div class="checkbox">
												<label>
													<input type="checkbox" id="prepaid" name="prepaid"> Pickup only, this order has been prepaid (by phone, web, etc)
												</label>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label for="notes" class="">Special Instructions?</label>
										<div class="">
											<textarea type="amount" class="form-control" id="notes" name="notes" placeholder="Luck favors the prepared..."></textarea>
										</div>
									</div>
									<!--<div class="form-group">
										<div class="col-sm-offset-2 col-sm-10">
											<button type="submit" class="btn btn-default">Sign in</button>
										</div>
									</div>-->
							</div>
						</div>
						<h4>About You</h4>
						<p>Your contact details and location are useful in getting your food to you. We show this information to people who offer to deliver your order.</p>
						<div class="panel panel-default">
							<div class="panel-body">
								<div class="form-group">
									<label for="orderemail" class="">Email Address (you'll be notified about changes) </label>
									<div class="">
										<input type="email" class="form-control" id="orderemail" name="orderemail" value="<?php if (isset($user->email) && !empty($user->email)) echo $user->email; ?>" placeholder="Not for spam, unless you ordered it...">
									</div>
								</div>
								<?php if (!$this->ion_auth->logged_in()) { ?> 
								<div class="form-group">
									<label for="orderpassword" class="">Set a password</label>
									<div class="">
										<input type="password" class="form-control" id="orderpassword" name="orderpassword" value="<?php if (isset($user->email) && !empty($user->email)) echo $user->email; ?>" placeholder="ISUrox44!">
									</div>
								</div>
								<div class="form-group">
									<label for="first_name" class="">What's your First Name?</label>
									<div class="">
										<input type="text" class="form-control" id="first_name" name="first_name" value="<?php if (isset($user->first_name) && !empty($user->first_name)) echo $user->first_name; ?>" placeholder="Don't be shy...">
									</div>
								</div>
								<div class="form-group">
									<label for="last_name" class="">...and last name</label>
									<div class="">
										<input type="text" class="form-control" id="last_name" name="last_name" value="<?php if (isset($user->last_name) && !empty($user->last_name)) echo $user->last_name; ?>" >
									</div>
								</div>
								<?php } ?>
								<div class="form-group">
									<label for="phone" class="">Phone Number (for delivery/order questions)</label>
									<div class="">
										<input type="phone" class="form-control" id="phone" name="phone" onchange="formatPhone(this);" onkeydown="formatPhone(this);" value="<?php if (isset($user->phone) && !empty($user->phone)) echo $user->phone; ?>" placeholder="555-867-5309">
									</div>
								</div>
								<div class="form-group">
									<label for="location" class="">Location</label>
									<div class="">
										<input type="text" class="form-control" id="location" name="location" value="<?php if (isset($user->location) && !empty($user->location)) echo $user->location; ?>" placeholder="Campus building and room number">
									</div>
								</div>
							</div>
						</div>
					</div><!-- /modal body -->
					<div class="modal-footer">
						<div id="neworderfail" class="alert alert-danger " style="display: none;" role="alert">Order not posted, fill out everything above and try again.</div>
						<div id="newordersuccess" class="alert alert-success " style="display: none;" role="alert">Great success, order posted.</div>
						<div id="neworderloading" class="alert alert-info " style="display: none;" role="alert">working...</div>
						<div id="neworderbuttons">
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							<button type="reset" class="btn btn-default" >Reset</button>
							<button type="button" class="btn btn-primary tt" id="addneworder" data-toggle="tooltip" title="Click this button and the world will see your order, and hopefully someone will pick it up and bring it to you. Get your cash ready!">Add my order!</button>
						</div>
					</div>
					</form>
				</div>
			</div>
		</div>
		<!-- End New Order Popup -->

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