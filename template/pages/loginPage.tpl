<h1>{lang node='system.page.login.title'}</h1>

{if $user.isGuest}

	<p>
		{lang node='system.page.login.introduction'}
	</p>

	<fieldset class="float-left" style="width: 45%;">

		<legend>{lang node='system.page.login.login.title'}</legend>

		<form method="post">
			<table>
				<tr>
					<td>
						<label for="username">{lang node="system.page.login.username"}:</label>
					</td>
					<td>
						<input type="text" required="required" name="username" id="username" />
					</td>
				</tr>
				<tr>
					<td>
						<label for="password">{lang node="system.page.login.password"}:</label>
					</td>
					<td>
						<input type="password" required="required" name="password" id="password" />
					</td>
				</tr>
				<tr>
					<td>
						<input type="submit" name="login" id="login" value="{lang node="system.page.login.login.login"}" />
					</td>
				</tr>
			</table>
		</form>

	</fieldset>
	<fieldset class="float-right" style="width: 45%;">

		<legend>{lang node='system.page.login.signUp.title'}</legend>
		<form method="post">
			<table>
				<tr>
					<td>
						<label for="signUpUsername">{lang node="system.page.login.username"}:</label>
					</td>
					<td>
						<input type="text" required="required" name="signUpUsername" id="signUpUsername" pattern="{$loginPage.usernamePattern}" />
					</td>
				</tr>
				<tr>
					<td>
						<label for="mail">{lang node="system.page.login.mail"}:</label>
					</td>
					<td>
						<input type="text" required="required" name="mail" id="mail" pattern="{$loginPage.mailPattern}" />
					</td>
				</tr>
				<tr>
					<td>
						<label for="password1">{lang node="system.page.login.passwordTwice"}:</label>
					</td>
					<td>
						<input type="password" required="required" name="password1" id="password1" /><br />
						<input type="password" required="required" name="password2" id="password2" />
					</td>
				</tr>
				<tr>
					<td>
						<input type="submit" name="signUpSubmit" id="signUpSubmit" value="{lang node="system.page.login.signUp.signUp"}" />
					</td>
				</tr>
			</table>
		</form>

	</fieldset>
	<br class="clear" />

{else}
	<p>
		{lang node="system.page.login.welcomeText" userVars=["userName" => $user.name]}
	</p>

	<fieldset class="float-left" style="width: 45%;">
		<legend>{lang node="system.page.login.changeMail.title"}</legend>

		<p class="description">
			{lang node="system.page.login.changeMail.description"}
		</p>

		<hr />

		<form method="post">

			<table>
				<tr>
					<td>
						<label for="changeMail">{lang node="system.page.login.mail"}:</label>
					</td>
					<td>
						<input type="text" required="required" name="changeMail" id="changeMail" pattern="{$loginPage.mailPattern}" value="{$loginPage.changeMail|default:$user.mail}" />
					</td>
				</tr>
				<tr>
					<td>
						<label for="changeMailPassword">{lang node="system.page.login.password"}:</label>
					</td>
					<td>
						<input type="password" required="required" name="changeMailPassword" id="changeMailPassword" />
					</td>
				</tr>
				<tr>
					<td>
						<input type="submit" name="changeMailSubmit" id="changeMailSubmit" value="{lang node="system.page.login.changeMail.change"}" />
					</td>
				</tr>
			</table>

		</form>

	</fieldset>

	<fieldset class="float-right" style="width: 45%;">

		<legend>{lang node="system.page.login.changePassword.title"}</legend>

		<p class="description">
			{lang node="system.page.login.changePassword.description"}
		</p>

		<hr />

		<form method="post">

			<table>
				<tr>
					<td>
						<label for="changePassword1">{lang node="system.page.login.passwordTwice"}:</label>
					</td>
					<td>
						<input type="password" required="required" name="changePassword1" id="changePassword1" /><br />
						<input type="password" required="required" name="changePassword2" id="changePassword2" />
					</td>
				</tr>
				<tr>
					<td>
						<input type="submit" name="changePasswordSubmit" id="changePasswordSubmit" value="{lang node="system.page.login.changePassword.change"}" />
					</td>
				</tr>
			</table>

		</form>

	</fieldset>

	<br class="clear" />

	<fieldset class="float-left" style="width: 45%;">

		<legend>{lang node="system.page.login.avatar.title"}</legend>

		<p class="description">

			<img src="http://gravatar.com/avatar/{$user.mail|trim|strtolower|md5}.png?s=150" alt="avatar" class="float-left" id="avatar" />

			{lang node="system.page.login.avatar.description"}

			<p>
				<a href="http://gravatar.com/emails/">{lang node="system.page.login.avatar.avatarLink"}</a>
			</p>

			<div class="clear"></div>

		</p>


	</fieldset>

	<fieldset class="float-right" style="width: 45%;">

		<legend>{lang node="system.page.login.chooseLanguage.title"}</legend>

		<p class="description">
			{lang node="system.page.login.chooseLanguage.description"}
		</p>

		<hr />

		<form method="post">

			<table>
				<tr>
					<td>
						<label for="chooseLanguage">{lang node="system.page.login.language"}:</label>
					</td>
					<td>
						<select name="chooseLanguage" id="chooseLanguage">
							{foreach $loginPage.availableLanguages as $curLanguage}
								<option value="{$curLanguage.id}" title="{$curLanguage.description}"{if $curLanguage.id == $language.id} selected="selected"{/if}>{$curLanguage.title}</option>
							{/foreach}
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<input type="submit" name="chooseLanguageSubmit" id="chooseLanguageSubmit" value="{lang node="system.page.login.chooseLanguage.change"}" />
					</td>
				</tr>
			</table>



		</form>

	</fieldset>

	<br class="clear" />

{/if}
