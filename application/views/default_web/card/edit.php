<div class="container">
	<div class="row">

		<form method="post" action="/card/save" class="form-horizontal">
			<fieldset>
				<legend>Basic Information</legend>

				<div class="control-group">
					<label class="control-label">Upload Photo</label>
					<div class="controls">
						<div class="btn-group" id="title" data-toggle="buttons-radio">
							<?php
								foreach($title_options AS $title_val => $title_label) {
									if ($title_val == 0) continue;
									echo '<button type="button" class="btn" name="title" value="',$title_val,'"';
									if ($title == $title_val) echo ' data-toggle="button"';
									echo '>',$title_label,'</button>';
								}
							?>
						</div>
						<script>
							$(document).ready(function() {
								$('#title').button();
							});
						</script>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="first_name"><?=$first_name_label?></label>
					<div class="controls">
						<?=form_input('first_name', $first_name, 'id="first_name"')?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="middle_name"><?=$middle_name_label?></label>
					<div class="controls">
						<?=form_input('middle_name', $middle_name, 'id="middle_name"')?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="last_name"><?=$last_name_label?></label>
					<div class="controls">
						<?=form_input('last_name', $last_name, 'id="last_name"')?>
					</div>
				</div>

				<br />

				<div class="control-group">
					<label class="control-label" for="organization_name"><?=$organization_name_label?></label>
					<div class="controls">
						<?=form_input('organization_name', $organization_name, 'id="organization_name"')?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="organization_title"><?=$organization_title_label?></label>
					<div class="controls">
						<?=form_input('organization_title', $organization_title, 'id="organization_title"')?>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="organization_number"><?=$organization_number_label?></label>
					<div class="controls">
						<?=form_input('organization_number', $organization_number, 'id="organization_number"')?>
					</div>
				</div>

				<br />

				<?php
					$x = 0;
					foreach($addon_email AS $e) {
						echo '<div class="control-group">';
							echo '<label class="control-label" for="email_',$e['id'],'">',$e['email_label'],'</label>';
							echo '<div class="controls">';
								echo form_hidden('addon_email['.$x.'][id]', $e['id']);
								echo form_input('addon_email['.$x.'][email]', $e['email'], 'id="email_'.$e['id'].'"');
							echo '</div>';
						echo '</div>';
						$x++;
					}
					echo $x;
				?>


				<br />
				<div class="control-group">
					<label class="control-label"></label>
					<div class="controls">
						<button type="submit" class="btn btn-primary">Save</button> or <a href="/card/view/<?=$id?>">cancel</a>
					</div>
				</div>

			</fieldset>
		</form>

	</div>
</div>