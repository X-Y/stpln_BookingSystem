<div class="navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>

			<div class="nav-collapse collapse">
				<ul class="nav">
					<li {{{ (Request::is('/') ? 'class="active"' : '') }}}><a href="{{{ URL::to('') }}}">Home</a></li>
				</ul>

				<ul class="nav pull-right">
					@if(Auth::check())
					<li><a href="">{{Auth::user()->username}}</a></li>
					<li><a href="{{URL::to('user/logout')}}">logout</a></li>
					@else
					<li {{{ (Request::is('user/login') ? 'class="active"' : '') }}}><a href="{{{ URL::to('user/login') }}}">Login</a></li>
					<li {{{ (Request::is('user/create') ? 'class="active"' : '') }}}><a href="{{{ URL::to('user/create') }}}">Register</a></li>
					@endif
				</ul>
			</div>
			<!-- ./ nav-collapse -->
		</div>
	</div>
</div>
