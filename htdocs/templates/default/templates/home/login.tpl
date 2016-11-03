
{literal}
<script type="text/javascript">
	<!--
		if (top.location!= self.location) {
			top.location = self.location.href
		}
	//-->
</script>
{/literal}

{if is_array($errors) && count($errors)}
<div id="errors">
<ol>
{foreach item=error from=$errors}
  <li> {$error} </li>
{/foreach}
</ol>
</div>
{/if}

<br />
<div class="body">
	<form method="post">
		<fieldset>
			<legend>Please Login</legend>
			<p>
				<label for="username">Username</label><br />
				<input type="text" name="username" id="username" value="{$smarty.post.username}">
			</p>

			<p>
				<label for="password">Password</label><br />
				<input type="password" name="password" id="password" value="{$smarty.post.password}">
			</p>
		</fieldset>
		<p><input type="submit" value="Continue &rarr;"></p>
	</form>
	

</div>
