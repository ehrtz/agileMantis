<?php
# This file is part of agileMantis.
#
# Developed by:
# gadiv GmbH
# Bövingen 148
# 53804 Much
# Germany
#
# Email: agilemantis@gadiv.de
#
# Copyright (C) 2012-2014 gadiv GmbH
#
# agileMantis is free software: you can redistribute it and/or modify
# it under the terms of the GNU Lesser General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Lesser General Public License for more details.
#
# You should have received a copy of the GNU Lesser General Public License
# along with agileMantis. If not, see <http://www.gnu.org/licenses/>.



if( $_POST['submit'] == plugin_lang_get( 'button_back' ) ) {
	header( $agilemantis_au->forwardReturnToPage( 'agileuser.php' ) );
}
if( $_POST['action'] == 'addUser' ) {

	$f_username = gpc_get_string( 'username' );
	$f_realname = gpc_get_string( 'realname', '' );
	$f_password = gpc_get_string( 'password', '' );
	$f_password_verify = gpc_get_string( 'password_verify', '' );
	$f_email = gpc_get_string( 'email', '' );
	$f_protected = false;
	$f_enabled = true;

	if( $_POST['administrator'] == 1 ) {
		$f_access_level = 70;
	} elseif( $_POST['developer'] == 1 ) {
		$f_access_level = 55;
	} else {
		$f_access_level = 25;
	}

	$t_data = array(
		'query' => array(),
		'payload' => array(
			'username' => $f_username,
			'email' => $f_email,
			'access_level' => array( 'id' => $f_access_level ),
			'real_name' => $f_realname,
			'password' => $f_password,
			'protected' => $f_protected,
			'enabled' => $f_enabled
		)
	);

	$t_command = new UserCreateCommand( $t_data );
	$t_result = $t_command->execute();

	# set language back to user language
	lang_pop();

	$t_user_id = $t_result['id'];

	$agilemantis_au->setAgileMantisUserRights(
				$t_user_id, $_POST['participant'], $_POST['developer'], $_POST['administrator'] );

	header( $agilemantis_au->forwardReturnToPage( 'agileuser.php' ) );
} else {
	layout_page_header( plugin_lang_get( 'manage_user_add_new_user' ) );

	layout_page_begin( 'info.php' );

	print_manage_menu( 'manage_plugin_page.php' );
}
?>

<?php if(user_get_name(auth_get_current_user_id()) == 'administrator'){?>
<br>
<div align="center">
	<form method="post" action="<?php echo plugin_page("add_user.php")?>"
		method="post">
		<input type="hidden" name="action" value="addUser">
		<div class="table-container">
			<table class="width50" cellspacing="1">
				<tr>
					<td class="form-title" colspan="2">
		<?php echo lang_get( 'create_new_account_title' ) ?>
	</td>
				</tr>
				<tr <?php echo helper_alternate_class() ?>>
					<td class="category" width="25%">
		<?php echo lang_get( 'username' ) ?>
	</td>
					<td width="75%"><input type="text" name="username" size="32"
						maxlength="<?php echo DB_FIELD_SIZE_USERNAME;?>" /></td>
				</tr>
<?php
	if ( !$t_ldap || config_get( 'use_ldap_realname' ) == OFF ) {
?>
<tr <?php echo helper_alternate_class() ?>>
					<td class="category">
		<?php echo lang_get( 'realname' ) ?>
	</td>
					<td><input type="text" name="realname" size="32"
						maxlength="<?php echo DB_FIELD_SIZE_REALNAME;?>" /></td>
				</tr>
<?php
	}

	if ( !$t_ldap || config_get( 'use_ldap_email' ) == OFF ) {
?>
<tr <?php echo helper_alternate_class() ?>>
					<td class="category">
		<?php echo lang_get( 'email' ) ?>
	</td>
					<td>
		<?php print_email_input( 'email', '' ) ?>
	</td>
				</tr>
<?php
	}
?>
<tr <?php echo helper_alternate_class() ?>>
					<td class="category">
		<?php echo lang_get( 'password' ) ?>
	</td>
					<td><input type="password" name="password" size="32"
						maxlength="<?php echo auth_get_password_max_size();?>" /></td>
				</tr>
				<tr <?php echo helper_alternate_class() ?>>
					<td class="category">
		<?php echo lang_get( 'verify_password' ) ?>
	</td>
					<td><input type="password" name="password_verify" size="32"
						maxlength="<?php echo auth_get_password_max_size();?>" /></td>
				</tr>
				<tr <?php echo helper_alternate_class() ?>>
					<td class="category">
		<?php echo plugin_lang_get( 'manage_user_participant' )?>
	</td>
					<td><input type="checkbox" name="participant" value="1"></td>
				</tr>
				<tr <?php echo helper_alternate_class() ?>>
					<td class="category">
		<?php echo plugin_lang_get( 'manage_user_developer' )?>
	</td>
					<td><input type="checkbox" name="developer" value="1"></td>
				</tr>
				<tr <?php echo helper_alternate_class() ?>>
					<td class="category">
		<?php echo plugin_lang_get( 'manage_user_administrator' )?>
	</td>
					<td><input type="checkbox" name="administrator" value="1"></td>
				</tr>
				<tr>
					<td class="center" colspan="2"><input type="submit" class="button"
						value="<?php echo lang_get( 'create_user_button' ) ?>" /> <input
						type="submit" class="button" name="submit"
						value="<?php echo plugin_lang_get( 'button_back' ) ?>" /></td>
				</tr>
			</table>
		</div>
	</form>
</div>
<?php } else {
	echo '<br><center><span class="message_error">'.
			plugin_lang_get( 'info_error_921001' ).'</span></center>';
	}?>
<?php
layout_page_end();
