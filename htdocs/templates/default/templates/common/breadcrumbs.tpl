
					<div class="breadcrumbs breadcrumbs-fixed" id="breadcrumbs">

						<ul class="breadcrumb">
							<li>
								<i class="icon-home home-icon"></i>
								<a href="/">Home</a>
							</li>

							{if $module_title}
							<li>
								{if $class_title}
								<a href="/{$module}/">{$module_title}</a>
								{else}
								{$module_title}
								{/if}
							</li>
							{/if}

							{if $class_title}
							<li class="active">{$class_title}</li>
							{/if}

						</ul><!-- /.breadcrumb -->

{*
						<div class="nav-search" id="nav-search">
							<form class="form-search">
								<span class="input-icon">
									<input type="text" placeholder="Search ..." class="nav-search-input" id="nav-search-input" autocomplete="off" />
									<i class="icon-search nav-search-icon"></i>
								</span>
							</form>
						</div><!-- #nav-search -->
*}
					</div>
