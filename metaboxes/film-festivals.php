<div class="film_control">
	<datalist id="awards">
		<option>Official Selection</option>
		<option>Audience Award</option>
	</datalist>
	<table>
		<tr>
			<th scope="col">Festival Name</th>
			<th scope="col">Festival Site</th>
			<th scope="col">Screening Date</th>
			<th scope="col">Screening Location</th>
			<th scope="col">Award</th>
			<th scope="col">Actions</th>
		</tr>
		<?php while($mb->have_fields_and_multi('festival')): ?>
		<?php $mb->the_group_open('tr'); ?>

		<?php $mb->the_field('name'); ?>
		<td><input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" /></td>
	
		<?php $mb->the_field('site'); ?>
		<td><input type="url" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" /></td>

		<?php $mb->the_field('screening_date'); ?>
		<td><input type="date" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" /></td>

		<?php $mb->the_field('screening_location'); ?>
		<td><input type="text" placeholder="Cannes, France" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" /></td>

		<?php $mb->the_field('award'); ?>
		<td><input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" list="awards" /></td>

		<td><a href="#" class="dodelete button" title="Remove" aria-label="Remove">&times;</a></td>

		<?php $mb->the_group_close(); ?>
		<?php endwhile; ?>
	</table>
	<a href="#" class="docopy-festival button" title="Add" aria-label="Add">+</a>
</div>