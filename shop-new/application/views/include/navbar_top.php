    <div class="navbar-top">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-sm-6 col-xs-6 col-md-6">
                    <div class="pull-left ">
                        <ul class="userMenu ">
                            <li><a href="#"> <span class="hidden-xs">HELP</span><i
                                    class="glyphicon glyphicon-info-sign hide visible-xs "></i> </a></li>
                            <li class="phone-number"><a href="callto:+12025550151"> <span> <i
                                    class="glyphicon glyphicon-phone-alt "></i></span> <span class="hidden-xs"
                                                                                             style="margin-left:5px"> +1-202-555-0151 </span>
                            </a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6 col-sm-6 col-xs-6 col-md-6 no-margin no-padding">
                    <div class="pull-right">
                        <ul class="userMenu">
                        <?php if(!is_numeric($this->id_customer)) : ?>
                        	<li><a href="account-1.html"><span class="hidden-xs"> My Account</span> <i class="glyphicon glyphicon-user hide visible-xs "></i></a></li>
                            <li><a href="#" data-toggle="modal" data-target="#ModalLogin"> <span class="hidden-xs">Sign In</span><i class="glyphicon glyphicon-log-in hide visible-xs "></i> </a></li>
                            <li class="hidden-xs"><a href="#" data-toggle="modal" data-target="#ModalSignup"> Create Account </a></li>                                
                        <?php else : ?>
                            <li><a href="account-1.html"><span class="hidden-xs"> My Account</span> <i class="glyphicon glyphicon-user hide visible-xs "></i></a></li>
                            <li class="dropdown hasUserMenu">
                            	<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> 
                                <i class="glyphicon glyphicon-log-in hide visible-xs "></i>
                                <span class="hidden-xs">Hi, <?php echo userNameByCustomer($this->id_customer); ?> <b class="caret"></b></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a href="account.html"> <i class="fa fa-user"></i> Account</a></li>
                                    <li><a href="account.html"><i class="fa fa fa-cog"></i> Profile</a></li>
                                    <li><a href="my-address.html"><i class="fa fa-map-marker"></i> Addresses</a></li>
                                    <li><a href="order-list.html"><i class="fa  fa-calendar"></i> Orders</a></li>
                                    <li><a href="wishlist.html" title="My wishlists"><i class="fa fa-heart"></i> My wishlists</a></li>
                                    <li class="divider"></li>
                                    <li><a href="javascript:void(0)" onClick="logOut()"><i class="fa  fa-sign-out"></i> Log Out</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>    
                        </ul>
                    </div>
               </div>
                
            </div>
        </div>
    </div>
    <!--/.navbar-top-->
    <script>
    	function logOut()
		{
			$.ajax({
				url:"<?php echo base_url(); ?>shop/login/logOut"	,
				type:"GET", cache:"false", success: function(rs){
					window.location.reload();	
				}
			});
		}
    </script>