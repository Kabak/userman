<!-- BEGIN: MAIN -->
			{FILE "{PHP.cfg.themes_dir}/{PHP.cfg.defaulttheme}/warnings.tpl"}
<!-- BEGIN: CREATE -->
		<div class="block">
			<h2 class="users">{UM_TITLE}</h2>

			<form action="{UM_USERS_CREATE_SEND}" method="post" name="um_useredit" enctype="multipart/form-data">
				<input type="hidden" name="id" value="{UM_USERS_CREATE_ID}" />
                        <table class="cells">

                            <tr>
                                    	<td class="centerall">{PHP.L.Username}:{UM_NAME}</td>
                                        <td class="centerall">{PHP.L.Email}:{UM_USERS_CREATE_EMAIL}</td>
					<td align="right"><p>{PHP.L.users_newpass}:{UM_USERS_DEFAULT_PASS}</p>
					<p>{PHP.L.users_confirmpass}:{UM_USERS_PASSWORDREPEAT}<p class="medium">{UM_USERS_HELPPASS}</p>
					</td>
			    </tr>
		<tr>
                                        <td >{PHP.L.Maingroup}:<br />&nbsp;{PHP.out.img_down}<br />{UM_USERS_CREATE_GROUPS}</td>
		<!-- IF {UM_USERS_CREATE_SIGNATURE} -->			
					<td>{PHP.L.Signature}:<br />{UM_USERS_CREATE_SIGNATURE}</td>
                                        <td></td>
		<!-- ENDIF -->
		</tr>					
				<tr>
				    <td> <button type="submit" class="button special">{UM_CREATE_USER}</button></td>
				    <td>
				    <a href="{UM_YOURPROFILE}" class="button special">{UM_YOURPROFILE_TEXT}</a>
				    <a href="{UM_SITECONFIG}" class="button special">{UM_SITECONFIG_TEXT}</a>
				    </td>
				    <td>
				    <a href="{UM_USER_RIGHTS}" class="button special">{UM_USER_RIGHTS_TEXT}</a>
				    <a href="{UM_EXTRA_FIELDS}" class="button special">{UM_EXTRA_FIELDS_TEXT}</a>
				    </td>
				</tr>

			</table>
			<h2 class="users">{UM_LIST_TITLE}</h2>                                
                        <table class="cells">                                
				<tr>
					<td class="coltop" class="width5">{UM_TOP_PM}</td>
					<td class="coltop" class="width5">{UM_TOP_ACCESS}</td>
					<td class="coltop" class="width20">{UM_TOP_NAME}</td>
					<td class="coltop" class="width20">{PHP.L.Email}</td>
					<td class="coltop" class="width20">{UM_TOP_GRPTITLE}</td>
					<td class="coltop" class="width15">{UM_TOP_GRPLEVEL}</td>
					<td class="coltop" class="width15">{UM_TOP_COUNTRY}</td>
					<td class="coltop" class="width25">{UM_TOP_REGDATE}</td>
					<td class="coltop" class="width25">{UM_TOP_DELETE}</td>
				</tr>
<!-- BEGIN: UM_ROW -->
				<tr>
					<td class="centerall">{UM_ROW_PM}</td>
					<td class="centerall">{UM_ROW_ACCESS}</td>
					<td class="centerall">{UM_ROW_NAME}&nbsp</td>
					<td class="centerall">{UM_ROW_EMAIL}</td>
					<td class="centerall">{UM_ROW_MAINGRP}</td>
					<td class="centerall">{UM_ROW_MAINGRPSTARS}</td>
					<td class="centerall">{UM_ROW_COUNTRYFLAG} {UM_ROW_COUNTRY}</td>
					<td class="centerall">{UM_ROW_REGDATE}</td>
					<td class="centerall">{UM_ROW_DELETE}</td>
				</tr>
<!-- END: UM_ROW -->
			</table>
			</form>
	
		</div>
		<div class="block">
			<h2 class="prefs">{PHP.L.Filters}</h2>
			<form action="{UM_TOP_FILTER_ACTION}" method="post">
				{UM_TOP_FILTERS_COUNTRY}
				{UM_TOP_FILTERS_MAINGROUP}
				{UM_TOP_FILTERS_GROUP}
				{UM_TOP_FILTERS_SEARCH}<br />
				<button type="submit" class="button special">{PHP.L.Submit}</button>
			</form>
		</div>
		<p class="paging"><span>{PHP.L.users_usersperpage}: {UM_TOP_MAXPERPAGE}</span><span>{PHP.L.users_usersinthissection}: {UM_TOP_TOTALUSERS}</span>{UM_TOP_PAGEPREV}{UM_TOP_PAGNAV}{UM_TOP_PAGENEXT}</p>
<!-- END: CREATE -->
<!-- BEGIN: UM_EDIT -->

		<div class="block">
			<h2 class="users">{UM_EDIT_TITLE}</h2>

			<form action="{UM_EDIT_SEND}" method="post" name="um_edit" enctype="multipart/form-data">
				<input type="hidden" name="id" value="{UM_EDIT_ID}" />
				<table class="cells">
					<tr>
						<td class="width30">{PHP.L.users_id}:</td>
						<td class="width70">#{UM_EDIT_ID}</td>
					</tr>
					<tr>
						<td>{PHP.L.Username}:</td>
						<td>{UM_EDIT_NAME}</td>
					</tr>
					<tr>
						<td>{PHP.L.Groupsmembership}:</td>
						<td>{PHP.L.Maingroup}:<br />&nbsp;{PHP.out.img_down}<br />{UM_EDIT_GROUPS}</td>
					</tr>
					<tr>
						<td>{PHP.L.Country}:</td>
						<td>{UM_EDIT_COUNTRY}</td>
					</tr>
					<tr>
						<td>{UM_EDIT_LOCATION_TITLE}:</td>
						<td>{UM_EDIT_LOCATION}</td>
					</tr>					
					<tr>
						<td>{PHP.L.Timezone}:</td>
						<td>{UM_EDIT_TIMEZONE}</td>
					</tr>
					<tr>
						<td>{PHP.L.Theme}:</td>
						<td>{UM_EDIT_THEME}</td>
					</tr>
					<tr>
						<td>{PHP.L.Language}:</td>
						<td>{UM_EDIT_LANG}</td>
					</tr>
					<!-- IF {UM_EDIT_AVATAR} -->
					<tr>
						<td>{PHP.L.Avatar}:</td>
						<td>{UM_EDIT_AVATAR}</td>
					</tr>
					<!-- ENDIF -->
					<!-- IF {UM_EDIT_SIGNATURE} -->
					<tr>
						<td>{PHP.L.Signature}:</td>
						<td>{UM_EDIT_SIGNATURE}</td>
					</tr>
					<!-- ENDIF -->
					<!-- IF {UM_EDIT_PHOTO} -->
					<tr>
						<td>{PHP.L.Photo}:</td>
						<td>{UM_EDIT_PHOTO}</td>
					</tr>
					<!-- ENDIF -->
					<tr>
						<td>{PHP.L.users_newpass}:</td>
						<td>
							{UM_EDIT_NEWPASS}
							<p class="small">{PHP.L.users_newpasshint1}</p>
						</td>
					</tr>
					<tr>
						<td>{PHP.L.Email}:</td>
						<td>{UM_EDIT_EMAIL}</td>
					</tr>
					<tr>
						<td>{PHP.L.users_hideemail}:</td>
						<td>{UM_EDIT_HIDEEMAIL}</td>
					</tr>
<!-- IF {PHP.cot_modules.pm} -->
					<tr>
						<td>{PHP.L.users_pmnotify}:</td>
						<td>{UM_EDIT_PMNOTIFY}<br />{PHP.themelang.usersedit.PMnotifyhint}</td>
					</tr>
<!-- ENDIF -->
					<tr>
						<td>{PHP.L.Birthdate}:</td>
						<td>{UM_EDIT_BIRTHDATE}</td>
					</tr>
					<tr>
						<td>{PHP.L.Gender}:</td>
						<td>{UM_EDIT_GENDER}</td>
					</tr>
					<tr>
						<td>{PHP.L.Signature}:</td>
						<td>{UM_EDIT_TEXT}</td>
					</tr>
					<tr>
						<td>{PHP.L.Registered}:</td>
						<td>{UM_EDIT_REGDATE}</td>
					</tr>
					<tr>
						<td>{PHP.L.Lastlogged}:</td>
						<td>{UM_EDIT_LASTLOG}</td>
					</tr>
					<tr>
						<td>{PHP.L.users_lastip}:</td>
						<td>{UM_EDIT_LASTIP}</td>
					</tr>
					<tr>
						<td>{PHP.L.users_logcounter}:</td>
						<td>{UM_EDIT_LOGCOUNT}</td>
					</tr>
					<tr>
						<td>{PHP.L.users_deleteuser}:</td>
						<td>{UM_EDIT_DELETE}</td>
					</tr>
					<tr>
						<td colspan="2" class="valid"><button type="submit">{PHP.L.Update}</button>
						<a href="{UM_EDIT_ACCESS}" class="button special large">{UM_EDIT_ACCESS_TEXT}</a>
						</td>
					</tr>
					<tr>
					<td colspan="2" class="valid"><a href="{UM_EDIT_GOBACK}" class="button special large">{UM_EDIT_GOBACK_TEXT}</a></td>
					</tr>
				</table>
			</form>
		</div>

<!-- END: UM_EDIT -->	
<!-- BEGIN: UM_PROFILE -->
		<div class="block">
			<h2 class="users">{UM_PROFILE_TITLE}</h2>

			<form action="{UM_PROFILE_FORM_SEND}" method="post" enctype="multipart/form-data" name="profile">
				<input type="hidden" name="userid" value="{UM_PROFILE_ID}" />
				<table class="cells">
					<tr>
						<td class="width30">{PHP.L.Username}:</td>
						<td class="width70">{UM_PROFILE_NAME}</td>
					</tr>
					<tr>
						<td>{PHP.L.Groupsmembership}:</td>
						<td>
							<div id="usergrouplist">
								{UM_PROFILE_GROUPS}
							</div>
						</td>
					</tr>
					<tr>
						<td>{PHP.L.Registered}:</td>
						<td>{UM_PROFILE_REGDATE}</td>
					</tr>
<!-- BEGIN: USERS_PROFILE_EMAILCHANGE -->
					<tr>
						<td>{PHP.L.Email}:</td>
						<td id="emailtd">
							<div class="width50 floatleft">
								{PHP.L.Email}:<br />{UM_PROFILE_EMAIL}
							</div>
<!-- BEGIN: USERS_PROFILE_EMAILPROTECTION -->
							<script type="text/javascript">
								//<![CDATA[
								$(document).ready(function(){
									$("#emailnotes").hide();
									$("#emailtd").click(function(){$("#emailnotes").slideDown();});
								});
								//]]>
							</script>
							<div>
								{PHP.themelang.usersprofile.Emailpassword}:<br />{UM_PROFILE_EMAILPASS}
							</div>
							<div class="small" id="emailnotes">{PHP.themelang.usersprofile.Emailnotes}</div>
<!-- END: USERS_PROFILE_EMAILPROTECTION -->
						</td>
					</tr>
<!-- END: USERS_PROFILE_EMAILCHANGE -->
					<tr>
						<td>{PHP.L.users_hideemail}:</td>
						<td>{UM_PROFILE_HIDEEMAIL}</td>
					</tr>
<!-- IF {PHP.cot_modules.pm} -->
					<tr>
						<td>{PHP.L.users_pmnotify}:</td>
						<td>
							{UM_PROFILE_PMNOTIFY}
							<p class="small">{PHP.L.users_pmnotifyhint}</p>
						</td>
					</tr>
<!-- ENDIF -->
					<tr>
						<td>{PHP.L.Theme}:</td>
						<td>{UM_PROFILE_THEME}</td>
					</tr>
					<tr>
						<td>{PHP.L.Language}:</td>
						<td>{UM_PROFILE_LANG}</td>
					</tr>
					<tr class="hidden">
						<td>{PHP.L.Country}:</td>
						<td>{UM_PROFILE_COUNTRY}</td>
					</tr>
					<tr>
						<td>{UM_PROFILE_LOCATION_TITLE}:</td>
						<td>{UM_PROFILE_LOCATION}</td>
					</tr>					
					<tr>
						<td>{PHP.L.Timezone}:</td>
						<td>{UM_PROFILE_TIMEZONE}</td>
					</tr>
					<tr>
						<td>{PHP.L.Birthdate}:</td>
						<td>{UM_PROFILE_BIRTHDATE}
						</td>
					</tr>
					<tr>
						<td>{PHP.L.Gender}:</td>
						<td>{UM_PROFILE_GENDER}</td>
					</tr>
					<!-- IF {UM_PROFILE_AVATAR} -->
					<tr>
						<td>{PHP.L.Avatar}:</td>
						<td>{UM_PROFILE_AVATAR}</td>
					</tr>
					<!-- ENDIF -->
					<!-- IF {UM_PROFILE_PHOTO} -->
					<tr>
						<td>{PHP.L.Photo}:</td>
						<td>{UM_PROFILE_PHOTO}</td>
					</tr>
					<!-- ENDIF -->
					<tr>
						<td>{PHP.L.Signature}:</td>
						<td>{UM_PROFILE_TEXT}</td>
					</tr>
					<tr>
						<td>
							{PHP.L.users_newpass}:
							<p class="small">{PHP.L.users_newpasshint1}</p>
						</td>
						<td>
							{UM_PROFILE_OLDPASS}
							<p class="small">{PHP.L.users_oldpasshint}</p>
							{UM_PROFILE_NEWPASS1} {UM_PROFILE_NEWPASS2}
							<p class="small">{PHP.L.users_newpasshint2}</p>
						</td>
					</tr>
					<tr>
						<td colspan="2" class="valid"><button type="submit">{PHP.L.Update}</button></td>
					</tr>
               				<tr>
					<td colspan="2" class="valid"><a href="{UM_PROFILE_GOBACK}" class="button special large">{UM_PROFILE_GOBACK_TEXT}</a></td>
					</tr>
	
				</table>
			</form>
		</div>
<!-- END: UM_PROFILE -->

<!-- BEGIN: UM_TIME_ACCESS -->  

		<div class="block">
			<h2 class="users">{UM_TIME_ACCESS_TITLE}</h2>

			<form action="{UM_TIME_ACCESS_SEND}" method="post" name="um_useredit" enctype="multipart/form-data">
				<input type="hidden" name="id" value="{UM_ACCESS_ID}" />
                        <table class="cells">

                            <tr>
					<td>{PHP.L.users_id}:</td>
					<td>#{UM_ACCESS_ID}</td>
			    </tr>
                            <tr>
                   	<td>{PHP.L.Username}:</td>
					<td>{UM_ACCESS_NAME}</td>
			    </tr>
                            <tr>
					<td>{PHP.L.Email}:</td>
					<td>{UM_ACCESS_EMAIL}</td>
			    </tr>
			    <tr>
               		<td>{PHP.L.Maingroup}:</td><td><br />&nbsp;{PHP.out.img_down}<br />
					{UM_ACCESS_GROUPS}</td>
			    </tr>
			    <tr>
					<td>{UM_ACCESS_START_TEXT}</td>
					<td>{UM_ACCESS_START}</td>
			    </tr>
			    <tr>
					<td>{UM_ACCESS_START_REASON_TEXT}</td>
					<td>{UM_ACCESS_START_REASON}</td>
			    </tr>
			    <tr>
					<td>{UM_ACCESS_STOP_TEXT}</td>
					<td>{UM_ACCESS_STOP}</td>
			    </tr>
			    <tr>
					<td>{UM_ACCESS_STOP_REASON_TEXT}</td>
					<td>{UM_ACCESS_STOP_REASON}</td>
			    </tr>
			    <tr>
					<td>{PHP.L.Lastlogged}:</td>
					<td>{UM_ACCESS_LASTDATE}</td>
			    </tr>
			    <tr>
					<td>{UM_ACCESS_ACTIVE_TEXT}</td>
					<td>{UM_ACCESS_ACTIVE}</td>
			    </tr>
			    <tr>
				    <td colspan="2" class="valid"> <button type="submit">{PHP.L.Update}</button></td>
			    </tr>
			    <tr>
				    <td colspan="2" class="valid"><a href="{UM_ACCESS_GOBACK_UMTOP}" class="button special large">{UM_ACCESS_GOBACK_UMTOP_TEXT}</a>
					<a href="{UM_ACCESS_GOBACK}" class="button special large">{UM_ACCESS_GOBACK_TEXT}</a>
				    </td>
			    </tr>

			</table>
			</form>
	
		</div>

<!-- END: UM_TIME_ACCESS -->  

<!-- END: MAIN -->          